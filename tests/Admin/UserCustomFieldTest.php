<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;
use App\Models\UserCustomField;

class UserCustomFieldTest extends TestCase
{
    /**
     * @test
     *
     * Get all user custom fields
     *
     * @return void
     */
    public function it_should_return_all_user_custom_fields()
    {
        $this->get(route('metadata.users.custom_fields'), ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "field_id",
                    "name",
                    "type",
                    "translations" => [
                        "*" => [
                            "lang",
                            "name",
                            "values"
                        ]
                    ],
                    "type",
                ]
            ],
            "message"
        ]);
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
        $this->get(route("metadata.users.custom_fields"), ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Create user custom field api
     *
     * @return void
     */
    public function it_should_create_user_custom_field()
    {
        $slug = str_random(20);
        $params = [
            'page_details' =>
                [
                'slug' => $slug,
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' =>  [
                            [
                                'title' => str_random(20),
                                'description' => str_random(255),
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $this->post("metadata.users.custom_fields/", $params, ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'page_id',
            ],
            'message',
            'status',
            ]);
        
        UserCustomField::where('slug', $slug)->delete();
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
        $params = [
            'page_details' =>
                [
                'slug' => str_random(20),
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' =>  [
                            [
                                'title' => str_random(20),
                                'description' => str_random(255),
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $connection = 'tenant';
        $footerPage = factory(\App\Models\UserCustomField::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();
        $page_id = $footerPage->page_id;

        $this->patch("metadata.users.custom_fields/".$page_id, $params, ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'page_id',
            ],
            'message',
            'status',
            ]);
        $footerPage->delete();
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
        $footerPage = factory(\App\Models\UserCustomField::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();

        $this->delete(
            "metadata.users.custom_fields/".$footerPage->page_id,
            [],
            ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ=']
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
            "metadata.users.custom_fields/".rand(1000000, 50000000),
            [],
            ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ=']
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
            "metadata.users.custom_fields/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ=']
        )
        ->seeStatusCode(404);
    }
}
