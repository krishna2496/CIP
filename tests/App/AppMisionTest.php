<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Mission;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class AppMissionTest extends TestCase
{
    /**
     * @test
     *
     * Get all mission
     *
     * @return void
     */
    public function it_should_return_all_missions()
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
            "data" => [
                [
                    "mission_id",
                    "theme_id",
                    "city_id",
                    "country_id",
                    "start_date",
                    "end_date",
                    "total_seats",
                    "mission_type",
                    "goal_objective",
                    "application_deadline",
                    "publication_status",
                    "organisation_id",
                    "organisation_name",
                    "user_application_count",
                    "mission_application_count",
                    "default_media_type",
                    "default_media_path",
                    "title",
                    "short_description",
                    "objective",
                    "set_view_detail",
                    "city_name",
                    "mission_theme" => [
                        "mission_theme_id",
                        "theme_name",
                        "translations"
                    ]
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
