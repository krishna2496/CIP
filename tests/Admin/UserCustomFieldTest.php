<?php

class UserCustomFieldTest extends TestCase
{
    /**
     * @test
     *
     * Create user custom field api
     *
     * @return void
     */
    public function it_should_create_user_custom_field()
    {
        $typeArray = config('constants.custom_field_types');
        $randomTypes = array_rand($typeArray,1);       
        $name = str_random(20);
        $params = [
            'name' => $name,
            'type' => $typeArray[$randomTypes],
            'is_mandatory' => 1,
            'translations' => [
                [
                    'lang' => "en",
                    'name' => str_random(10),
                    'values' => "[".rand(1, 5).",".rand(5, 10)."]"
                ]
            ]
        ];

        $this->post("metadata/users/custom_fields/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'field_id',
            ],
        'message',
        'status',
        ]);
        App\Models\UserCustomField::where("name", $name)->orderBy("field_id", "DESC")->take(1)->delete();
    }
    
    /**
     * @test
     *
     * Get all user custom fields
     *
     * @return void
     */
    public function it_should_return_all_user_custom_fields()
    {
        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();

        $this->get(route('metadata.users.custom_fields'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                [
                    "field_id",
                    "name",
                    "type",
                    "translations",
                    "is_mandatory",
                ]
            ],
            "message"
        ]);
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * No user custom field found
     *
     * @return void
     */
    public function it_should_return_no_user_custom_field_found()
    {
        $this->get(route("metadata.users.custom_fields"), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }
    
    /**
     * @test
     *
     * Update user custom field api
     *
     * @return void
     */
    public function it_should_update_user_custom_field()
    {
        $typeArray = config('constants.custom_field_types');
        $randomTypes = array_rand($typeArray,1);    
        $name = str_random(20);
        $params = [
            'name' => $name,
            'type' => $typeArray[$randomTypes],
            'is_mandatory' => 1,
            'translations' => [
                [
                    'lang' => "en",
                    'name' => str_random(10),
                    'values' => "[".rand(1, 5).",".rand(5, 10)."]"
                ]
            ]
        ];

        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();
        $field_id = $userCustomField->field_id;

        $this->patch("metadata/users/custom_fields/".$field_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'field_id',
            ],
            'message',
            'status',
            ]);
        $userCustomField->delete();
    }
    
    /**
     * @test
     *
     * Delete user custom field
     *
     * @return void
     */
    public function it_should_delete_user_custom_field()
    {
        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();

        $this->delete(
            "metadata/users/custom_fields/".$userCustomField->field_id,
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Delete user custom field api with already deleted or not available user custom field id
     * @return void
     */
    public function it_should_return_user_custom_field_not_found_on_delete()
    {
        $this->delete(
            "metadata/users/custom_fields/".rand(1000000, 50000000),
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * Update user custom field api with already deleted or not available user custom field id
     * @return void
     */
    public function it_should_return_user_custom_field_not_found_on_update()
    {
        $params = [
            'page_details' =>
                [
                'slug' => str_random(20),
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' =>  [
                            'title' => str_random(20),
                            'description' => str_random(255),
                        ],
                    ]
                ],
            ],
        ];
        
        $this->patch(
            "metadata/users/custom_fields/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(404);
    }
}
