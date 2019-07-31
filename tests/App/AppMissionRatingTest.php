<?php
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class AppMissionRatingTest extends TestCase
{    
    /**
     * @test
     *
     * It should add mission rating
     *
     * @return void
     */
    public function it_should_add_mission_rating()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
                'mission_id' => $mission->mission_id,
                'rating' => rand(1, 5)
            ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('app/mission/rating', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
}

    /**
     * @test
     *
     * It should update mission rating
     *
     * @return void
     */
    public function it_should_update_mission_rating()
    {
        DB::setDefaultConnection('tenant');
        $missionRatingData = App\Models\MissionRating::get()->random();
        DB::setDefaultConnection('mysql');

        $params = [
                'mission_id' => $missionRatingData->mission_id,
                'rating' => $missionRatingData->rating
            ];

        $token = Helpers::getJwtToken($missionRatingData->user_id);
        $this->post('app/mission/rating', $params, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\MissionRating::where(['user_id' => $missionRatingData->user_id, 'mission_id' => $missionRatingData->mission_id])->delete();
        App\User::where(['user_id' => $missionRatingData->user_id])->delete();
        App\Models\Mission::where(['mission_id' => $missionRatingData->mission_id])->delete();
}

    /**
     * @test
     *
     * It should return error for invalid mission id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_store_rating()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $params = [
                'mission_id' => rand(1000000, 2000000),
                'rating' => rand(1, 5)
            ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('app/mission/rating', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]); 
        $user->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission rating
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_minimum_rating()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
                'mission_id' => $mission->mission_id,
                'rating' => 0.2
            ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('app/mission/rating', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]); 
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission rating
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_maximum_rating()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
                'mission_id' => $mission->mission_id,
                'rating' => 5.5
            ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('app/mission/rating', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]); 
        $user->delete();
        $mission->delete();
    }
}
