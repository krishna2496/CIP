<?php

namespace Tests\Unit\Http\Controllers\App\User;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Admin\User\UserController;
use App\Repositories\User\UserRepository;
use App\Services\TimesheetService;
use App\Services\UserService;
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

        $service = $this->getController(
            null,
            $responseHelper,
            null,
            $userService,
            null, null,
            $request
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
        Request $request
    ) {

        $userRepository = $userRepository ?? $this->mock(UserRepository::class);
        $responseHelper = $responseHelper ?? $this->mock(ResponseHelper::class);
        $languageHelper = $languageHelper ?? $this->mock(LanguageHelper::class);
        $userService = $userService ?? $this->mock(UserService::class);
        $timesheetService = $timesheetService ?? $this->mock(TimesheetService::class);
        $helpers = $helpers ?? $this->mock(Helpers::class);

        return new UserController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            $timesheetService,
            $helpers,
            $request
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