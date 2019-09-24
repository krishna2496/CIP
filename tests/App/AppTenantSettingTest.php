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
        $connection = 'tenant';
        $tenantSetting = factory(\App\Models\TenantSetting::class)->make();
        $tenantSetting->setConnection($connection);
        $tenantSetting->save();
        // $user->id;

        // $connection = 'tenant';
        $tenantActivatedSetting = factory(\App\Models\TenantActivatedSetting::class)->make();
        $tenantActivatedSetting->setConnection($connection);
        $tenantActivatedSetting->save();

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
        $tenantActivatedSetting->delete();
        $tenantSetting->delete();
    }
}
