<?php

class TenantSettingsTest extends TestCase
{
    /**
     * @test
     *
     * Get all tenant settings
     *
     * @return void
     */
    public function it_should_return_all_tenant_settings()
    {
        $this->get('settings', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
               "*" => [
                    "tenant_setting_id",
                    "title",
                    "description",
                    "key",
                    "value"
                ]
            ],
            "message"
        ]);
    }

    /**
     * @test
     *
     * Update tenant setting
     *
     * @return void
     */
    public function it_should_update_tenant_settings()
    {
        DB::setDefaultConnection('tenant');
        $settingId = \App\Models\TenantSetting::get()->random()->tenant_setting_id;
        DB::setDefaultConnection('mysql');

        $params = [
                    "value" => "1"
                ];

        $this->patch("settings/".$settingId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status'
        ]);
    }

    /**
     * @test
     *
     * Update tenant setting return error if user enter wrong value
     *
     * @return void
     */
    public function it_should_return_error_if_user_enter_wrong_value()
    {
        DB::setDefaultConnection('tenant');
        $settingId = \App\Models\TenantSetting::get()->random()->tenant_setting_id;
        DB::setDefaultConnection('mysql');

        $params = [
                    "value" => "123456"
                ];

        $this->patch("settings/".$settingId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Update tenant setting return error if user enter invalid setting id
     *
     * @return void
     */
    public function it_should_return_error_if_user_enter_invalid_setting_id()
    {
        $settingId = rand(100000, 500000);
        $params = [
                    "value" => "1"
                ];

        $this->patch("settings/".$settingId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Update tenant setting return error if user enter blank value
     *
     * @return void
     */
    public function it_should_return_error_if_user_enter_blank_value()
    {
        DB::setDefaultConnection('tenant');
        $settingId = \App\Models\TenantSetting::get()->random()->tenant_setting_id;
        DB::setDefaultConnection('mysql');

        $params = [
                    "value" => ""
                ];

        $this->patch("settings/".$settingId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
}
