<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Mission;

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
        $this->get(route('app.missions'), ['token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjEsImlhdCI6MTU2MjA3NTY5NywiZXhwIjoxNTYyMDc5Mjk3LCJmcWRuIjoidGF0dmEifQ.rb6kfvopbWcQku0ZDBGYHhxvChJPvK03cMn7AC2Hxfs'])
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
                        "already_volunteered",
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
        $this->get(route('app.missions'), ['token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjEsImlhdCI6MTU2MjA3NTY5NywiZXhwIjoxNTYyMDc5Mjk3LCJmcWRuIjoidGF0dmEifQ.rb6kfvopbWcQku0ZDBGYHhxvChJPvK03cMn7AC2Hxfs'])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
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
}
