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
        $randomTypes = array_rand($typeArray, 1);
        $name = str_random(20);
        $params = [
            'name' => $name,
            'type' => $typeArray[$randomTypes],
            'is_mandatory' => 1,
            'translations' => [
                [
                    'lang' => 'en',
                    'name' => str_random(10),
                    'values' => '[' . rand(1, 5) . ',' . rand(5, 10) . ']'
                ]
            ],
            'internal_note' => 'Sample note'
        ];

        $this->post(
            'metadata/users/custom_fields/',
            $params,
            ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))]
        )
            ->seeStatusCode(201)
            ->seeJsonStructure([
                'data' => [
                    'field_id'
                ],
                'message',
                'status'
            ]);
        $customField = App\Models\UserCustomField::where('name', $name)
            ->orderBy('field_id', 'DESC')
            ->first();
        $this->assertSame($params['name'], $customField->name);
        $this->assertSame($params['type'], $customField->type);
        $this->assertSame($params['is_mandatory'], $customField->is_mandatory);
        $this->assertSame($params['translations'], $customField->translations);
        $this->assertSame($params['internal_note'], $customField->internal_note);

        $customField->delete();
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

        $this->get(
            'metadata/users/custom_fields?search=' . $userCustomField->name,
            ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))]
        )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status',
                'data' => [
                    [
                        'field_id',
                        'order',
                        'name',
                        'type',
                        'translations',
                        'is_mandatory',
                        'internal_note'
                    ]
                ],
                'message'
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
        $this->get(route("metadata.users.custom_fields"), ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))])
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
        $randomTypes = array_rand($typeArray, 1);
        $name = str_random(20);
        $params = [
            'name' => $name,
            'order' => 1,
            'type' => $typeArray[$randomTypes],
            'is_mandatory' => 1,
            'translations' => [
                [
                    'lang' => 'en',
                    'name' => str_random(10),
                    'values' => '[' . rand(1, 5) . ',' . rand(5, 10) . ']'
                ]
            ],
            'internal_note' => 'Sample note'
        ];

        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make([
            'order' => 1
        ]);
        $userCustomField->setConnection($connection);
        $userCustomField->save();
        $fieldId = $userCustomField->field_id;

        $this->patch(
            'metadata/users/custom_fields/' . $fieldId,
            $params,
            ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))]
        )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'data' => [
                    'field_id'
                ],
                'message',
                'status'
            ]);

        $updatedCustomField = App\Models\UserCustomField::where('field_id', $fieldId)
            ->orderBy('field_id', 'DESC')
            ->first();
        $this->assertSame($params['name'], $updatedCustomField->name);
        $this->assertSame($params['type'], $updatedCustomField->type);
        $this->assertSame($params['is_mandatory'], $updatedCustomField->is_mandatory);
        $this->assertSame($params['translations'], $updatedCustomField->translations);
        $this->assertSame($params['internal_note'], $updatedCustomField->internal_note);

        $updatedCustomField->delete();
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
            "metadata/users/custom_fields/" . $userCustomField->field_id,
            [],
            ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))]
        )
            ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Delete multiple user custom field
     *
     * @return void
     */
    public function it_should_delete_multiple_user_custom_field()
    {
        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();

        $this->delete(
            "metadata/users/custom_fields/" . $userCustomField->field_id,
            [1, 2, 3],
            ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))]
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
            "metadata/users/custom_fields/" . rand(1000000, 50000000),
            [],
            ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))]
        )
            ->seeStatusCode(404)
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
                'translations' => [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' => [
                            'title' => str_random(20),
                            'description' => str_random(255)
                        ]
                    ]
                ]
            ]
        ];

        $this->patch(
            "metadata/users/custom_fields/" . rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))]
        )
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
    }

    /**
     * @test
     *
     * return invalid argument error for get all user custom fields
     *
     * @return void
     */
    public function it_should_return_invalid_argument_error_for_all_user_custom_fields()
    {
        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();

        $this->get('metadata/users/custom_fields?order=test', ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))])
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
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * Return invalid data error for create user custom field api
     *
     * @return void
     */
    public function it_should_return_error_for_create_user_custom_field()
    {
        $typeArray = config('constants.custom_field_types');
        $randomTypes = array_rand($typeArray, 1);
        $name = str_random(20);
        $params = [
            'name' => $name,
            'type' => $typeArray[$randomTypes],
            'is_mandatory' => 1,
            'translations' => [
                [
                    'lang' => "test",
                    'name' => str_random(10),
                    'values' => "[" . rand(1, 5) . "," . rand(5, 10) . "]"
                ]
            ]
        ];

        $this->post("metadata/users/custom_fields/", $params, ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))])
            ->seeStatusCode(422)
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
     * Return invalid data error for update user custom field api
     *
     * @return void
     */
    public function it_should_return_error_for_update_user_custom_field()
    {
        $typeArray = config('constants.custom_field_types');
        $randomTypes = array_rand($typeArray, 1);
        $name = str_random(20);
        $params = [
            'name' => $name,
            'type' => $typeArray[$randomTypes],
            'is_mandatory' => 1,
            'translations' => [
                [
                    'lang' => "en",
                    'name' => str_random(10),
                    'values' => "[" . rand(1, 5) . "," . rand(5, 10) . "]"
                ]
            ]
        ];

        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();
        $field_id = $userCustomField->field_id;

        $params = [
            'name' => $name,
            'type' => $typeArray[$randomTypes],
            'is_mandatory' => 1,
            'translations' => [
                [
                    'lang' => "test",
                    'name' => str_random(10),
                    'values' => "[" . rand(1, 5) . "," . rand(5, 10) . "]"
                ]
            ]
        ];

        $this->patch("metadata/users/custom_fields/" . $field_id, $params, ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))])
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "errors" => [
                    [
                        "status",
                        "type",
                        "message"
                    ]
                ]
            ]);
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * Return invalid argument error for get all user custom fields
     *
     * @return void
     */
    public function it_should_return_invalid_argument_error_for_get_all_user_custom_fields()
    {
        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();

        $this->get('metadata/users/custom_fields?order=test', ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))])
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
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * Get a custom field by custom_fields_id
     *
     * @return void
     */
    public function it_should_return_a_custom_fields_for_admin_by_custom_fields_id()
    {
        $connection = 'tenant';
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();

        $this->get('metadata/users/custom_fields/' . $userCustomField->field_id, ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                "status",
                "data" => [
                    "field_id",
                    "order",
                    "name",
                    "type",
                    "translations"
                ],
                "message"
            ]);
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * Return error if custom_fields_id is wrong
     *
     * @return void
     */
    public function it_should_return_error_if_custom_fields_id_is_wrong()
    {
        $this->get('metadata/users/custom_fields/' . rand(1000000, 2000000), ['Authorization' => 'Basic ' . base64_encode(env('API_KEY') . ':' . env('API_SECRET'))])
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
}
