<?php
namespace App\Http\Controllers\App\Auth;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\TenantOption;
use App\Repositories\City\CityRepository;
use App\Repositories\Country\CountryRepository;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Repositories\Timezone\TimezoneRepository;
use App\Repositories\User\UserRepository;
use App\Exceptions\SamlException;
use App\Traits\RestExceptionHandlerTrait;
use App\User;
use Bschmitt\Amqp\Amqp;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Settings;
use Hybridauth\Hybridauth;

class GoogleAuthController extends Controller
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
        ResponseHelper $responseHelper,
        UserRepository $userRepository,
        TenantOptionRepository $tenantOptionRepository,
        LanguageHelper $languageHelper,
        TimezoneRepository $timezoneRepository,
        CountryRepository $countryRepository,
        CityRepository $cityRepository,
        Availability $availability
    ) {
        $this->helpers = $helpers;
        $this->responseHelper = $responseHelper;
        $this->userRepository = $userRepository;
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->languageHelper = $languageHelper;
        $this->timezoneRepository = $timezoneRepository;
        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
        $this->availability = $availability;
    }

    public function login(Request $request, User $user)
    {

        $config = [
            'callback' => route('google.authentication'), 
            'providers' => [ 
                "Google" => [
                    "enabled" => true,
                    "keys" => [
                        "id" => env('GOOGLE_AUTH_ID'),
                        "secret" => env('GOOGLE_AUTH_SECRET'),
                    ]
                ]
            ]
        ];

        $hybridauth = new Hybridauth($config);
        $adapter = $hybridauth->authenticate('Google');
        $isConnected = $adapter->isConnected();
        $frontendFqdn = $request->input('domain');

        if (!$isConnected) {
            $redirectUrl = sprintf(
                'http%s://%s/saml-error?errors=%s',
                ($request->secure() ? 's' : ''),
                $frontendFqdn,
                implode(',',['GOOGLE_AUTH_ERROR']),
            );
            return redirect($redirectUrl);
        }

        $userProfile = $adapter->getUserProfile();
        $userEmail = $userProfile->email;

        $isOptimyDomain = preg_match('/\.optimy\.com$/i', $userEmail) || preg_match('/@optimy\.com$/i', $userEmail);

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL) || !$isOptimyDomain) {
            $redirectUrl = sprintf(
                'http%s://%s/saml-error?errors=%s',
                ($request->secure() ? 's' : ''),
                $frontendFqdn,
                implode(',',['INVALID_OPTIMY_EMAIL']),
            );
            return redirect($redirectUrl);
        }

        $isAdminUser = $this->helpers->isAdminUser($userEmail);

        if (!$isAdminUser) {
            $redirectUrl = sprintf(
                'http%s://%s/saml-error?errors=%s',
                ($request->secure() ? 's' : ''),
                $frontendFqdn,
                implode(',',['GOOGLE_AUTH_UNAUTHORIZE']),
            );
            return redirect($redirectUrl);
        }

        $userDetail = $user->where('email', $userEmail)->first();

        $userData = [
            'first_name' => $userProfile->firstName,
            'last_name' => $userProfile->lastName,
            'email' => $userProfile->email,
        ];

        if (!$userDetail) {
            $language = $this->languageHelper->getLanguages()->first();
            $userData['language_id'] = $language->id;
        }

        $userDetail = $userDetail ?
            $this->userRepository->update($userData, $userDetail->user_id) :
            $this->userRepository->store($userData);

        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        $token = $this->helpers->getJwtToken(
            $userDetail->user_id,
            $tenantName,
            true,
            60
        );

        $redirectUrl = sprintf(
            'http%s://%s/auth/sso?token=%s',
            ($request->secure() ? 's' : ''),
            $frontendFqdn,
            $token,
        );

        return redirect($redirectUrl);
    }
}
