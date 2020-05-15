<?php

class AvailabilityTest extends TestCase
{
    /**
     * @test
     *
     * Create availability
     *
     * @return void
     */
    public function it_should_create_availability()
    {
        $availabilityType = str_random(10);
        $params = [        
            "type" => $availabilityType,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "Work Week only"
                ]
            ]
        ];

        $this->post("entities/availability", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        App\Models\Availability::where("type", $availabilityType)->orderBy("availability_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if user do not enter availability name
     *
     * @return void
     */
    public function it_should_return_error_if_type_is_blank()
    {
        $params = [        
            "type" => "",
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "availability testing"
                ]
            ]
        ];

        $this->post("entities/availability", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Get all availability
     *
     * @return void
     */
    public function it_should_return_all_availability_for_admin()
    {
        $availabilityType = str_random(10);
        $params = [        
            "type" => $availabilityType,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "availability testing"
                ]
            ]
        ];

        $this->post("entities/availability", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);
        DB::setDefaultConnection('mysql');

        $this->get('entities/availability?perPage=test&search='.$availabilityType, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\Availability::where("type", $availabilityType)->orderBy("availability_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Get a availability by availability id
     *
     * @return void
     */
    public function it_should_return_a_availability_for_admin_by_availability_id()
    {
        $availabilityType = str_random(10);
        $params = [        
            "type" => $availabilityType,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "availability testing"
                ]
            ]
        ];

        $this->post("entities/availability", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        $availability = App\Models\Availability::where("type", $availabilityType)->orderBy("availability_id", "DESC")->take(1)->get();
        $availabilityId = $availability[0]->availability_id;
        DB::setDefaultConnection('mysql');

        $this->get('entities/availability/'.$availabilityId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "availability_id",
                "type",
                "translations"
            ],
            "message"
        ]);
        App\Models\Availability::where("type", $availabilityType)->orderBy("availability_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if availability id is wrong
     *
     * @return void
     */
    public function it_should_return_error_if_availability_id_is_wrong()
    {
        $this->get('entities/availability/'.rand(1000000,2000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
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
     * It should update availability
     *
     * @return void
     */
    public function it_should_update_availability()
    {
        $availabilityType = str_random(10);
        $params = [        
            "type" => $availabilityType,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "availability testing"
                ]
            ]
        ];

        $this->post("entities/availability", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);

        $availability = App\Models\Availability::where("type", $availabilityType)->orderBy("availability_id", "DESC")->take(1)->get();
        $availabilityId = $availability[0]->availability_id;
        
        DB::setDefaultConnection('mysql');

        $params = [        
            "type" => str_random(20)
        ];
        
        $this->patch('entities/availability/'.$availabilityId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\Availability::where("availability_id", $availabilityId)->delete();
    }

    /**
     * @test
     *
     * It should return error for blank availability name
     *
     * @return void
     */
    public function it_should_return_error_for_update_availability_blank_type()
    {
        $availabilityType = str_random(10);
        $params = [        
            "type" => $availabilityType,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "availability testing"
                ]
            ]
        ];

        $this->post("entities/availability", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);
        
        $availability = App\Models\Availability::where("type", $availabilityType)->orderBy("availability_id", "DESC")->take(1)->get();
        $availabilityId = $availability[0]->availability_id;
        DB::setDefaultConnection('mysql');

        $params = [        
            "type" => ""
        ];
        
        $this->patch('entities/availability/'.$availabilityId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        App\Models\Availability::where("availability_id", $availabilityId)->delete();
    }

    /**
     * @test
     *
     * It should return error if user enter wrong availability id
     *
     * @return void
     */
    public function it_should_return_error_for_wrong_availability_id()
    {   
        $params = [        
            "type" => str_random(20),
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "availability testing"
                ]
            ]
        ];

        $this->patch('entities/availability/'.rand(1000000, 5000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
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
     * It should delete availability
     *
     * @return void
     */
    public function it_should_delete_availability()
    {
        $availabilityType = str_random(10);
        $params = [        
            "type" => $availabilityType,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "availability testing"
                ]
            ]
        ];

        $this->post("entities/availability", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);

        $availability = App\Models\Availability::where("type", $availabilityType)->orderBy("availability_id", "DESC")->take(1)->get();
        $availabilityId = $availability[0]->availability_id;
        DB::setDefaultConnection('mysql');
        
        $this->delete('entities/availability/'.$availabilityId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * It should return error for invalid availability id for delete availability
     *
     * @return void
     */
    public function it_should_return_error_for_delete_availability_for_invalid_availability_id()
    {   
        $this->delete('entities/availability/'.rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
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
     * Return invalid argument error
     *
     * @return void
     */
    public function it_should_return_invalid_argument_error_for_get_all_availability_for_admin()
    {
        $availabilityType = str_random(10);
        $params = [        
            "type" => $availabilityType,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "availability testing"
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');

        $this->post("entities/availability", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);
        DB::setDefaultConnection('mysql');

        $this->get('entities/availability?order=test', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(400)
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
        App\Models\Availability::where("type", $availabilityType)->orderBy("availability_id", "DESC")->take(1)->delete();
    }
}
