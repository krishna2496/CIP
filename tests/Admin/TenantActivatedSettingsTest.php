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
        DB::setDefaultConnection('mysql');
        $emailNotificationInviteColleague = config('constants.tenant_settings.EMAIL_NOTIFICATION_INVITE_COLLEAGUE');
        $settings = DB::select("SELECT * FROM tenant_setting as t WHERE t.key='$emailNotificationInviteColleague'"); 
        DB::setDefaultConnection('tenant');        
        $tenantSetting1 = App\Models\TenantSetting::create(['setting_id' =>$settings[0]->tenant_setting_id]);
        App\Models\TenantActivatedSetting::create(['tenant_setting_id' =>$tenantSetting1->tenant_setting_id]);

        DB::setDefaultConnection('mysql');
        $missionCommentAutoApproved = config('constants.tenant_settings.MISSION_COMMENT_AUTO_APPROVED');
        $settings = DB::select("SELECT * FROM tenant_setting as t WHERE t.key='$missionCommentAutoApproved'"); 
        DB::setDefaultConnection('tenant');        
        $tenantSetting2 = App\Models\TenantSetting::create(['setting_id' =>$settings[0]->tenant_setting_id]);
        App\Models\TenantActivatedSetting::create(['tenant_setting_id' =>$tenantSetting2->tenant_setting_id]);

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
        $tenantSetting1->delete();
        $tenantSetting2->delete();
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
        DB::setDefaultConnection('mysql');
        $emailNotificationInviteColleague = config('constants.tenant_settings.EMAIL_NOTIFICATION_INVITE_COLLEAGUE');
        $settings = DB::select("SELECT * FROM tenant_setting as t WHERE t.key='$emailNotificationInviteColleague'"); 
        DB::setDefaultConnection('tenant');        
        $tenantSetting1 = App\Models\TenantSetting::create(['setting_id' =>$settings[0]->tenant_setting_id]);
        App\Models\TenantActivatedSetting::create(['tenant_setting_id' =>$tenantSetting1->tenant_setting_id]);

        DB::setDefaultConnection('mysql');
        $missionCommentAutoApproved = config('constants.tenant_settings.MISSION_COMMENT_AUTO_APPROVED');
        $settings = DB::select("SELECT * FROM tenant_setting as t WHERE t.key='$missionCommentAutoApproved'"); 
        DB::setDefaultConnection('tenant');        
        $tenantSetting2 = App\Models\TenantSetting::create(['setting_id' =>$settings[0]->tenant_setting_id]);
        App\Models\TenantActivatedSetting::create(['tenant_setting_id' =>$tenantSetting2->tenant_setting_id]);

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
        $tenantSetting1->delete();
        $tenantSetting2->delete();
    }
}