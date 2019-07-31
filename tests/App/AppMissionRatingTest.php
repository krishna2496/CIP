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
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        DB::setDefaultConnection('mysql');
     
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $params = [
                'mission_id' => $missionId,
                'rating' => rand(1, 5)
            ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('app/mission/rating', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        App\Models\MissionRating::where(['user_id' => $user->user_id, 'mission_id' => $missionId])->delete();
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
        DB::setDefaultConnection('tenant');
        $missionRatingData = App\Models\MissionRating::get()->random();
        DB::setDefaultConnection('mysql');

        $params = [
                'mission_id' => $missionRatingData->mission_id,
                'rating' => 0.2
            ];

        $token = Helpers::getJwtToken($missionRatingData->user_id);
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
        DB::setDefaultConnection('tenant');
        $missionRatingData = App\Models\MissionRating::get()->random();
        DB::setDefaultConnection('mysql');

        $params = [
                'mission_id' => $missionRatingData->mission_id,
                'rating' => 5.5
            ];

        $token = Helpers::getJwtToken($missionRatingData->user_id);
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
    }
}
