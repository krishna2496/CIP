<?php
namespace App\Http\Controllers\App\Auth;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\TenantOption;
use App\Repositories\City\CityRepository;
use App\Repositories\Country\CountryRepository;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Repositories\Timezone\TimezoneRepository;
use App\Repositories\User\UserRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\User;
use Bschmitt\Amqp\Amqp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Settings;
use App\Exceptions\SamlException;

class SamlController extends Controller
{
    private $helpers;
    private $userRepository;
    private $tenantOptionRepository;
    private $languageHelper;
    private $timezoneRepository;
    private $countryRepository;
    private $cityRepository;
    private $availability;

    public function __construct(
        Helpers $helpers,
        UserRepository $userRepository,
        TenantOptionRepository $tenantOptionRepository,
        LanguageHelper $languageHelper,
        TimezoneRepository $timezoneRepository,
        CountryRepository $countryRepository,
        CityRepository $cityRepository,
        Availability $availability
    ) {
        $this->helpers = $helpers;
        $this->userRepository = $userRepository;
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->languageHelper = $languageHelper;
        $this->timezoneRepository = $timezoneRepository;
        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
        $this->availability = $availability;
    }

    public function sso(Request $request)
    {
        $settings = $this->getIdentityProviderSettings();
        if (!isset($settings['idp_id'])) {
            SamlException::throw('ERROR_INVALID_SAML_IDENTITY_PROVIDER');
        } elseif ($settings['idp_id'] !== $request->query('t')) {
            SamlException::throw('ERROR_INVALID_SAML_ACCESS');
        }

        $auth = new Auth($this->getSamlSettings($settings));
        return $auth->login();
    }

    public function acs(Request $request, User $user)
    {
        $validationErrors = [];
        $settings = $this->getIdentityProviderSettings();
        if (!isset($settings['idp_id'])) {
            SamlException::throw('ERROR_INVALID_SAML_IDENTITY_PROVIDER');
        } elseif ($settings['idp_id'] !== $request->query('t')) {
            SamlException::throw('ERROR_INVALID_SAML_ACCESS');
        }

        $auth = new Auth($this->getSamlSettings($settings));
        $auth->processResponse();
        if (!$auth->isAuthenticated()) {
            $auth->redirectTo('http'.($request->secure() ? 's' : '').'://'.$settings['frontend_fqdn']);
        }

        $email = $auth->getNameId();
        $userDetail = $user->where('email', $email)->first();
        $attributes = [];
        $userData = [];

        $optimyAppMapping = [
            'availability' => 'availability_id',
            'timezone' => 'timezone_id',
            'language' => 'language_id',
            'postal_city' => 'city_id',
            'postal_country' => 'country_id',
            'profile' => 'profile_text',
            'department' => 'department',
            'linkedin' => 'linked_in_url',
            'volunteer' => 'why_i_volunteer',
            'position' => 'title'
        ];

        $validProperties = [
            'first_name',
            'last_name',
            'email',
            'availability_id',
            'timezone_id',
            'language_id',
            'city_id',
            'country_id',
            'profile_text',
            'employee_id',
            'department',
            'linked_in_url',
            'why_i_volunteer',
            'title'
        ];

        foreach ($auth->getAttributes() as $key => $attribute) {
            if (empty($attribute[0])) {
                continue;
            }
            $attributes[$key] = count($attribute) > 1 ?
                $attribute :
                $attribute[0];
        }

        foreach ($settings['mappings'] as $mapping) {
            $name = $mapping['name'];

            if (!isset($attributes[$mapping['value']])) {
                continue;
            }

            if (!in_array($name, $validProperties)
                && array_key_exists($name, $optimyAppMapping)
            ) {
                $name = $optimyAppMapping[$name];
            }

            if (!in_array($name, $validProperties)) {
                continue;
            }

            $value = $attributes[$mapping['value']];

            if ($name === 'language_id') {
                $language = $this->languageHelper->getTenantLanguageByCode($request, $value);
                if (!$language) {
                    $validationErrors[] = 'Language';
                }
                $value = $language->language_id;
            }

            if ($name === 'timezone_id') {
                $timezone = $this->timezoneRepository->getTenantTimezoneByCode($value);
                if (!$timezone) {
                     $validationErrors[] = 'Timezone';
                }
                $value = $timezone->timezone_id;
            }

            if ($name === 'country_id') {
                $country = $this->countryRepository->getCountryByCode($value);
                if (!$country) {
                     $validationErrors[] = 'Country';
                }
                $value = $country->country_id;
            }

            $userData[$name] = $value;
        }

        if ($validationErrors) {
            $auth->redirectTo(
                'http'.($request->secure() ? 's' : '').'://'.$settings['frontend_fqdn'].'/saml-error',
                ['errors' => implode(',', $validationErrors)]
            );
        }

        if (isset($userData['city_id']) ) {
            $city = $this->cityRepository->searchCity(
                $userData['city_id'],
                (int)$userData['language_id'],
                (int)$userData['country_id']
            );
            unset($userData['city_id']);
            if ($city) {
                $userData['city_id'] = $city->city_id;
            }
        }

        if (isset($userData['availability_id'])) {
            $availabilityId = $userData['availability_id'];
            unset($userData['availability_id']);
            $availabilityList = $this->availability->getAvailability();
            foreach($availabilityList as $availability) {
                $index = array_search(
                    $availabilityId,
                    array_column($availability['translations'], 'title')
                );

                if ($index === false) {
                    continue;
                }

                $userData['availability_id'] = $availability->availability_id;
                break;
            }
        }

        $userDetail = $userDetail ?
            $this->userRepository->update($userData, $userDetail->user_id) :
            $this->userRepository->store($userData);

        $this->syncContact($userDetail, $settings);

        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        $token = $this->helpers->getJwtToken(
            $userDetail->user_id,
            $tenantName,
            true,
            10
        );

        $auth->redirectTo(
            'http'.($request->secure() ? 's' : '').'://'.$settings['frontend_fqdn'].'/auth/sso',
            ['token' => $token]
        );
    }

    public function slo(Request $request)
    {
        $settings = $this->getIdentityProviderSettings();
        if ($settings['idp_id'] !== $request->query('t')) {
            throw new SamlException(
                trans('messages.custom_error_message.ERROR_INVALID_SAML_IDENTITY_PROVIDER'),
                config('constants.error_codes.ERROR_INVALID_SAML_IDENTITY_PROVIDER')
            );
        }

        $auth = new Auth($this->getSamlSettings($settings));
        $sloUrl = $auth->logout(null, [], null, null, true);

        $auth->redirectTo(
            'http'.($request->secure() ? 's' : '').'://'.$settings['frontend_fqdn'].'/auth/slo',
            ['slo' => $sloUrl]
        );
    }

    public function metadata(Request $request, Response $response)
    {
        $settings = $this->getIdentityProviderSettings();
        if ($settings['idp_id'] !== $request->query('t')) {
            throw new SamlException(
                trans('messages.custom_error_message.ERROR_INVALID_SAML_IDENTITY_PROVIDER'),
                config('constants.error_codes.ERROR_INVALID_SAML_IDENTITY_PROVIDER')
            );
        }

        $samlSettings = new Settings($this->getSamlSettings($settings));
        $metadata = $samlSettings->getSPMetadata();
        $errors = $samlSettings->validateMetadata($metadata);
        return $response->header('Content-Type', 'text/xml')
            ->setContent($metadata);
    }

    private function getSamlSettings(array $settings)
    {
        return [
            'debug' => env('APP_DEBUG'),
            'strict' => $settings['strict'],
            'security' => $settings['security'],
            'idp' => $settings['idp'],
            'sp' => [
                'entityId' => route('saml.metadata', ['t' => $settings['idp_id']]),
                'singleSignOnService' => [
                    'url' => route('saml.sso', ['t' => $settings['idp_id']])
                ],
                'singleLogoutService' => [
                    'url' => route('saml.slo', ['t' => $settings['idp_id']])
                ],
                'assertionConsumerService' => [
                    'url' => route('saml.acs', ['t' => $settings['idp_id']])
                ],
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:emailAddress',
                'x509cert' => Storage::disk('local')->get('samlCertificate/optimy.cer'),
                'privateKey' => Storage::disk('local')->get('samlCertificate/optimy.pem'),
            ]
        ];
    }

    private function getIdentityProviderSettings()
    {
        $optionSetting = $this->tenantOptionRepository
            ->getOptionValueFromOptionName(TenantOption::SAML_SETTINGS);

        return $optionSetting->getOptionValueAttribute(
            $optionSetting->option_value
        );
    }

    private function syncContact($userDetail, $settings)
    {
        $city = $this->cityRepository->getCityData($userDetail->city_id);
        $country = $this->countryRepository->getCountryData($userDetail->country_id);
        $language = $this->languageHelper->getLanguage($userDetail->language_id);

        $cityLanguages = collect($city['languages']);
        $postalCity = null;
        if ($cityLanguages->count()) {
            $postalCity = $cityLanguages->where('language_id', $userDetail->country_id)
                ->first();
            if ($postalCity) {
                $postalCity = $postalCity['name'];
            } else {
                $postalCity = $cityLanguages->first()['name'];
            }
        }

        $payload = json_encode([
            'ci_platform_instance_id' => $settings['ci_platform_instance_id'],
            'contact_info' => [
                'email' => $userDetail->email,
                'position' => $userDetail->title,
                'first_name' => $userDetail->first_name,
                'last_name' => $userDetail->last_name,
                'postal_city' => $postalCity,
                'postal_country' => $country['ISO'],
                'preferred_language' => $language->code,
                'department' => $userDetail->department
            ]
        ]);

        (new Amqp)->publish(
            'ciContacts',
            $payload,
            ['queue' => 'ciContacts']
        );
    }
}
