<?php
use App\Helpers\Helpers;

class AppNotificationTest extends TestCase
{
    /**
     * @test
     *
     * Get notification settings
     *
     * @return void
     */
    public function it_should_return_notification_settings()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/notification-settings', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Save notification settings
     *
     * @return void
     */
    public function it_should_save_notification_settings()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        DB::setDefaultConnection('tenant');
        $notificationTypeId = App\Models\NotificationType::get()->random()->notification_type_id;

        $params = [
            "settings" => [
                [
                    "notification_type_id" => $notificationTypeId,
                    "value" => 1
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/user-notification-settings/update', $params, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);

        // For update notification settings
        DB::setDefaultConnection('mysql');
        $this->post('app/user-notification-settings/update', $params, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Return error for inalid data on save notification settings
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_data__for_save_notification_settings()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "settings" => [
                [
                    "notification_type_id" => '',
                    "value" => 1
                ]
            ]
        ];
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/user-notification-settings/update', $params, ['token' => $token])
          ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Return error for inalid data on save notification settings
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_id_for_save_notification_settings()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "settings" => [
                [
                    "notification_type_id" => rand(1000000, 5000000),
                    "value" => 1
                ]
            ]
        ];
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/user-notification-settings/update', $params, ['token' => $token])
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Return error for inalid data on save notification settings
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_value_on_save_notification_settings()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        DB::setDefaultConnection('tenant');
        $notificationTypeId = App\Models\NotificationType::get()->random()->notification_type_id;

        $params = [
            "settings" => [
                [
                    "notification_type_id" => $notificationTypeId,
                    "value" => rand(1000, 50000)
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/user-notification-settings/update', $params, ['token' => $token])
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Get notifications
     *
     * @return void
     */
    public function it_should_list_notifications()
    {
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
            "settings" => $notificationTypeArray
        ];

        // Save user notification settings
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/user-notification-settings/update', $params, ['token' => $token])
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
        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);

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
            'skills' => $skillsArray
        ];

        DB::setDefaultConnection('mysql');
        $this->patch('app/user/', $params, ['token' => $token])
        ->seeStatusCode(200);

        // Create mission
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
                    "default" => "1"
                ],
                [
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => ""
                ]
            ],
            "documents" => [[
                    "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf"
                ]
            ],
            "media_videos"=> [[
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2019-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 1000),
            "application_deadline" => "2019-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1,
            "skills" => [
                [
                    "skill_id" => $skillId
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->patch('/missions/'.$mission->mission_id.'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        // Recommend a mission to user
        $toUser = factory(\App\User::class)->make();
        $toUser->setConnection($connection);
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
        $this->patch('stories/'.$story->story_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->patch('/missions/'.$mission->mission_id.'/comments/'.$comment->comment_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        DB::setDefaultConnection('mysql');
        $this->get('app/notifications', ['token' => $token])
        ->seeStatusCode(200);
    }
}
