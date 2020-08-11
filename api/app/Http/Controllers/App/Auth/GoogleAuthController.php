<?php
namespace App\Http\Controllers\App\Auth;

use App\Exceptions\MaximumUsersReachedException;
use App\Repositories\Timezone\TimezoneRepository;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\User;
use Hybridauth\Hybridauth;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    private $helpers;
    private $user;
    private $userService;

    public function __construct(
        LanguageHelper $languageHelper,
        Helpers $helpers,
        User $user,
        UserService $userService,
        TimezoneRepository $timezoneRepository
    ) {
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->user = $user;
        $this->userService = $userService;
        $this->timezoneRepository = $timezoneRepository;
    }

    // TODO: Refactor!!!
    public function login(Request $request)
    {
        $frontendFqdn = $request->input('domain');
        $tenantId = $request->input('tenant');
        $state = $request->input('state');

        if ($state) {
            $decodedToken = $this->helpers->decodeJwtToken($state);
            $frontendFqdn = $decodedToken->domain;
            $tenantId = $decodedToken->tenant;
            $request->merge(['tenant' => $tenantId]);
        }

        $this->helpers->createConnection($tenantId);
        $state = $this->helpers->encodeJwtToken([
            'tenant' => $tenantId,
            'domain' => $frontendFqdn
        ], 60);

        $config = [
            'callback' => route('google.authentication'),
            'providers' => [
                "Google" => [
                    "enabled" => true,
                    "keys" => [
                        "id" => env('GOOGLE_AUTH_ID'),
                        "secret" => env('GOOGLE_AUTH_SECRET'),
                    ],
                    'authorize_url_parameters' => [
                        'state' => $state,
                    ]
                ]
            ]
        ];

        $hybridauth = new Hybridauth($config);
        $adapter = $hybridauth->authenticate('Google');
        $isConnected = $adapter->isConnected();

        if (!$isConnected) {
            return $this->errorRedirect($request->secure(), $frontendFqdn, 'GOOGLE_AUTH_ERROR');
        }

        $userProfile = $adapter->getUserProfile();

        $userEmail = $userProfile->email;

        $isOptimyDomain = preg_match('/\.optimy\.com$/i', $userEmail) || preg_match('/@optimy\.com$/i', $userEmail);

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL) || !$isOptimyDomain) {
            return $this->errorRedirect($request->secure(), $frontendFqdn, 'INVALID_EMAIL');
        }

        $isAdminUser = $this->helpers->isAdminUser($userEmail);

        if (!$isAdminUser) {
            return $this->errorRedirect($request->secure(), $frontendFqdn, 'GOOGLE_AUTH_UNAUTHORIZE');
        }

        $userDetail = $this->user
            ->where('email', $userEmail)
            ->first();

        $userData = [
            'avatar' => $userProfile->photoURL,
            'first_name' => $userProfile->firstName,
            'last_name' => $userProfile->lastName,
            'email' => $userProfile->email,
            'status' => '1',
        ];

        if (!$userDetail) {
            $language = $this->languageHelper
                ->getTenantLanguagesByTenantId($tenantId)
                ->first();
            $timezone = $this->timezoneRepository
                ->getTenantTimezoneByCode($this->timezoneRepository->getTimezoneList()->first())
                ->first();

            $userData['language_id'] = $language->language_id;
            $userData['timezone_id'] = $timezone->timezone_id;
        }

        try {
            $userDetail = $userDetail ?
                $this->userService->update($userData, $userDetail->user_id) :
                $this->userService->store($userData);
        } catch (MaximumUsersReachedException $e) {
            return $this->errorRedirect($request->secure(), $frontendFqdn, 'MAXIMUM_USERS_REACHED');
        }

        $this->helpers->syncUserData($request, $userDetail);

        $tenantName = $this->helpers->getTenantDomainByTenantId($tenantId);

        $token = $this->helpers->getJwtToken(
            $userDetail->user_id,
            $tenantName,
            true,
            60
        );

        return $this->successRedirect($request->secure(), $frontendFqdn, $token);
    }

    /**
     * @param  bool
     * @param  domain
     * @param  string
     *
     * @return  Laravel\Lumen\Http\Redirector|Illuminate\Http\RedirectResponse
     */
    private function errorRedirect(bool $httpSecure, string $domain, string $errorMessage)
    {
        $url = '{protocol}://{domain}/auth/sso/error?{query}';
        $query = [
            'error' => $errorMessage,
            'source' => 'google',
        ];

        return $this->redirect($url, $httpSecure, $domain, $query);
    }

    /**
     * @param  bool
     * @param  domain
     * @param  string
     *
     * @return  Laravel\Lumen\Http\Redirector|Illuminate\Http\RedirectResponse
     */
    private function successRedirect(bool $httpSecure, string $domain, string $token)
    {
        $url = '{protocol}://{$domain}/auth/sso?{query}';
        $query = [
            'token' => $token,
        ];

        return $this->redirect($url, $httpSecure, $domain, $query);
    }

    /**
     * @param  string
     * @param  bool
     * @param  domain
     * @param  array
     *
     * @return  Laravel\Lumen\Http\Redirector|Illuminate\Http\RedirectResponse
     */
    private function redirect(string $urlPattern, bool $httpSecure, string $domain, array $query): string
    {
        // NOTE: look into using http_build_url (PECL pecl_http >= 0.21.0)

        $parts = [
            '{protocol}' => $httpSecure ? 'https' : 'http',
            '{domain}' => $domain,
            '{query}' => http_build_query($query),
        ];
        $url = strtr($urlPattern, $parts);

        return redirect($url);
    }
}
