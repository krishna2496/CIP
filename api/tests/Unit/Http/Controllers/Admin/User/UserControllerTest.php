<?php

namespace Tests\Unit\Http\Controllers\App\User;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Admin\User\UserController;
use App\Repositories\User\UserRepository;
use App\Services\TimesheetService;
use App\Services\UserService;
use App\Repositories\Notification\NotificationRepository;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use TestCase;
use Validator;
use App\Events\User\UserActivityLogEvent;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class UserControllerTest extends TestCase
{

    /**
    * @testdox Test contentStatistics
    *
    * @return void
    */
    public function testContentStatistics()
    {
        $request = new Request();
        $methodResponse = [
            'messages_count' => 5,
            'comments_count' => 3,
            'stories_count' => 2,
            'stories_views_count' => 3,
            'stories_invites_count' => 1,
            'organization_count' => 2
        ];

        $user = new User();
        $user->setAttribute('user_id', 1);

        $userService = $this->mock(UserService::class);
        $userService
            ->shouldReceive('findById')
            ->once()
            ->with($user->user_id)
            ->andReturn($user);

        $userService
            ->shouldReceive('statistics')
            ->once()
            ->with($user, $request->all())
            ->andReturn($methodResponse);

        $jsonResponse = new JsonResponse(
            $methodResponse,
            Response::HTTP_OK
        );

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('success')
            ->once()
            ->with(
                Response::HTTP_OK,
                trans('messages.success.MESSAGE_TENANT_USER_CONTENT_STATISTICS_SUCCESS'),
                $methodResponse
            )
            ->andReturn($jsonResponse);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $service = $this->getController(
            null,
            $responseHelper,
            null,
            $userService,
            null,
            null,
            $request,
            $notificationRepository
        );

        $response = $service->contentStatistics($request, $user->user_id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals([
            'messages_count' => 5,
            'comments_count' => 3,
            'stories_count' => 2,
            'stories_views_count' => 3,
            'stories_invites_count' => 1,
            'organization_count' => 2
        ], json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test volunteerSummary
    *
    * @return void
    */
    public function testVolunteerSummary()
    {
        $request = new Request();
        $methodResponse = [
            'last_volunteer' => '2020-05-01',
            'last_login' => '2020-05-15 10:10:31',
            'open_volunteer_request' => 1,
            'mission' => 1,
            'favourite_mission' => 1
        ];

        $user = new User();
        $user->setAttribute('user_id', 1);

        $userService = $this->mock(UserService::class);
        $userService
            ->shouldReceive('findById')
            ->once()
            ->with($user->user_id)
            ->andReturn($user);

        $userService
            ->shouldReceive('volunteerSummary')
            ->once()
            ->with($user, $request->all())
            ->andReturn($methodResponse);

        $jsonResponse = new JsonResponse(
            $methodResponse,
            Response::HTTP_OK
        );

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('success')
            ->once()
            ->with(
                Response::HTTP_OK,
                trans('messages.success.MESSAGE_TENANT_USER_VOLUNTEER_SUMMARY_SUCCESS'),
                $methodResponse
            )
            ->andReturn($jsonResponse);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $service = $this->getController(
            null,
            $responseHelper,
            null,
            $userService,
            null,
            null,
            $request,
            $notificationRepository
        );

        $response = $service->volunteerSummary($request, $user->user_id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals([
            'last_volunteer' => '2020-05-01',
            'last_login' => '2020-05-15 10:10:31',
            'open_volunteer_request' => 1,
            'mission' => 1,
            'favourite_mission' => 1
        ], json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test store user with required fields
    *
    * @return void
    */
    public function testStoreUserWithRequiredFieldsOnlySuccess()
    {
        $this->expectsEvents(UserActivityLogEvent::class);
        $symfonyRequest = $this->mock(SymfonyRequest::class);
        $symfonyRequest->shouldReceive('remove')
            ->andReturn(true);
        $request = $this->mock(Request::class);
        $request->shouldReceive('header')
            ->shouldReceive('all')
            ->andReturn([
                'email' => 'testemail@yahoo.com',
                'password' => 'Passw0rd'
            ])
            ->shouldReceive('toArray')
            ->andReturn([
                'email' => 'testemail@yahoo.com',
                'password' => 'Passw0rd'
            ]);
        $request->request = $symfonyRequest;
        $request->skills = null;
        $request->language_id = null;
        $request->expiry = null;

        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $validator->shouldReceive('fails')
            ->andReturn(false);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $user = new User();
        $user->setAttribute('user_id', 1);

        $methodResponse = [
            'user_id' => 1
        ];

        $userService = $this->mock(UserService::class);
        $userService
            ->shouldReceive('store')
            ->once()
            ->with($request->toArray())
            ->andReturn($user);

        $jsonResponse = new JsonResponse(
            $methodResponse,
            Response::HTTP_CREATED
        );

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('success')
            ->once()
            ->with(
                Response::HTTP_CREATED,
                trans('messages.success.MESSAGE_USER_CREATED'),
                $methodResponse
            )
            ->andReturn($jsonResponse);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $languageHelper = $this->mock(LanguageHelper::class);
        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('checkProfileCompleteStatus')
            ->once()
            ->with(1, $request)
            ->andReturn($user);

        $service = $this->getController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            null,
            null,
            $request,
            $notificationRepository
        );

        $response = $service->store($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            $methodResponse,
            json_decode($response->getContent(), true)
        );
    }

    /**
    * @testdox Test store user
    *
    * @return void
    */
    public function testStoreUserSuccess()
    {
        $this->expectsEvents(UserActivityLogEvent::class);
        $symfonyRequest = $this->mock(SymfonyRequest::class);
        $symfonyRequest->shouldReceive('remove')
            ->andReturn(true);
        $request = $this->mock(Request::class);
        $request->shouldReceive('header')
            ->shouldReceive('all')
            ->andReturn([
                'email' => 'testemail@yahoo.com',
                'password' => 'Passw0rd',
                'first_name' => 'Test',
                'last_name' => 'Email'
            ])
            ->shouldReceive('toArray')
            ->andReturn([
                'email' => 'testemail@yahoo.com',
                'password' => 'Passw0rd',
                'first_name' => 'Test',
                'last_name' => 'Email'
            ]);
        $request->request = $symfonyRequest;
        $request->skills = [['skill_id' => 1]];
        $request->language_id = 1;
        $request->expiry = null;

        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $validator->shouldReceive('fails')
            ->andReturn(false);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $user = new User();
        $user->setAttribute('user_id', 1);

        $methodResponse = [
            'user_id' => 1
        ];

        $userService = $this->mock(UserService::class);
        $userService
            ->shouldReceive('store')
            ->once()
            ->with($request->toArray())
            ->andReturn($user);

        $jsonResponse = new JsonResponse(
            $methodResponse,
            Response::HTTP_CREATED
        );

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('success')
            ->once()
            ->with(
                Response::HTTP_CREATED,
                trans('messages.success.MESSAGE_USER_CREATED'),
                $methodResponse
            )
            ->andReturn($jsonResponse);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $languageHelper = $this->mock(LanguageHelper::class);
        $languageHelper
            ->shouldReceive('validateLanguageId')
            ->once()
            ->with($request)
            ->andReturn(true);

        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('checkProfileCompleteStatus')
            ->once()
            ->with(1, $request)
            ->andReturn($user);

        $userRepository
            ->shouldReceive('linkSkill')
            ->once()
            ->with($request->toArray(), 1)
            ->andReturn([true]);

        $service = $this->getController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            null,
            null,
            $request,
            $notificationRepository
        );

        $response = $service->store($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            $methodResponse,
            json_decode($response->getContent(), true)
        );
    }

    /**
    * @testdox Test store user invalid password
    *
    * @return void
    */
    public function testStoreUserPasswordInvalid()
    {
        $symfonyRequest = $this->mock(SymfonyRequest::class);
        $symfonyRequest->shouldReceive('remove')
            ->andReturn(true);
        $request = $this->mock(Request::class);
        $request->shouldReceive('header')
            ->shouldReceive('all')
            ->andReturn([
                'email' => 'testemail@yahoo.com',
                'password' => 'password',
                'first_name' => 'Test',
                'last_name' => 'Email'
            ])
            ->shouldReceive('toArray')
            ->andReturn([
                'email' => 'testemail@yahoo.com',
                'password' => 'password',
                'first_name' => 'Test',
                'last_name' => 'Email'
            ]);
        $request->request = $symfonyRequest;
        $request->skills = [['skill_id' => 1]];
        $request->language_id = 1;
        $request->expiry = null;

        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $errors = new Collection([
            trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
        ]);
        $validator->shouldReceive('fails')
            ->andReturn(true)
            ->shouldReceive('errors')
            ->andReturn($errors);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $user = new User();
        $user->setAttribute('user_id', 1);

        $methodResponse = [
            'errors' => [
                'message' => trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
            ]
        ];

        $userService = $this->mock(UserService::class);
        $userService
            ->shouldReceive('store')
            ->never()
            ->with($request->toArray())
            ->andReturn($user);

        $jsonResponse = new JsonResponse(
            $methodResponse,
            Response::HTTP_CREATED
        );

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('error')
            ->once()
            ->with(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
            )
            ->andReturn($jsonResponse);

        $responseHelper
            ->shouldReceive('success')
            ->never()
            ->with(
                Response::HTTP_CREATED,
                trans('messages.success.MESSAGE_USER_CREATED'),
                $methodResponse
            )
            ->andReturn($jsonResponse);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $languageHelper = $this->mock(LanguageHelper::class);
        $languageHelper
            ->shouldReceive('validateLanguageId')
            ->never()
            ->with($request)
            ->andReturn(true);

        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('checkProfileCompleteStatus')
            ->never()
            ->with(1, $request)
            ->andReturn($user);

        $userRepository
            ->shouldReceive('linkSkill')
            ->never()
            ->with($request->toArray(), 1)
            ->andReturn([true]);

        $service = $this->getController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            null,
            null,
            $request,
            $notificationRepository
        );

        $response = $service->store($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            $methodResponse,
            json_decode($response->getContent(), true)
        );
    }

    /**
    * @testdox Test store user invalid password
    *
    * @return void
    */
    public function testStoreUserLanguageIdInvalid()
    {
        $symfonyRequest = $this->mock(SymfonyRequest::class);
        $symfonyRequest->shouldReceive('remove')
            ->andReturn(true);
        $request = $this->mock(Request::class);
        $request->shouldReceive('header')
            ->shouldReceive('all')
            ->andReturn([
                'email' => 'testemail@yahoo.com',
                'password' => 'password',
                'first_name' => 'Test',
                'last_name' => 'Email'
            ])
            ->shouldReceive('toArray')
            ->andReturn([
                'email' => 'testemail@yahoo.com',
                'password' => 'password',
                'first_name' => 'Test',
                'last_name' => 'Email'
            ]);
        $request->request = $symfonyRequest;
        $request->skills = [['skill_id' => 1]];
        $request->language_id = 1;
        $request->expiry = null;

        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $validator->shouldReceive('fails')
            ->andReturn(false);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $user = new User();
        $user->setAttribute('user_id', 1);

        $methodResponse = [
            'errors' => [
                'message' => trans('messages.custom_error_message.ERROR_USER_INVALID_LANGUAGE')
            ]
        ];

        $userService = $this->mock(UserService::class);
        $userService
            ->shouldReceive('store')
            ->never()
            ->with($request->toArray())
            ->andReturn($user);

        $jsonResponse = new JsonResponse(
            $methodResponse,
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('error')
            ->once()
            ->with(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                trans('messages.custom_error_message.ERROR_USER_INVALID_LANGUAGE')
            )
            ->andReturn($jsonResponse);

        $notificationRepository = $this->mock(NotificationRepository::class);

        $languageHelper = $this->mock(LanguageHelper::class);
        $languageHelper
            ->shouldReceive('validateLanguageId')
            ->once()
            ->with($request)
            ->andReturn(false);

        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('checkProfileCompleteStatus')
            ->never()
            ->with(1, $request)
            ->andReturn($user);

        $userRepository
            ->shouldReceive('linkSkill')
            ->never()
            ->with($request->toArray(), 1)
            ->andReturn([true]);

        $service = $this->getController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            null,
            null,
            $request,
            $notificationRepository
        );

        $response = $service->store($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            $methodResponse,
            json_decode($response->getContent(), true)
        );
    }

    /**
    * @testdox Test update user
    *
    * @return void
    */
    public function testUpdateUserSuccess()
    {
        $this->expectsEvents(UserActivityLogEvent::class);
        $symfonyRequest = $this->mock(SymfonyRequest::class);
        $symfonyRequest->shouldReceive('remove')
            ->andReturn(true);
        $request = $this->mock(Request::class);
        $request->shouldReceive('header')
            ->shouldReceive('all')
            ->andReturn([
                'password' => 'Passw0rd'
            ])
            ->shouldReceive('toArray')
            ->andReturn([
                'password' => 'Passw0rd'
            ]);
        $request->request = $symfonyRequest;
        $request->skills = null;
        $request->language_id = null;
        $request->expiry = null;
        $request->status = null;

        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $validator->shouldReceive('fails')
            ->andReturn(false);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $user = new User();
        $user->setAttribute('user_id', 1);
        $user->setAttribute('pseudonymize_at', null);

        $methodResponse = [
            'user_id' => 1
        ];

        $userService = $this->mock(UserService::class);

        $jsonResponse = new JsonResponse(
            $methodResponse,
            Response::HTTP_OK
        );

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('success')
            ->once()
            ->with(
                Response::HTTP_OK,
                trans('messages.success.MESSAGE_USER_UPDATED'),
                $methodResponse
            )
            ->andReturn($jsonResponse);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $helpers
            ->shouldReceive('getSupportedFieldsToPseudonymize')
            ->once()
            ->andReturn([]);
        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($user);

        $userRepository
            ->shouldReceive('update')
            ->once()
            ->with(['password' => 'Passw0rd', 'expiry' => null], 1)
            ->andReturn($user);

        $userRepository
            ->shouldReceive('checkProfileCompleteStatus')
            ->once()
            ->with(1, $request)
            ->andReturn($user);

        $service = $this->getController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            null,
            $helpers,
            $request,
            $notificationRepository
        );

        $response = $service->update($request, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            $methodResponse,
            json_decode($response->getContent(), true)
        );
    }

    /**
    * @testdox Test update user invalid password
    *
    * @return void
    */
    public function testUpdateUserInvalidPassword()
    {
        $symfonyRequest = $this->mock(SymfonyRequest::class);
        $symfonyRequest->shouldReceive('remove')
            ->andReturn(true);
        $request = $this->mock(Request::class);
        $request->shouldReceive('header')
            ->shouldReceive('all')
            ->andReturn([
                'password' => 'password'
            ])
            ->shouldReceive('toArray')
            ->andReturn([
                'password' => 'password'
            ]);
        $request->request = $symfonyRequest;
        $request->skills = null;
        $request->language_id = null;
        $request->expiry = null;
        $request->status = null;

        $errors = new Collection([
            trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
        ]);
        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $validator
            ->shouldReceive('fails')
            ->andReturn(true)
            ->shouldReceive('errors')
            ->andReturn($errors);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $user = new User();
        $user->setAttribute('user_id', 1);
        $user->setAttribute('pseudonymize_at', null);

        $methodResponse = [
            'user_id' => 1
        ];

        $userService = $this->mock(UserService::class);

        $jsonResponse = new JsonResponse(
            $methodResponse,
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('error')
            ->once()
            ->with(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
            )
            ->andReturn($jsonResponse);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $helpers
            ->shouldReceive('getSupportedFieldsToPseudonymize')
            ->once()
            ->andReturn([]);
        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('find')
            ->never()
            ->with(1)
            ->andReturn($user);

        $userRepository
            ->shouldReceive('update')
            ->never()
            ->with(['password' => 'Passw0rd', 'expiry' => null], 1)
            ->andReturn($user);

        $userRepository
            ->shouldReceive('checkProfileCompleteStatus')
            ->never()
            ->with(1, $request)
            ->andReturn($user);

        $service = $this->getController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            null,
            $helpers,
            $request,
            $notificationRepository
        );

        $response = $service->update($request, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(
            $methodResponse,
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Create a new service instance.
     *
     * @param  App\Services\UserService $userService
     *
     * @return void
     */
    private function getController(
        UserRepository $userRepository = null,
        ResponseHelper $responseHelper = null,
        LanguageHelper $languageHelper = null,
        UserService $userService = null,
        TimesheetService $timesheetService = null,
        Helpers $helpers = null,
        Request $request,
        NotificationRepository $notificationRepository
    ) {
        $userRepository = $userRepository ?? $this->mock(UserRepository::class);
        $responseHelper = $responseHelper ?? $this->mock(ResponseHelper::class);
        $languageHelper = $languageHelper ?? $this->mock(LanguageHelper::class);
        $userService = $userService ?? $this->mock(UserService::class);
        $timesheetService = $timesheetService ?? $this->mock(TimesheetService::class);
        $helpers = $helpers ?? $this->mock(Helpers::class);
        $notificationRepository = $notificationRepository ?? $this->mock(NotificationRepository::class);

        return new UserController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            $timesheetService,
            $helpers,
            $request,
            $notificationRepository
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
