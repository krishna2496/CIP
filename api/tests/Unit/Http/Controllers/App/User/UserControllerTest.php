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
use Illuminate\Http\Response;
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

        $userController = new UserController(
            $userRepository,
            $userCustomFieldRepository,
            $cityRepository,
            $userFilterRepository,
            $responseHelper,
            $languageHelper,
            $helpers,
            $s3Helper,
            $tenantOptionRepository
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

        $userController = new UserController(
            $userRepository,
            $userCustomFieldRepository,
            $cityRepository,
            $userFilterRepository,
            $responseHelper,
            $languageHelper,
            $helpers,
            $s3Helper,
            $tenantOptionRepository
        );

        $this->withoutEvents();

        $response = $userController->createPassword($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testIndexGetAllUsers()
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userCustomFieldRepository = $this->createMock(UserCustomFieldRepository::class);
        $responseHelper = $this->createMock(ResponseHelper::class);
        $languageHelper = $this->createMock(LanguageHelper::class);
        $helpers = $this->createMock(Helpers::class);
        $s3helper = $this->createMock(S3Helper::class);
        $userFilterRepository = $this->createMock(UserFilterRepository::class);
        $tenantOptionRepository = $this->createMock(TenantOptionRepository::class);
        $cityRepository = $this->createMock(CityRepository::class);

        $controller = new UserController(
            $userRepository,
            $userCustomFieldRepository,
            $cityRepository,
            $userFilterRepository,
            $responseHelper,
            $languageHelper,
            $helpers,
            $s3helper,
            $tenantOptionRepository
        );

        $request = new Request();
        $request->auth = new \stdClass();
        $request->auth->user_id = 1;

        $user1 = new User(['first_name' => 'Jeannot', 'last_name' => 'Lapin', 'avatar' => 'default.png']);
        $user1->user_id = 1;
        $user2 = new User(['first_name' => 'Daisy', 'last_name' => 'Duck', 'avatar' => 'default.png']);
        $user2->user_id = 2;
        $user3 = new User(['first_name' => 'Mickey', 'last_name' => 'Mouse', 'avatar' => 'default.png']);
        $user3->user_id = 3;

        $userCollection = new Collection([
            $user1,
            $user2,
            $user3
        ]);

        $userRepository
            ->expects($this->once())
            ->method('listUsers')
            ->willReturn($userCollection);

        $userRepository
            ->expects($this->never())
            ->method('searchUsers');

        $helpers
            ->expects($this->once())
            ->method('getSubDomainFromRequest')
            ->with($request)
            ->willReturn('ci-api');

        $responseHelper
            ->expects($this->once())
            ->method('success')
            ->with(
                Response::HTTP_OK,
                trans('messages.success.MESSAGE_USER_LISTING'),
                [
                    $user1,
                    $user2,
                    $user3
                ]
            )
            ->willReturn(new JsonResponse());

        $result = $controller->index($request);

        // testing the mock to avoid warning in phpunit
        $this->assertInstanceOf(JsonResponse::class, $result);
    }

    public function testIndexSearchUsers()
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userCustomFieldRepository = $this->createMock(UserCustomFieldRepository::class);
        $responseHelper = $this->createMock(ResponseHelper::class);
        $languageHelper = $this->createMock(LanguageHelper::class);
        $helpers = $this->createMock(Helpers::class);
        $s3helper = $this->createMock(S3Helper::class);
        $userFilterRepository = $this->createMock(UserFilterRepository::class);
        $tenantOptionRepository = $this->createMock(TenantOptionRepository::class);
        $cityRepository = $this->createMock(CityRepository::class);

        $controller = new UserController(
            $userRepository,
            $userCustomFieldRepository,
            $cityRepository,
            $userFilterRepository,
            $responseHelper,
            $languageHelper,
            $helpers,
            $s3helper,
            $tenantOptionRepository
        );

        $request = new Request(['search' => 'jeannot']);
        $request->auth = new \stdClass();
        $request->auth->user_id = 1;

        $user1 = new User(['first_name' => 'Jeannot', 'last_name' => 'Lapin', 'avatar' => 'default.png']);
        $user1->user_id = 1;

        $userCollection = new Collection([
            $user1
        ]);

        $userRepository
            ->expects($this->never())
            ->method('listUsers');

        $userRepository
            ->expects($this->once())
            ->method('searchUsers')
            ->willReturn($userCollection);

        $helpers
            ->expects($this->once())
            ->method('getSubDomainFromRequest')
            ->with($request)
            ->willReturn('ci-api');

        $responseHelper
            ->expects($this->once())
            ->method('success')
            ->with(
                Response::HTTP_OK,
                trans('messages.success.MESSAGE_USER_LISTING'),
                [
                    $user1
                ]
            )
            ->willReturn(new JsonResponse());

        $result = $controller->index($request);

        // testing the mock to avoid warning in phpunit
        $this->assertInstanceOf(JsonResponse::class, $result);
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
