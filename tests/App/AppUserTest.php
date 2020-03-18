<?php
use App\Helpers\Helpers;
use Firebase\JWT\JWT;
use Carbon\Carbon;
class AppUserTest extends TestCase
{
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

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
		\DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;        
        \DB::setDefaultConnection('mysql');
		
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

        $skillsArray[] = ["skill_id" => $skill->skill_id];

        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(50),
            'employee_id' => str_random(3),
            'department' => str_random(5),
            'custom_fields' => [
                [
                    "field_id" => $fieldId,
                    "value" => "1"
                ]
            ],
            'skills' => $skillsArray,
			"city_id" => $cityId,
			"country_id" => $countryDetail->country_id

        ];
    
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;        
        \DB::setDefaultConnection('mysql');
                
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
        for ($i = 0; $i <= config('constants.SKILL_LIMIT'); $i++) {
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
            'custom_fields' => [
                [
                    "field_id" => $fieldId,
                    "value" => "1"
                ]
            ],
            'skills' => $skillsArray,
			"city_id" => $cityId,
			"country_id" => $countryDetail->country_id

        ];
    
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $res = $this->patch('app/user/', $params, ['token' => $token])
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

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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

        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();

        $userSkill = factory(\App\Models\UserSkill::class)->make();
        $userSkill->setConnection($connection);
        $userSkill->user_id = $user->user_id;
        $userSkill->skill_id = $skill->skill_id;
        $userSkill->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $userSkill->delete();
        $userCustomField->delete();
        $skill->delete();
        $user->delete();
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

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
     * Get user language
     *
     * @return void
     */
    public function it_should_return_user_language()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/get-user-language?email='.$user->email, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data" => [
                "default_language_id"
            ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return error for invalid token
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_email_for_get_user_language()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/get-user-language?email=test', ['token' => $token])
        ->seeStatusCode(404)
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
     * Validate skill limit for add skill to user
     *
     * @return void
     */
    public function it_should_return_invalid_language_error_for_update_user_data()
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
            'language_id' => rand(1000, 5000),
            'availability_id' => 1,
            'why_i_volunteer' => str_random(50),
            'employee_id' => str_random(3),
            'department' => str_random(5),
            'custom_fields' => [
                [
                    "field_id" => $fieldId,
                    "value" => "1"
                ]
            ],
            'skills' => []

        ];
    
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

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
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * Show error if jwt token is blank
     *
     * @return void
     */
    public function it_should_show_error_if_jwt_token_is_blank()
    {
        $token = '';
        DB::setDefaultConnection('mysql');
        $this->patch('app/change-password', [], ['token' => $token])
        ->seeStatusCode(401)
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
     * Show error if jwt token is blank
     *
     * @return void
     */
    public function it_should_show_error_on_jwt_token_expiration()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->user_id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time(), // Expiration time
            'fqdn' => env('DEFAULT_TENANT')
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'));

        $this->patch('app/change-password', [], ['token' => $token])
        ->seeStatusCode(401)
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
     * Show error if jwt signature is invalid
     *
     * @return void
     */
    public function it_should_show_error_on_jwt_signature_invalid()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->user_id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() * 60 * 60, // Expiration time
            'fqdn' => env('DEFAULT_TENANT')
        ];

        $token = JWT::encode($payload, 'test');

        $this->patch('app/change-password', [], ['token' => $token])
        ->seeStatusCode(401)
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
     * Show error if jwt token is expired
     *
     * @return void
     */
    public function it_should_show_error_if_jwt_token_is_expired()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjIsImlhdCI6MTU2ODExNDA5NCwiZXhwIjoxNTY4MTI4NDk0LCJmcWRuIjoidGF0dmEifQ.x5mLYFU619-xnxSqJbRUt7iQz_Pwx5kka1YjWnNAhkc';
        $this->patch('app/change-password', [], ['token' => $token])
        ->seeStatusCode(401)
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
     * It should return invalid FQDN error
     *
     * @return void
     */
    public function it_should_return_invalid_fqdn_error()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, str_random('5'));

        $this->get('/app/search-user', ['token' => $token])
        ->seeStatusCode(401);
        $user->delete();
    }

    /**
     * @test
     *
     * It should return an error, tenant not found
     *
     * @return void
     */
    public function it_should_return_tenant_not_found()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        DB::setDefaultConnection('mysql');
        
        DB::table('tenant')->where('name', env('DEFAULT_TENANT'))->update(['deleted_at' => Carbon::now()]);

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/search-user?search='.substr($user->first_name, 2), ['token' => $token])
        ->seeStatusCode(404);

        $user->delete();
        
        DB::setDefaultConnection('mysql');
        DB::table('tenant')->where('name', env('DEFAULT_TENANT'))->update(['deleted_at' => null]);
        
    }
    
    /**
     * @test
     *
     * Return error if language id is invalid
     *
     * @return void
     */
    public function it_should_return_error_if_language_id_is_invalid_on_save_user_data()
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
            'language_id' => rand(1000000, 50000000),
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
            ]
        ];
    
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $this->patch('app/user/', $params, ['token' => $token])
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
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * Save cookie agreement date
     *
     * @return void
     */
    public function it_should_save_cookie_agreement_date()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
      
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/accept-cookie-agreement', [], ['token' => $token])
        ->seeStatusCode(201)
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

        $newUser = factory(\App\User::class)->make();
        $newUser->setConnection($connection);
        $newUser->save();

        $token = Helpers::getJwtToken($newUser->user_id, env('DEFAULT_TENANT'));
        $this->get('app/search-user?search='.substr($user->first_name, 2), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $newUser->delete();
    }

    /**
     * @test
     *
     * Edit user data
     *
     * @return void
     */
    public function it_should_return_error_on_save_user_data()
    {
		\DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;        
        \DB::setDefaultConnection('mysql');
		
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

        $skillsArray[] = ["skill_id" => $skill->skill_id];

        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 0,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(50),
            'employee_id' => str_random(3),
            'department' => str_random(5),
            'custom_fields' => [
                [
                    "field_id" => $fieldId,
                    "value" => "1"
                ]
            ],
            'skills' => $skillsArray,
			"city_id" => $cityId,
			"country_id" => $countryDetail->country_id

        ];
    
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $this->patch('app/user/', $params, ['token' => $token])
        ->seeStatusCode(422);
        $user->delete();
        $userCustomField->delete();
    }
    
    /**
     * @test
     *
     * It should create user with incomplete profile
     *
     * @return void
     */
    public function it_should_create_user_with_incomplete_profile()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->is_profile_complete = "0";
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        DB::setDefaultConnection('mysql');
        $this->get('app/missions', ['token' => $token])
        ->seeStatusCode(401);
        
        $user->delete();      
    }
}
