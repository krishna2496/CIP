<?php

class TenantActivatedSettingsTest extends TestCase
{
    /**
     * @test
     *
     * Store multiple settings
     *
     * @return void
     */
    public function tenant_activated_settings_it_should_update_multiple_settings_at_time()
    {
        DB::setDefaultConnection('tenant');
        $settings = \App\Models\TenantSetting::get()->random(2);
        
        DB::setDefaultConnection('mysql');

        $params = [
            "settings" => [
                [
                    "tenant_setting_id" => $settings->first()->tenant_setting_id,
                    "value" => 1
                ],
                [
                    "tenant_setting_id" => $settings->last()->tenant_setting_id,
                    "value" => 0
                ]
            ]
        ];

        $this->post("tenant-settings", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Store multiple settings, validation error
     *
     * @return void
     */
    public function tenant_activated_settings_it_should_return_validation_error_for_update_multiple_settings_at_time()
    {
        DB::setDefaultConnection('tenant');
        $settings = \App\Models\TenantSetting::get()->random(2);
        
        DB::setDefaultConnection('mysql');

        $params = [
            "settings" => [
                [
                    "tenant_setting_id" => '',
                    "value" => 1
                ],
                [
                    "tenant_setting_id" => $settings->last()->tenant_setting_id,
                    "value" => 1
                ]
            ]
        ];

        $this->post("tenant-settings", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }
}