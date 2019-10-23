<?php
use App\Helpers\Helpers;

class AppDashoardTest extends TestCase
{
    /**
     * @test
     *
     * Get dashboard details
     *
     * @return void
     */
    public function it_should_return_dashboard_details()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/dashboard', ['token' => $token])
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
     * Get dashboard details
     *
     * @return void
     */
    public function it_should_return_dashboard_details_with_chart_details()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
            ],
            "mission_detail" => [[
                    "lang" => "en",
                    "title" => str_random(10),
                    "short_description" => str_random(20),
                    "objective" => str_random(20),
                    "section" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ]
                ]
            ],
            "media_images" => [[
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
        ->seeStatusCode(201);

        $timesheet = App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->first();
        $timesheet->update(['status_id' => config("constants.timesheet_status_id")["APPROVED"]]);

        $this->get('app/dashboard?mission_id='.$mission[0]['mission_id'], ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        \App\Models\Mission::whereNull('deleted_at')->delete();

    }
}
