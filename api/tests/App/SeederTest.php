<?php

class SeederTest extends TestCase
{
    /**
     * @test
     *
     * It will add default city and country data into database
     *
     * @return void
     */
    public function it_should_add_default_city_and_country_data()
    {
        \DB::setDefaultConnection('tenant');
        
        App\Models\Country::whereNull('deleted_at')->delete();
        App\Models\City::whereNull('deleted_at')->delete();
        App\Models\Timesheet::whereNull('deleted_at')->delete();
        App\Models\Story::whereNull('deleted_at')->delete();
        App\Models\Mission::whereNull('deleted_at')->delete();
        
        \DB::setDefaultConnection('mysql');

        $iso = str_random(2);
        $params = [
            "countries" => [
                [
                    "iso" => $iso,
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        /* Add country end */
        
        \DB::setDefaultConnection('mysql');
        /* Add state details start */     
        $stateName = str_random(5);   
        $params = [
            "country_id" => $countryId,
            "states" => [ 
                [ 
                    "translations" => [ 
                        [ 
                            "lang" => "en",
                            "name" => $stateName
                        ]
                    ]
                ]         
            ]
        ];

        $response = $this->post("entities/states", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        \DB::setDefaultConnection('mysql');

        /* Add city details start */     
        $cityName = str_random(5);   
        $params = [
            "country_id" => $countryId,
            "state_id" => $stateId,
            "cities" => [ 
                [ 
                    "translations" => [ 
                        [ 
                            "lang" => "en",
                            "name" => $cityName
                        ]
                    ]
                ]         
            ]
        ];

        $response = $this->post("entities/cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $cityId = json_decode($response->response->getContent())->data->city_ids[0]->city_id;

        \DB::setDefaultConnection('mysql');
        /* Add city details end */
    }
}
