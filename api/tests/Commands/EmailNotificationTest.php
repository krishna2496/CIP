<?php
use App\Helpers\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class EmailNotificationTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_should_run_email_notification_for_all_tenant()
    {
        \DB::setDefaultConnection('tenant');
        \App\Models\Notification::where('deleted_at', null)->delete();
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        DB::setDefaultConnection('tenant');
        $notificationTypes = App\Models\NotificationType::get()->toArray();
        
        $notificationTypeArray = [];

        foreach ($notificationTypes as $notificationType) {
            $notificationTypeArray[] = ["notification_type_id" => $notificationType['notification_type_id'], "value" => 1];
        }

        $params = [
            "settings" => $notificationTypeArray,
            "user_settings" => [
                [
                "receive_email_notification"=> 1
                ]
            ]
        ];

        // Get setting id from master table
        DB::setDefaultConnection('mysql');
        $emailNotificationInviteColleague = config('constants.tenant_settings.EMAIL_NOTIFICATION_INVITE_COLLEAGUE');
        $settings = DB::select("SELECT * FROM tenant_setting as t WHERE t.key='$emailNotificationInviteColleague'");

        DB::setDefaultConnection('tenant');
        $setting = App\Models\TenantSetting::create(['setting_id' =>$settings[0]->tenant_setting_id]);
        App\Models\TenantActivatedSetting::create(['tenant_setting_id' =>$setting->tenant_setting_id]);

        // Save user notification settings
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $res = $this->post('app/user-notification-settings/update', $params, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);

        // Add skill
        $skillName = str_random(20);
        $params = [
            "skill_name" => $skillName,
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');
        $this->post("entities/skills", $params, ['Authorization' => Helpers::getBasicAuth()]);

        $skill = App\Models\Skill::where("skill_name", $skillName)->orderBy("skill_id", "DESC")->take(1)->get();
        $skillId = $skill[0]->skill_id;

        // Update user and user skills
        $skillsArray[] = ["skill_id" => $skillId];
        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(50),
            'employee_id' => str_random(3),
            'department' => str_random(5),
            'skills' => $skillsArray,
            "city_id" => $cityId,
            "country_id" => $countryDetail->country_id
        ];

        DB::setDefaultConnection('mysql');
        $this->patch('app/user/', $params, ['token' => $token])
        ->seeStatusCode(200);
        
        DB::setDefaultConnection('tenant');
        App\Models\Mission::whereNull("deleted_at")->delete();
        
        // Create goal mission
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => $cityId,
                "country_code" => $countryDetail->ISO
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
                        ],
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ]
                ],
                [
                    "lang" => "fr",
                    "title" => str_random(10),
                    "short_description" => str_random(20),
                    "objective" => str_random(20),
                    "section" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ],
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ]
                ]
            ],
            "media_images" => [[
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "1",
                    "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2022-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(100, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "volunteering_attribute" => [
                "availability_id" => 1,
                "total_seats" => rand(1, 10),
                "is_virtual" => 0
            ],
            "skills" => [
                [
                    "skill_id" => $skillId
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $this->post("missions", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->first();

        // Apply to a mission
        $params = [
            'mission_id' => $mission->mission_id,
            'motivation' => str_random(10),
            'availability_id' => 1
        ];

        DB::setDefaultConnection('mysql');
        $this->post('app/mission/application', $params, ['token' => $token])
        ->seeStatusCode(201);

        $missionApplication = \App\Models\MissionApplication::where('mission_id', $mission->mission_id)->first();
        
        // Update mission application status
        $params = [
            "approval_status" => "AUTOMATICALLY_APPROVED",
        ];

        DB::setDefaultConnection('mysql');
        $this->patch('/missions/'.$mission->mission_id.'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        // Recommend a mission to user
        $toUser = factory(\App\User::class)->make();
        $toUser->setConnection($connection);
        $toUser->receive_email_notification = 1;
        $toUser->save();

        $notification = factory(\App\Models\UserNotification::class)->make();
        $notification->setConnection($connection);
        $notification->user_id = $toUser->user_id;
        $notification->save();

        $params = [
            'mission_id' => $mission->mission_id,
            'to_user_id' => $toUser->user_id
        ];
        DB::setDefaultConnection('mysql');
        $this->post('/app/mission/invite', $params, ['token' => $token])
        ->seeStatusCode(201);

        // Add story
        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        $this->post('app/story', $params, ['token' => $token])
        ->seeStatusCode(201);

        // Submit story for approval
        $story = App\Models\Story::orderBy("story_id", "DESC")->take(1)->first();
        DB::setDefaultConnection('mysql');
        $this->post('app/story/'.$story->story_id.'/submit', [], ['token' => $token])
        ->seeStatusCode(200);
        
        // Update story status
        DB::setDefaultConnection('mysql');
        $params = ["status" => config('constants.story_status.PUBLISHED')];
        $this->patch('stories/'.$story->story_id, $params, ['Authorization' => Helpers::getBasicAuth()])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);

        // Recommend a story to a user
        $notification = factory(\App\Models\UserNotification::class)->make();
        $notification->setConnection($connection);
        $notification->user_id = $toUser->user_id;
        $notification->notification_type_id = 8;
        $notification->save();

        $params = [
            'story_id' => $story->story_id,
            'to_user_id' => $toUser->user_id
        ];
                
        DB::setDefaultConnection('mysql');
        $this->post('/app/story/invite', $params, ['token' => $token])
        ->seeStatusCode(201);

        // Add comment
        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];
        DB::setDefaultConnection('mysql');
        $this->post('/app/mission/comment', $params, ['token' => $token])
        ->seeStatusCode(201);

        // Update comment status
        $comment = App\Models\Comment::where('user_id', $user->user_id)->first();
        $params = [
            "approval_status" => config("constants.comment_approval_status.PUBLISHED"),
        ];
        DB::setDefaultConnection('mysql');
        $this->patch('/missions/'.$mission->mission_id.'/comments/'.$comment->comment_id, $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        DB::setDefaultConnection('tenant');
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();
        
        // Add news
        DB::setDefaultConnection('tenant');
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        // Add message
        $params = [
            "subject" => str_random('50'),
            "message" => str_random('1000'),
            "admin"  => str_random('10'),
            "user_ids" => [
                $user->user_id
            ]
        ];

        DB::setDefaultConnection('mysql');
        // Add message from admin side
        $response = $this->post('message/send', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        // Add timesheet for volunteering hours
        $params = [
            'mission_id' => $mission->mission_id,
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'action' => rand(1, 5),
            'documents[]' =>[]
        ];

        DB::setDefaultConnection('mysql');
        $this->post('app/timesheet', $params, ['token' => $token])
        ->seeStatusCode(201);

        // Add time type mission
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => $cityId,
                "country_code" => $countryDetail->ISO
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
                    "default" => "1",
                    "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "volunteering_attribute" => [
                "availability_id" => 1,
                "total_seats" => rand(1, 10),
                "is_virtual" => 0
            ]
        ];

        DB::setDefaultConnection('mysql');
        $this->post("missions", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);
        
        $timeMissionId = json_decode($this->response->getContent())->data->mission_id;
       
        $params = [
                'mission_id' => $timeMissionId,
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        $this->post('app/mission/application', $params, ['token' => $token])
        ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $timeMissionId,
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

        // Submit timesheet for approval
        $timesheet = App\Models\Timesheet::where("mission_id", $mission->mission_id)->orderBy('timesheet_id', 'DESC')->first();
        $timeMissionTimesheet = App\Models\Timesheet::where("mission_id", $timeMissionId)->orderBy('timesheet_id', 'DESC')->first();
        $params = [
            'timesheet_entries' => [
                [
                    "timesheet_id" => $timesheet->timesheet_id
                ],
                [
                    "timesheet_id" => $timeMissionTimesheet->timesheet_id
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $this->post("app/timesheet/submit", $params, ['token' => $token])
        ->seeStatusCode(200);

        // Update timesheet status
        $params = [
            "status" => config('constants.timesheet_status.AUTOMATICALLY_APPROVED')
        ];
        
        DB::setDefaultConnection('mysql');
        $this->patch('timesheet/'.$timesheet->timesheet_id, $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        // Update timesheet status
        DB::setDefaultConnection('mysql');
        $this->patch('timesheet/'.$timeMissionTimesheet->timesheet_id, $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        // Get Notifications
        DB::setDefaultConnection('mysql');
        $this->get('app/notifications', ['token' => $token])
        ->seeStatusCode(200);
                
        // Get notification of to user
        $token = Helpers::getJwtToken($toUser->user_id, env('DEFAULT_TENANT'));
        DB::setDefaultConnection('mysql');
        $this->call('GET', 'app/notifications', [], [], [], ['HTTP_token' => $token, 'HTTP_X-localization' => 'test']);
        $this->seeStatusCode(200);

        /* -- Call command because for success email notification -- */
        $kernel = $this->app->make(Illuminate\Contracts\Console\Kernel::class);
        $status = $kernel->handle(
            $input = new Symfony\Component\Console\Input\ArrayInput([
                'command' => 'send:email-notification', // put your command name here
            ]),
            $output = new Symfony\Component\Console\Output\BufferedOutput
        );


        $user->delete();
        $toUser->delete();
        $mission->delete();
        $notification->delete();
        $newsCategory->delete();
        App\Models\TenantActivatedSetting::where(['tenant_setting_id' => $setting->tenant_setting_id])->delete();
        App\Models\TenantSetting::where(['setting_id' => $settings[0]->tenant_setting_id])->delete();
       
        $this->assertTrue(true);
    }
}
