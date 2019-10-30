<?php

class AppTenantSettingTest extends TestCase
{
    /**
     * @test
     *
     * Get tenant setting details
     *
     * @return void
     */
    public function tenant_settings_it_should_return_tenant_setting_details()
    {
        
        $this->get(route('app.tenant-settings'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "key",
                    "tenant_setting_id"
                ]
            ],
            "message"
        ]);
    }

    /**
     * @test
     *
     * Get tenant setting details
     *
     * @return void
     */
    public function tenant_settings_it_should_return_no_data_found_on_tenant_setting_details()
    {
        $currentDate = date('Y-m-d H:i:s');
        DB::setDefaultConnection('tenant');
        \App\Models\TenantActivatedSetting::where('deleted_at', $currentDate)->update(['deleted_at' => null]);

        DB::setDefaultConnection('tenant');
        \App\Models\TenantActivatedSetting::where('deleted_at', NULL)->update(['deleted_at' => $currentDate]);
        DB::setDefaultConnection('mysql');
        $this->get(route('app.tenant-settings'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        DB::setDefaultConnection('tenant');
        \App\Models\TenantActivatedSetting::withTrashed()->where('deleted_at', $currentDate)->update(['deleted_at' => null]);
    }
}
