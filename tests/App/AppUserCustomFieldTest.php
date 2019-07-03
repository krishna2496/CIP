<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;
use App\Models\UserCustomField;

class AppUserCustomFieldTest extends TestCase
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
        $this->get(route('metadata.users.custom_fields'), ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
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
        $this->get(route("metadata.users.custom_fields"), ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }
}
