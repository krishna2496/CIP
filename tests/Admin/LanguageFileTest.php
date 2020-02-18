<?php

class LanguageFileTest extends TestCase
{

    /**
     * @test
     *
     * It should fetch language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_fetch_language_file()
    {
        $this->get('language-file?code=en', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
     * @test
     *
     * It should upload language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_upload_language_file()
    {
        $fileName = 'en';
        $path  = storage_path("unitTestFiles/$fileName.json");
        $params = [
            'file_name' => $fileName
        ];
        
        $res = $this->call(
            'POST',
            'language-file',
            $params,
            [],
            [
                'file_path' => array(new \Illuminate\Http\UploadedFile($path, $fileName.'.json', 'text/plain', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        // dd($res->response);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(['status', 'message']);
    }

    /**
     * @test
     *
     * It should upload language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_return_validation_error_on_upload_language_file()
    {
        $fileName = str_random(5);
        $path  = storage_path("unitTestFiles/en.json");
        $params = [
            'file_name' => $fileName
        ];
        
        $res = $this->call(
            'POST',
            'language-file',
            $params,
            [],
            [
                'file_path' => array(new \Illuminate\Http\UploadedFile($path, 'en.json', 'text/plain', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        // dd($res->response);
        $this->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should upload language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_return_invalid_file_error_on_upload_language_file()
    {
        $fileName = 'en';
        $path  = storage_path("unitTestFiles/dummy.css");
        $params = [
            'file_name' => $fileName
        ];
        
        $res = $this->call(
            'POST',
            'language-file',
            $params,
            [],
            [
                'file_path' => array(new \Illuminate\Http\UploadedFile($path, 'dummy.css', 'text/plain', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        // dd($res->response);
        $this->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should upload language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_return_invalid_json_format_error_on_upload_language_file()
    {
        $fileName = 'en';
        $path  = storage_path("unitTestFiles/invalid_en.json");
        $params = [
            'file_name' => $fileName
        ];
        
        $res = $this->call(
            'POST',
            'language-file',
            $params,
            [],
            [
                'file_path' => array(new \Illuminate\Http\UploadedFile($path, 'invalid_en.json', 'text/plain', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        // dd($res->response);
        $this->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should upload language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_return_error_when_adding_invalid_file_name()
    {
        $fileName = str_random(2);
        $path  = storage_path("unitTestFiles/en.json");
        $params = [
            'file_name' => $fileName
        ];
        
        $res = $this->call(
            'POST',
            'language-file',
            $params,
            [],
            [
                'file_path' => array(new \Illuminate\Http\UploadedFile($path, 'en.json', 'text/plain', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        // dd($res->response);
        $this->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should fetch language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_return_error_on_fetch_language_file()
    {
        $this->get('language-file?code=qq', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should fetch language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_return_error_for_invalid_code_on_fetch_language_file()
    {
        $this->get('language-file?code=test', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should upload language file on S3 bucket
     *
     * @return void
     */
    public function language_file_test_it_should_return_error_on_upload_language_file()
    {
        $fileName = 'en';
        $path  = storage_path("unitTestFiles/mission_params_en.json");
        $params = [
            'file_name' => $fileName
        ];
        
        $res = $this->call(
            'POST',
            'language-file',
            $params,
            [],
            [
                'file_path' => array(new \Illuminate\Http\UploadedFile($path, 'mission_params_en.json', 'text/plain', null, null, true))[0]
            ],
            [
                'HTTP_php-auth-user' => env('API_KEY'),
                'HTTP_php-auth-pw' => env('API_SECRET')
            ]
        );
        // dd($res->response);
        $this->seeStatusCode(422);
        // $this->seeJsonStructure(['status', 'message']);
    }
}
