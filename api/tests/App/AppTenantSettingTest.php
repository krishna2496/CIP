<?php
use App\Helpers\Helpers;

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
        $this->get(route('app.tenant-settings'), ['Authorization' => Helpers::getBasicAuth()])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
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
        \App\Models\TenantActivatedSetting::where('deleted_at', null)->update(['deleted_at' => $currentDate]);
        DB::setDefaultConnection('mysql');
        $this->get(route('app.tenant-settings'), ['Authorization' => Helpers::getBasicAuth()])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
          ]);
        DB::setDefaultConnection('tenant');
        \App\Models\TenantActivatedSetting::withTrashed()->where('deleted_at', $currentDate)->update(['deleted_at' => null]);
    }
}
