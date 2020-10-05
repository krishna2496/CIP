<?php

namespace Tests\Unit\Http\Controllers\App\Auth;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\S3Helper;
use App\Http\Controllers\App\User\UserController;
use App\Models\TenantOption;
use App\Repositories\City\CityRepository;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Repositories\UserCustomField\UserCustomFieldRepository;
use App\Repositories\UserFilter\UserFilterRepository;
use App\Repositories\User\UserRepository;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UserService;
use Mockery;
use TestCase;

class UserControllerTest extends TestCase
{
    const OPTION_NAME_SSO = 'saml_settings';

    public function testInviteUser()
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('toArray')
            ->andReturn([
                'email' => 'test@optimy.com',
                'subject' => 'Notification',
                'body' => 'body',
                'language' => 'en'
            ])
            ->shouldReceive('get')
            ->andReturn('test@optimy.com')
            ->shouldReceive('only');

        $userModel = $this->mock(User::class);
        $userModel->shouldReceive('getAttribute')
            ->shouldReceive('notify')
            ->shouldReceive('setAttribute')
            ->shouldReceive('save');

        $userRepository = $this->mock(UserRepository::class);
        $userRepository->shouldReceive('findUserByEmail')
            ->andReturn($userModel);

        $userCustomFieldRepository = $this->mock(UserCustomFieldRepository::class);
        $cityRepository = $this->mock(CityRepository::class);
        $userFilterRepository = $this->mock(UserFilterRepository::class);

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper->shouldReceive('success');

        $languageHelper = $this->mock(LanguageHelper::class);

        $helpers = $this->mock(Helpers::class);

        $s3Helper = $this->mock(S3Helper::class);

        $samlSettings = new Collection([
            0 => [
                'option_value' => [
                    'saml_access_only' => false
                ]
            ]
        ]);

        $tenantOption = $this->mock(TenantOption::class);
        $tenantOption->shouldReceive('getAttribute');

        $tenantOptionRepository = $this->mock(TenantOptionRepository::class);
        $tenantOptionRepository->shouldReceive('getOptionValue')
            ->with(self::OPTION_NAME_SSO)
            ->once()
            ->andReturn($samlSettings)
            ->shouldReceive('getOptionValueFromOptionName')
            ->andReturn($tenantOption);

        $userService = $this->mock(UserService::class);

        $userController = new UserController(
            $userRepository,
            $userCustomFieldRepository,
            $cityRepository,
            $userFilterRepository,
            $responseHelper,
            $languageHelper,
            $helpers,
            $s3Helper,
            $tenantOptionRepository,
            $userService
        );

        $this->withoutEvents();

        $response = $userController->inviteUser($userModel, $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testCreatePassword()
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('toArray')
            ->andReturn([
                'email' => 'test@optimy.com',
                'password' => 'YwE$#12dW'
            ])
            ->shouldReceive('get')
            ->andReturn('test@optimy.com')
            ->shouldReceive('only');

        $userModel = $this->mock(User::class);
        $userModel->shouldReceive('getAttribute')
            ->shouldReceive('setAttribute')
            ->shouldReceive('save');

        $userRepository = $this->mock(UserRepository::class);
        $userRepository->shouldReceive('findUserByEmail')
            ->andReturn($userModel);

        $userCustomFieldRepository = $this->mock(UserCustomFieldRepository::class);
        $cityRepository = $this->mock(CityRepository::class);
        $userFilterRepository = $this->mock(UserFilterRepository::class);

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper->shouldReceive('success');

        $languageHelper = $this->mock(LanguageHelper::class);

        $helpers = $this->mock(Helpers::class);

        $s3Helper = $this->mock(S3Helper::class);

        $tenantOptionRepository = $this->mock(TenantOptionRepository::class);

        $userService = $this->mock(UserService::class);

        $userController = new UserController(
            $userRepository,
            $userCustomFieldRepository,
            $cityRepository,
            $userFilterRepository,
            $responseHelper,
            $languageHelper,
            $helpers,
            $s3Helper,
            $tenantOptionRepository,
            $userService
        );

        $this->withoutEvents();

        $response = $userController->createPassword($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * Mock an object
     *
     * @param string name
     *
     * @return Mockery
     */
    private function mock($class)
    {
        return Mockery::mock($class);
    }
}
