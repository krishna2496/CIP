<?php
use App\Helpers\Helpers;

class AppUserTest extends TestCase
{
    /**
     * @test
     *
     * Search user by first name
     *
     * @return void
     */
    public function it_should_search_user_by_first_name()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('app/search-user?search='.substr($user->first_name, 2), ['token' => $token])
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
     * Search user by last name
     *
     * @return void
     */
    public function it_should_search_user_by_last_name()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('app/search-user?search='.substr($user->last_name, 2), ['token' => $token])
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
     * Search user by email
     *
     * @return void
     */
    public function it_should_search_user_by_email()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('app/search-user?search='.substr($user->email, 3), ['token' => $token])
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
     * Search user by email
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_param_for_search_user()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('app/search-user?search='.str_random(5), ['token' => $token])
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
     * Edit user data
     *
     * @return void
     */
    public function it_should_save_user_data()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();
        $fieldId = $userCustomField->field_id;

        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(50),
            'employee_id' => str_random(3),
            'department' => str_random(5),
            'manager_name' => str_random(5),
            'custom_fields' => [
                [
                    "field_id" => $fieldId,
                    "value" => "1"
                ]
            ],
            'skills' => []

        ];
    
        $token = Helpers::getJwtToken($user->user_id);

        $this->patch('app/user/', $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $userCustomField->delete();
    }
       
    /**
     * @test
     *
     * Validate skill limit for add skill to user
     *
     * @return void
     */
    public function it_should_return_skill_limit_error_for_save_user_data()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();
        $fieldId = $userCustomField->field_id;

        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();

        $skillsArray = [];
        for ($i = 0; $i <= config('constants.SKILL_LIMIT'); $i++ ) {
            $skillsArray[] = ["skill_id" => $skill->skill_id];
        }       

        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(50),
            'employee_id' => str_random(3),
            'department' => str_random(5),
            'manager_name' => str_random(5),
            'custom_fields' => [
                [
                    "field_id" => $fieldId,
                    "value" => "1"
                ]
            ],
            'skills' => $skillsArray

        ];
    
        $token = Helpers::getJwtToken($user->user_id);

        $this->patch('app/user/', $params, ['token' => $token])
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
        $skill->delete();
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * Return error if user data is invalid
     *
     * @return void
     */
    public function it_should_return_error_if_data_is_invalid_for_save_user_data()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'availability_id' => rand(1000000, 2000000)
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/user/', $params, ['token' => $token])
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
     * Change password
     *
     * @return void
     */
    public function it_should_change_password()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $user->password = "123456789";
        $user->update();

        $params = [
            'old_password' => "123456789",
            'password' => "12345678",
            'confirm_password' => "12345678"
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/change-password', $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
            "status",
            "data" =>[
                "token"
            ],
            "message"
            ]
        );
        $user->delete();
    }

    /**
     * @test
     * 
     * Show error if incorrect old password
     *
     * @return void
     */
    public function it_should_show_error_for_incorrect_old_password()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'old_password' => "test",
            'password' => "12345678",
            'confirm_password' => "12345678"
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/change-password', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        $user->delete();
    }
    
    /**
     * @test
     *
     * Show error if password and confirm password does not matched
     *
     * @return void
     */
    public function it_should_show_error_for_new_password_does_not_matched()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $user->password = "123456789";
        $user->update();

        $params = [
            'old_password' => "123456789",
            'password' => "12345678",
            'confirm_password' => "1234567800"
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/change-password', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        $user->delete();
    }

    /**
     * @test
     * 
     * Show error if required fields are empty
     *
     * @return void
     */
    public function it_should_show_error_if_required_fields_are_empty_for_change_password()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'old_password' => "",
            'password' => "",
            'confirm_password' => ""
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/change-password', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     * 
     * Upload profile image
     *
     * @return void
     */
    public function it_should_upload_profile_image()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $path= 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $fileData = file_get_contents($path);
        $base64 = base64_encode($fileData);
        
        $params = [
            'avatar' => $base64
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/user/upload-profile-image', $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "message"
            ]
        );
        $user->delete();
    }

    /**
     * @test
     *
     * Return error if required field is empty for upload profile image
     *
     * @return void
     */
    public function it_should_return_error_if_required_field_is_empty_for_upload_profile_image()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $params = [
            'avatar' => ""
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/user/upload-profile-image', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Get user detail
     *
     * @return void
     */
    public function it_should_get_user_detail()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();

        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/user-detail', ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data" => [
                "user_id",
                "first_name",
                "last_name",
                "email",
                "avatar",
                "timezone_id",
                "language_id",
                "availability_id",
                "why_i_volunteer",
                "employee_id",
                "department",
                "manager_name",
                "city_id",
                "country_id",
                "profile_text",
                "linked_in_url",
                "title",
                "status",
                "city",
                "country",
                "timezone",
                "availability",
                "custom_fields",
                "user_skills",
                "city_list",
                "language_list",
                "availability_list",
            ],
            "message"
        ]);
        $user->delete();
        $userCustomField->delete();
        $skill->delete();
    }

    /**
     * @test
     * 
     * Return error for invalid token
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_authorization_token()
    {
        $token = str_random(50);
        $this->get('/app/user-detail', ['token' => $token])
        ->seeStatusCode(400)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message"
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Return error if user data is invalid
     *
     * @return void
     */
    public function it_should_return_error_for_maxlength_validate_for_save_user_data()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'first_name' => str_random(300)
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/user/', $params, ['token' => $token])
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
     * Return error if user data is invalid
     *
     * @return void
     */
    public function it_should_return_error_for_url_validation_for_save_user_data()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'linked_in_url' => str_random(20)
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/user/', $params, ['token' => $token])
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
     * Return error if not valid file for upload profile image
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_file_type_for_upload_profile_image()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $path= 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $fileData = file_get_contents($path);
        $base64 = base64_encode($fileData);
        
        $params = [
            'avatar' => $base64
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/user/upload-profile-image', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        $user->delete();
    }
}
