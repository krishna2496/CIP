<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Mission;
use App\Models\FavouriteMission;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\DB;

class AppMissionTest extends TestCase
{
    /**
     * @test
     *
     * Get all mission
     *
     * @return void
     */
    public function it_should_return_all_app_missions()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        DB::connection('mysql')->getPdo();
        Config::set('database.default', 'mysql');

        $token = $this->getToken($user->user_id);
        $this->get(route('app.missions'), ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "meta_data" => [
                "filters" => [
                    "search"
                ]
            ],
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * No mission found
     *
     * @return void
     */
    public function it_should_return_no_mission_found()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        DB::connection('mysql')->getPdo();
        Config::set('database.default', 'mysql');
        $token = $this->getToken($user->user_id);
        
        $this->get(route('app.missions'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);        
        $user->delete();
    }

    /**
     * @test
     *
     * Show error invalid credentials
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_token()
    {
        $this->get(route('app.missions'), ['token' => str_random(100)])
        ->seeStatusCode(400);
    }

    /**
     * @test
     *
     * It should validate data for add mission to favourite
     *
     * @return void
     */
    public function it_should_validate_request_for_add_mission_to_favourite()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        DB::connection('mysql')->getPdo();
        Config::set('database.default', 'mysql');

        $params = [
                'mission_id' => rand(1000000, 2000000)
            ];
        $token = $this->getToken($user->user_id);
        $this->post('app/mission/favourite', $params, ['token' => $token])
          ->seeStatusCode(404);
        $user->delete();
    }

    /**
     * @test
     *
     * It should add mission to favourite
     *
     * @return void
     */
    public function it_should_add_or_remove_mission_to_favourite()
    {
       
        DB::setDefaultConnection('tenant');        
        $missionId = App\Models\Mission::get()->random()->mission_id;
        DB::setDefaultConnection('mysql');
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $params = [
                'mission_id' => $missionId
            ];

        $token = $this->getToken($user->user_id);
        $this->post('app/mission/favourite', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        FavouriteMission::where('user_id', $user->user_id)->delete(); 
    }


    /**
     * Create new user and generate jwt token
     *
     * @param int $userId
     * @return string
     */
    public function getToken(int $userId)
    {   
        $payload = [
            'iss' => "lumen-jwt",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'fqdn' => 'tatva'
        ];
        return JWT::encode($payload, env('JWT_SECRET'));
    }
}
