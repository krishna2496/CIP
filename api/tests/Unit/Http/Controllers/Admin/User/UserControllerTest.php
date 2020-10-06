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
use App\Repositories\Timezone\TimezoneRepository;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use TestCase;

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
        $timezoneRepository = $this->mock(TimezoneRepository::class);

        $service = $this->getController(
            null,
            $responseHelper,
            null,
            $userService,
            null,
            null,
            $request,
            $notificationRepository,
            $timezoneRepository
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
        $timezoneRepository = $this->mock(TimezoneRepository::class);

        $service = $this->getController(
            null,
            $responseHelper,
            null,
            $userService,
            null,
            null,
            $request,
            $notificationRepository,
            $timezoneRepository
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
         $timezoneRepository = $this->mock(TimezoneRepository::class);

        return new UserController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            $timesheetService,
            $helpers,
            $request,
            $notificationRepository,
            $timezoneRepository
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
