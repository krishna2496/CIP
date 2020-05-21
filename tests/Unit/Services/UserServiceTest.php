<?php

namespace Tests\Unit\Services;

use App\Models\MissionApplication;
use App\Models\ActivityLog;
use App\Models\Timesheet;
use App\Models\FavouriteMission;
use App\Repositories\User\UserRepository;
use App\Services\UserService;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use TestCase;

class UserServiceTest extends TestCase
{
    /**
    * @testdox Test findById
    *
    * @return void
    */
    public function testFindById()
    {
        $user = new User();
        $user->setAttribute('user_id', 1);

        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('find')
            ->once()
            ->with($user->user_id)
            ->andReturn($user);

        $service = $this->getService(
            $userRepository
        );

        $response = $service->findById($user->user_id);

        $this->assertEquals($user, $response);

    }

    /**
    * @testdox Test statistics
    *
    * @return void
    */
    public function testStatistics()
    {
        $request = new Request();
        $methodResponse = $this->getMockResponse();
        $methodResponseTwo = $this->getMockResponseTwo();

        $user = new User();
        $user->setAttribute('user_id', 1);

        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('getStatistics')
            ->once()
            ->with($user, $request->all())
            ->andReturn($methodResponse);

        $userRepository
            ->shouldReceive('getOrgCount')
            ->once()
            ->with($user, $request->all())
            ->andReturn($methodResponseTwo);

        $service = $this->getService(
            $userRepository
        );

        $response = $service->statistics($user, $request->all());

        $this->assertEquals([
            'messages_count' => 5,
            'comments_count' => 3,
            'stories_count' => 2,
            'stories_views_count' => 3,
            'stories_invited_users_count' => 1,
            'organization_count' => 2
        ], $response);

    }

    /**
    * @testdox Test volunteer summary
    *
    * @return void
    */
    public function testVolunteersummary()
    {
        $request = new Request();

        $activityLog = new ActivityLog();
        $activityLog->setAttribute('last_login', '2020-05-15 10:10:31');
        $activityLog->setAttribute('last_volunteer', '2020-05-01');
        $activityLog = new Collection([$activityLog]);

        $missionCount = new User();
        $missionCount->setAttribute('open_volunteer_request', 1);
        $missionCount->setAttribute('mission', 1);
        $missionCount = new Collection([$missionCount]);

        $favoriteMission = new FavouriteMission();
        $favoriteMission->setAttribute('favourite_mission', 1);
        $favoriteMission = new Collection([$favoriteMission]);

        $user = new User();
        $user->setAttribute('user_id', 1);

        $userRepository = $this->mock(UserRepository::class);
        $userRepository
            ->shouldReceive('volunteerSummary')
            ->once()
            ->with($user, $request->all())
            ->andReturn($activityLog);

        $userRepository
            ->shouldReceive('getMissionCount')
            ->once()
            ->with($user, $request->all())
            ->andReturn($missionCount);

        $userRepository
            ->shouldReceive('getFavoriteMission')
            ->once()
            ->with($user, $request->all())
            ->andReturn($favoriteMission);

        $service = $this->getService(
            $userRepository
        );
        $response = $service->volunteerSummary($user, $request->all());
        $this->assertEquals([
            'last_volunteer' => '2020-05-01',
            'last_login' => '2020-05-15 10:10:31',
            'open_volunteer_request' => 1,
            'mission' => 1,
            'favourite_mission' => 1
        ], $response);
    }

    private function getMockResponse()
    {
        $user = new User();
        $user->setAttribute('messages_count', 5);
        $user->setAttribute('comments_count', 3);
        $user->setAttribute('stories_count', 2);
        $user->setAttribute('stories_views_count', 3);
        $user->setAttribute('stories_invited_users_count', 1);

        return new Collection([
            $user
        ]);
    }

    private function getMockResponseTwo()
    {
        $mission = new MissionApplication();
        $mission->setAttribute('organization_count', 2);

        return new Collection([
            $mission
        ]);  
    }

    /**
     * Create a new service instance.
     *
     * @param  App\Repositories\Timesheet\UserRepository $userRepository
     * 
     * @return void
     */
    private function getService(
        UserRepository $userRepository
    ) {
        return new UserService(
            $userRepository
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