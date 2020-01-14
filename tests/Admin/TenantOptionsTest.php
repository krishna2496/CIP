<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class TenantOptionsTest extends TestCase
{
    /**
     * @test
     *
     * Reset style to default
     *
     * @return void
     */
    public function tenant_option_testing_it_should_reset_style_to_default()
    {
        $this->get('style/reset-style', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }
    
    /**
     * @test
     *
     * Download style and images from s3
     *
     * @return void
     */
    public function tenant_option_testing_it_should_download_style_from_s3()
    {
        $this->get('style/download-style', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
            ]);
    }

    /**
    * @test
    *
    * Update style
    *
    * @return void
    */
    public function tenant_option_testing_it_should_update_primary_color()
    {
        $params = [
            'primary_color' => "#ccc"
        ];

        $this->post('style/update-style', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
    * @test
    *
    * Get custom styling css
    *
    * @return void
    */
    public function tenant_option_testing_style_it_should_return_custom_css()
    {
        $this->get('app/custom-css', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
     * @test
     *
     * Create tenant option
     *
     * @return void
     */
    public function tenant_option_testing_it_should_create_tenant_option()
    {
        $optionName = str_random(20);
        $params = [
            'option_name' => $optionName,
            'option_value' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'message' => str_random(20)
                    ]
                ],
            ],
        ];

        $this->post("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        App\Models\TenantOption::where("option_name", $optionName)->orderBy("tenant_option_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if data is invalid
     *
     * @return void
     */
    public function tenant_option_testing_it_should_return_error_for_invalid_data_for_tenant_option()
    {
        $optionName = '';
        $params = [
            'option_name' => $optionName,
            'option_value' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'message' => str_random(20)
                    ]
                ],
            ],
        ];

        $this->post("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    * It should update assets image on S3 server
    *
    * @return void
    */
    public function tenant_option_testing_style_update_it_should_update_assets_image_on_s3_server()
    {
        
        $fileName = 'back-arrow-black.svg';
        $path  = storage_path("unitTestFiles/$fileName");
        $params = [
            'image_name' => $fileName
        ];
        
        $res = $this->call(
            'PATCH',
            'style/update-image', 
            $params, [], 
            [
                'image_file' => array(new \Illuminate\Http\UploadedFile($path, $fileName, 'image/svg+xml', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );

        $this->seeStatusCode(200);
        $this->seeJsonStructure(['status', 'message']);

        // Image name field is required
        $params = [
            'image_name' => ''
        ];
        
        DB::setDefaultConnection('mysql');
        $res = $this->call(
            'PATCH',
            'style/update-image', 
            $params, [], 
            [
                'image_file' => array(new \Illuminate\Http\UploadedFile($path, $fileName, 'image/svg+xml', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(422);
        
        // Invalid file type
        $invalidFileName = 'dummy.txt';
        $path  = storage_path("unitTestFiles/$fileName");
        $params = [
            'image_name' => $fileName
        ];
        DB::setDefaultConnection('mysql');
        $res = $this->call(
            'PATCH',
            'style/update-image', 
            $params, [], 
            [
                'image_file' => array(new \Illuminate\Http\UploadedFile($path, $invalidFileName, '', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(422);

        // Invalid file extension
        $invalidFileName = 'dummy.txt';
        $path  = storage_path("unitTestFiles/$invalidFileName");
        $params = [
            'image_name' => $invalidFileName
        ];
        DB::setDefaultConnection('mysql');
        $res = $this->call(
            'PATCH',
            'style/update-image', 
            $params, [], 
            [
                'image_file' => array(new \Illuminate\Http\UploadedFile($path, $invalidFileName, '', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(422);

        // File not exist on S3
        $invalidFileName = 'dummy.svg';
        $path  = storage_path("unitTestFiles/$fileName");
        $params = [
            'image_name' => $invalidFileName
        ];
        DB::setDefaultConnection('mysql');

        $res = $this->call(
            'PATCH',
            'style/update-image', 
            $params, [], 
            [
                'image_file' => array(new \Illuminate\Http\UploadedFile($path, $fileName, 'image/svg+xml', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(404);
    }

    /**
    * @test
    *
    * It should update SCSS changes on S3 and update new CSS
    *
    * @return void
    */
    public function tenant_option_testing_style_update_it_should_update_scss_changes()
    {
        // Simple update other SCSS file
        $fileName = 'typography.scss';
        $path  = storage_path("unitTestFiles/$fileName");
        $params = [
            'custom_scss_file_name' => $fileName
        ];
        DB::setDefaultConnection('mysql');
        $res = $this->call(
            'POST',
            'style/update-style',
            $params,
            [],
            [
                'custom_scss_file' => array(new \Illuminate\Http\UploadedFile($path, $fileName, '', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(200);
        $this->seeJsonStructure(['status', 'message']);              
    }

    /**
     * @test
     *
     * Update tenant option
     *
     * @return void
     */
    public function tenant_option_testing_it_should_update_tenant_option()
    {
        $optionName = str_random(20);
        $params = [
            'option_name' => $optionName,
            'option_value' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'message' => str_random(20)
                    ]
                ],
            ],
        ];

        $this->post("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        DB::setDefaultConnection('mysql');
        $this->patch("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        App\Models\TenantOption::where("option_name", $optionName)->orderBy("tenant_option_id", "DESC")->take(1)->delete();
    }

        /**
     * @test
     *
     * Return error if data is invalid
     *
     * @return void
     */
    public function tenant_option_testing_it_should_return_error_for_invalid_data_for_update_tenant_option()
    {
        $optionName = str_random(20);
        $params = [
            'option_name' => $optionName,
            'option_value' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'message' => str_random(20)
                    ]
                ],
            ],
        ];

        $this->post("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        DB::setDefaultConnection('mysql');

        $optionName = str_random(20);
        $params = [
            'option_name' => $optionName,
            'option_value' =>
                [
                'translations' =>  [
                    [
                        'lang' => str_random('3'),
                        'message' => str_random(20)
                    ]
                ],
            ],
        ];
        
        $this->patch("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    * Update style
    *
    * @return void
    */
    public function tenant_option_testing_it_should_return_error_for_required_field_while_update_style()
    {
        $params = [];

        $this->post('style/update-style', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should return error, while trying to update unavailable option
     *
     * @return void
     */
    public function tenant_option_testing_it_should_update_tenant_option_with_unavailable_option()
    {
        $optionName = str_random(20);
        $params = [
            'option_name' => $optionName,
            'option_value' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'message' => str_random(20)
                    ]
                ],
            ],
        ];
        
        $this->patch("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);

    }

    /**
     * @test
     *
     * Reset assets images to default
     *
     * @return void
     */
    public function tenant_option_testing_it_should_reset_assets_images_to_default()
    {
        $this->get('style/reset-asset-images', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
     * @test
     *
     * Create tenant option
     *
     * @return void
     */
    public function tenant_option_testing_it_should_create_tenant_option_value()
    {
        $optionName = str_random(20);
        $params = [
            'option_name' => $optionName,
            'option_value' => 1            
        ];

        $this->post("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        App\Models\TenantOption::where("option_name", $optionName)->orderBy("tenant_option_id", "DESC")->take(1)->delete();
    }

    /**
    * @test
    * it should return error bucket not found for update primary color
    * 
    * @return void
    */
    public function tenant_option_testing_it_should_return_error_bucket_not_found_for_update_primary_color()
    {
        DB::setDefaultConnection('mysql');
        
        $tenantId = DB::table('tenant')->insertGetId(
            [
                'name' => str_random('5'),
                'sponsor_id' => rand(1,9999)
            ]
        );

        $apiKey = base64_encode(str_random('8'));
        $randomString = str_random('8');
        $apiSecret = Hash::make($randomString);
        
        $apiUserId = DB::table('api_user')->insertGetId(
            [
                'tenant_id' => $tenantId,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'status' => 1
            ]
        );

        $apiUser = DB::table('api_user')->where('api_user_id', $apiUserId)->first();

        $apiKey = base64_decode($apiUser->api_key);
        $apiSecret = $randomString;

        DB::statement("CREATE DATABASE IF NOT EXISTS `ci_tenant_{$tenantId}`");
        DB::table('tenant_language')->insert([
            'tenant_id' => $tenantId,
            'language_id' => 1,
            'default' => '1'
        ]);

        $this->get('style/download-style', ['Authorization' => 'Basic '.base64_encode($apiKey.':'.$apiSecret), 'X-localization' => 'en'])
        ->seeStatusCode(404);

        DB::setDefaultConnection('mysql');

        DB::statement("DROP DATABASE ci_tenant_{$tenantId}");

        DB::table('tenant')->where('tenant_id', $tenantId)->delete();
    }
   
    /**
    * @test
    *
    * Uploading variable file with primary color
    *
    * @return void
    */
    public function tenant_option_testing_style_update_it_should_uploading_variable_file_with_primary_color()
    {
        $fileName = '_variables.scss';
        $path  = storage_path("unitTestFiles/$fileName");
        $params = [
            'primary_color' => '#69c027',
            'custom_scss_file_name' => $fileName
        ];
        DB::setDefaultConnection('mysql');
        $res = $this->call(
            'POST',
            'style/update-style',
            $params,
            [],
            [
                'custom_scss_file' => array(new \Illuminate\Http\UploadedFile($path, $fileName, '', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(200);
        $this->seeJsonStructure(['status', 'message']);
    }

    /**
    * @test
    *
    * Uploading File name is require
    *
    * @return void
    */
    public function tenant_option_testing_style_update_it_should_return_error_uploading_File_name_is_require_onupdate_scss_changes()
    {
        $fileName = 'typography.scss';
        $path  = storage_path("unitTestFiles/$fileName");

        $params = [
            'custom_scss_file_name' => ''
        ];
        DB::setDefaultConnection('mysql');
        $res = $this->call(
            'POST',
            'style/update-style',
            $params,
            [],
            [
                'custom_scss_file' => array(new \Illuminate\Http\UploadedFile($path, $fileName, '', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(422);
        
    }

    /**
    * @test
    *
    * Uploading File extension must be SCSS, but file name is correct
    *
    * @return void
    */
    public function tenant_option_testing_style_update_it_should_return_error_for_invalid_extension_on_update_scss_changes()
    {
        $fileName = 'typography.scss';
        $invalidFileName = 'dummy.txt';
        $path  = storage_path("unitTestFiles/$invalidFileName");
        $params = [
            'custom_scss_file_name' => $fileName
        ];
        DB::setDefaultConnection('mysql');
        $res = $this->call(
            'POST',
            'style/update-style',
            $params,
            [],
            [
                'custom_scss_file' => array(new \Illuminate\Http\UploadedFile($path, $invalidFileName, '', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(422);
        
    }

    /**
    * @test
    *
    * Uploading File extension is correct, but file name is incorrect
    *
    * @return void
    */
    public function tenant_option_testing_style_update_it_should_return_error_for_invalid_file_name_on_update_scss_changes()
    {
        $fileName = 'typography.scss';
        $invalidFileName = 'dummy.txt';
        $path  = storage_path("unitTestFiles/$fileName");
        $params = [
            'custom_scss_file_name' => $invalidFileName
        ];
        DB::setDefaultConnection('mysql');
        $res = $this->call(
            'POST',
            'style/update-style',
            $params,
            [],
            [
                'custom_scss_file' => array(new \Illuminate\Http\UploadedFile($path, $fileName, '', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        $this->seeStatusCode(422);
        
    }

    /**
    * @test
    *
    * Valiation error for empty data
    *
    * @return void
    */
    public function tenant_option_testing_style_update_it_should_error_for_empty_date_on_update_scss_changes()
    {
        $params = [
            'custom_scss_file_name' => '',
            'custom_scss_file' => ''
        ];
        DB::setDefaultConnection('mysql');
        $this->post("style/update-style/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);  
        
    }

    /**
     * @test
     *
     * Download style and images from s3
     *
     * @return void
     */
    public function tenant_option_testing_it_should_return_error_bucket_not_found_on_download_style_from_s3()
    {
        DB::setDefaultConnection('mysql');
        
        $tenantId = DB::table('tenant')->insertGetId([
            'name' => str_random('5'),
            'sponsor_id' => rand(1, 100000)
        ]);
            
        DB::table('tenant_language')->insert([
            'tenant_id' => $tenantId,
            'language_id' => 1,
            'default' => '1'
        ]);


        $apiKey = str_random(16);
        $apiSecret = str_random(16);

        $apiKeys['api_key'] = base64_encode($apiKey);
        $apiKeys['api_secret'] = \Illuminate\Support\Facades\Hash::make($apiSecret);
        $apiKeys['tenant_id'] = $tenantId;
        
        $apiUserId = DB::table('api_user')->insertGetId($apiKeys);

        DB::statement("CREATE DATABASE IF NOT EXISTS `ci_tenant_{$tenantId}`");
        
        $this->get('style/download-style', ['Authorization' => 'Basic '.base64_encode($apiKey.':'.$apiSecret)])
        ->seeStatusCode(404);
        
        DB::statement("DROP DATABASE `ci_tenant_{$tenantId}`");
        DB::setDefaultConnection('mysql');
        DB::table('tenant')->where('tenant_id', $tenantId)->delete();
        DB::table('api_user')->where('api_user_id', $apiUserId)->delete();
    }

    /**
    * @test
    *
    * It should return error bucket not found on update assets image on s3 server
    *
    * @return void
    */
    public function tenant_option_testing_it_should_return_error_bucket_not_found_on_update_assets_image_on_s3_server()
    {
        DB::setDefaultConnection('mysql');
        
        $tenantId = DB::table('tenant')->insertGetId([
            'name' => str_random('5'),
            'sponsor_id' => rand(1, 100000)
        ]);

        $apiKey = str_random(16);
        $apiSecret = str_random(16);

        $apiKeys['api_key'] = base64_encode($apiKey);
        $apiKeys['api_secret'] = \Illuminate\Support\Facades\Hash::make($apiSecret);
        $apiKeys['tenant_id'] = $tenantId;
        
        $apiUserId = DB::table('api_user')->insertGetId($apiKeys);

        DB::statement("CREATE DATABASE IF NOT EXISTS `ci_tenant_{$tenantId}`");

        DB::table('tenant_language')->insert([
            'tenant_id' => $tenantId,
            'language_id' => 1,
            'default' => '1'
        ]);

        $fileName = 'back-arrow-black.svg';
        $path  = storage_path("unitTestFiles/$fileName");
        $params = [
            'image_name' => $fileName
        ];
        
        $res = $this->call(
            'PATCH',
            'style/update-image', 
            $params, [], 
            [
                'image_file' => array(new \Illuminate\Http\UploadedFile($path, $fileName, 'image/svg+xml', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => $apiKey,
                'HTTP_php-auth-pw' => $apiSecret
            ]
        );

        $this->seeStatusCode(404);

        DB::statement("DROP DATABASE `ci_tenant_{$tenantId}`");
        DB::setDefaultConnection('mysql');
        DB::table('tenant')->where('tenant_id', $tenantId)->delete();
        DB::table('api_user')->where('api_user_id', $apiUserId)->delete();
    }

    /**
     * @test
     *
     * It should return error no file found on s3 when downloading style from s3
     *
     * @return void
     */
    public function tenant_option_testing_it_should_return_error_no_file_found_on_s3_when_downloading_style_from_s3()
    {
        DB::setDefaultConnection('mysql');
        
        $tenantId = DB::table('tenant')->insertGetId([
            'name' => str_random('5'),
            'sponsor_id' => rand(1, 100000)
        ]);
        
        $tenant = DB::table('tenant')->where('tenant_id', $tenantId)->first();
        $apiKey = str_random(16);
        $apiSecret = str_random(16);

        $apiKeys['api_key'] = base64_encode($apiKey);
        $apiKeys['api_secret'] = \Illuminate\Support\Facades\Hash::make($apiSecret);
        $apiKeys['tenant_id'] = $tenantId;
        
        $apiUserId = DB::table('api_user')->insertGetId($apiKeys);

        DB::statement("CREATE DATABASE IF NOT EXISTS `ci_tenant_{$tenantId}`");
        DB::table('tenant_language')->insert([
            'tenant_id' => $tenantId,
            'language_id' => 1,
            'default' => '1'
        ]);

        Storage::disk('s3')->put(
            $tenant->name.'/assets/css/style.css',
            $path  = storage_path().'/unitTestFiles/dummy.css'
        );

        $this->get('style/download-style', ['Authorization' => 'Basic '.base64_encode($apiKey.':'.$apiSecret)])
        ->seeStatusCode(404);

        DB::statement("DROP DATABASE `ci_tenant_{$tenantId}`");
        DB::setDefaultConnection('mysql');
        DB::table('tenant')->where('tenant_id', $tenantId)->delete();
        DB::table('api_user')->where('api_user_id', $apiUserId)->delete();

        Storage::disk('s3')->deleteDirectory($tenant->name);
    }
    
    /**
    * @test
    *
    * Update style
    *
    * @return void
    */
    public function tenant_option_testing_it_should_update_secondary_color()
    {
        $params = [
            'primary_color' => "#ccc",            
            'secondary_color' => "#000"
        ];

        $this->post('style/update-style', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }
}
