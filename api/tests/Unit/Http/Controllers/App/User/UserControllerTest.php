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
use App\Models\UserFilter;
use App\Events\User\UserActivityLogEvent;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

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

    public function testUpdateSuccess()
    {
        $mergeData = [
            'avatar' => null,
            'expiry' => null
        ];
        $exceptData = [
            'language_id' => 1,
            'avatar' => null,
            'expiry' => null
        ];
        $data = [
            'password' => 'Qwerty1234',
            'language_id' => 1,
            'avatar' => null,
            'expiry' => null,
            'skills' => [['skill_id' => 1]]
        ];

        $symfonyRequest = $this->mock(SymfonyRequest::class);
        $symfonyRequest->user_id = 1;
        $symfonyRequest->email = 'testuser@email.com';

        $request = $this->mock(Request::class);
        $request
            ->shouldReceive('header')
            ->shouldReceive('all')
            ->andReturn($data)
            ->shouldReceive('merge')
            ->andReturn(array_merge($data, $mergeData))
            ->shouldReceive('except')
            ->andReturn($exceptData)
            ->shouldReceive('replace')
            ->andReturn($exceptData);
        $request->auth = $symfonyRequest;

        $userRepository = $this->mock(UserRepository::class);
        $userCustomFieldRepository = $this->mock(UserCustomFieldRepository::class);
        $userFilterRepository = $this->mock(UserFilterRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $userService = $this->mock(UserService::class);

        $user = new User();
        $user->setAttribute('pseudonymize_at', null);
        $user->setAttribute('user_id', 1);
        $user->setAttribute('is_profile_complete', 1);

        $userService
            ->shouldReceive('validateFields')
            ->once()
            ->with($request->all(), 1, false)
            ->andReturn(true);

        $languageHelper
            ->shouldReceive('validateLanguageId')
            ->once()
            ->with($request)
            ->andReturn(true);

        $userFilterRepository
            ->shouldReceive('saveFilter')
            ->once()
            ->with($request)
            ->andReturn(new UserFilter);

        $userService
            ->shouldReceive('update')
            ->once()
            ->with($request->all(), 1)
            ->andReturn($user);

        $userService
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($user);

        $userRepository
            ->shouldReceive('checkProfileCompleteStatus')
            ->once()
            ->with(1, $request)
            ->andReturn($user);

        $userService
            ->shouldReceive('updateSkill')
            ->once()
            ->with($request->all(), 1)
            ->andReturn(true);

        $helpers
            ->shouldReceive('syncUserData')
            ->once()
            ->with($request, $user)
            ->andReturn(true);

        $responseHelper
            ->shouldReceive('success')
            ->once()
            ->with(
                Response::HTTP_OK,
                trans('messages.success.MESSAGE_USER_UPDATED'),
                ['user_id' => 1, 'is_profile_complete' => 1]
            );

        $this->expectsEvents(UserActivityLogEvent::class);

        $controller = $this->getController(
            $userRepository,
            $userCustomFieldRepository,
            null,
            $userFilterRepository,
            $responseHelper,
            $languageHelper,
            $helpers,
            null,
            null,
            $userService
        );

        $response = $controller->update($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    private function getController(
        UserRepository $userRepository = null,
        UserCustomFieldRepository $userCustomFieldRepository = null,
        CityRepository $cityRepository = null,
        UserFilterRepository $userFilterRepository = null,
        ResponseHelper $responseHelper = null,
        LanguageHelper $languageHelper = null,
        Helpers $helpers = null,
        S3Helper $s3Helper = null,
        TenantOptionRepository $tenantOptionRepository = null,
        UserService $userService = null
    ) {
        $userRepository = $userRepository ?? $this->mock(UserRepository::class);
        $userCustomFieldRepository = $userCustomFieldRepository ?? $this->mock(UserCustomFieldRepository::class);
        $cityRepository = $cityRepository ?? $this->mock(CityRepository::class);
        $userFilterRepository = $userFilterRepository ?? $this->mock(UserFilterRepository::class);
        $responseHelper = $responseHelper ?? $this->mock(ResponseHelper::class);
        $languageHelper = $languageHelper ?? $this->mock(LanguageHelper::class);
        $helpers = $helpers ?? $this->mock(Helpers::class);
        $s3Helper = $s3Helper ?? $this->mock(S3Helper::class);
        $tenantOptionRepository = $tenantOptionRepository ?? $this->mock(TenantOptionRepository::class);
        $userService = $userService ??$this->mock(UserService::class);

        return new UserController(
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
