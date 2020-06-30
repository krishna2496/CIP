<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Aws\S3\Exception\S3Exception;
use App\Models\ApiUser;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Models\TenantHasSetting;

class TenantSettingTest extends TestCase
{
    /**
     * @test
     *
     * List of all settings of tenant with their status
     * @return void
     */
    public function it_should_list_all_settings_for_tenant()
    {
        $tenant = Tenant::get()->random();
        
        $this->get(route('tenants.settings', ['tenantId' => $tenant->tenant_id]))
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'data' => [
                ['*' =>
                    'title',
                    'tenant_setting_id',
                    'description',
                    'key',
                    'is_active'
                ]
            ],
            'message',
        ]);
    }
    
    /**
     * @test
     *
     * List of all settings of tenant with their status, while tenant not found
     * @return void
     */
    public function it_should_return_tenant_not_found_for_tenant_setting()
    {
        // Generate random tenantId
        $tenantId = rand(99999999, 999999999);
        
        $this->get(route('tenants.settings', ['tenantId' => $tenantId]))
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * Add tenant setting
     * @return void
     */
    public function it_should_add_setting_for_tenant()
    {
        $donationRelatedSettingsArray = config('constants.DONATION_RELATED_SETTINGS');
        $donationRelatedIds = [];
        $tenantSettingIds = [];

        // Get donation setting id
        $donationSettingData = TenantSetting::where('key', '=', 'donation')->get()->toArray();
        $donationSettingId = $donationSettingData[0]['tenant_setting_id'];
        array_push($donationRelatedIds, $donationSettingId);

        // Donation related setting data
        $donationRelatedSettingIds = $this->getParamsArray($donationRelatedSettingsArray);
        foreach ($donationRelatedSettingIds['settings'] as $value) {
            array_push($donationRelatedIds, $value['tenant_setting_id']);
        }

        // All tenant setting data
        foreach (TenantSetting::get()->toArray() as $value) {
            array_push($tenantSettingIds, $value['tenant_setting_id']);
        }

        $TenantSettingIdsWithoutDonation = array_diff($tenantSettingIds, $donationRelatedIds);

        // Create random settings array
        for ($i=0; $i<30; $i++) {
            $params['settings'][$i] = [
                'tenant_setting_id' => $TenantSettingIdsWithoutDonation[array_rand($TenantSettingIdsWithoutDonation)],
                'value' => 1,
            ];
        }
            
        // Add settings into tenant_has_setting (master database) and tenant_setting in (tenant's database)
        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(200);

        // Make disable settings
        foreach ($params['settings'] as $key => $param) {
            $params['settings'][$key] = [
                'tenant_setting_id' => $param['tenant_setting_id'],
                'value' => 0
            ];
        }
        // And again call setting API with 0 value to delete it
        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(200);
    }

    /**
     * @test
     *
     * Add tenant setting, error if enter wrong tenant_setting_id
     * @return void
     */
    public function it_should_return_error_if_wrong_tenant_setting_id()
    {
        // Create random settings array
        for ($i=0; $i<3; $i++) {
            $params['settings'][$i] = [
                'tenant_setting_id' => rand(9999999, 999999999),
                'value' => 1,
            ];
        }
        // Add settings into tenant_has_setting (master database) and tenant_setting in (tenant's database)
        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Add tenant setting, error if enter wrong value
     * @return void
     */
    public function it_should_return_error_if_wrong_value()
    {
        // Create random settings array
        for ($i=0; $i<3; $i++) {
            $params['settings'][$i] = [
                'tenant_setting_id' => TenantSetting::get()->random()->tenant_setting_id,
                'value' => rand(9999999, 999999999),
            ];
        }
        // Add settings into tenant_has_setting (master database) and tenant_setting in (tenant's database)
        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Add tenant setting, error if enter value is blank
     * @return void
     */
    public function it_should_return_error_if_value_is_blank()
    {
        // Create random settings array
        for ($i=0; $i<3; $i++) {
            $params['settings'][$i] = [
                'tenant_setting_id' => TenantSetting::get()->random()->tenant_setting_id,
                'value' => '',
            ];
        }
        // Add settings into tenant_has_setting (master database) and tenant_setting in (tenant's database)
        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Add tenant setting, error if enter tenant_setting_id is blank
     * @return void
     */
    public function it_should_return_error_if_tenant_setting_id_is_blank()
    {
        // Create random settings array
        for ($i=0; $i<3; $i++) {
            $params['settings'][$i] = [
                'tenant_setting_id' => '',
                'value' => 1,
            ];
        }
        // Add settings into tenant_has_setting (master database) and tenant_setting in (tenant's database)
        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should return all settings
     * @return void
     */
    public function it_should_return_all_settings()
    {
        $this->get(route('settings'))->seeStatusCode(200);
    }

    /**
     * @test
     *
     * It should return tenant not found error on add tenant setting
     * @return void
     */
    public function it_should_return_error_tenant_not_found_on_add_setting_for_tenant()
    {
        // Create random settings array
        for ($i=0; $i<3; $i++) {
            $params['settings'][$i] = [
                'tenant_setting_id' => TenantSetting::get()->random()->tenant_setting_id,
                'value' => 1,
            ];
        }
        // Add settings into tenant_has_setting (master database) and tenant_setting in (tenant's database)
        $this->post(route('tenants.store.settings', ['tenantId' => rand(500000, 5000000)]), $params)
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * It should return donation setting disable if donation setting is already enable else return error message
     * @return void
     */
    public function it_should_disable_all_donation_related_settings()
    {

        // Set Doantion setting disable
        $donationSettingData = TenantSetting::get()->where('key', '=', 'donation')->toArray();
        foreach ($donationSettingData as $donationValue) {
            $donationSettingId = $donationValue['tenant_setting_id'];
        }

        $donationHasSetting = TenantHasSetting::where(['tenant_setting_id' => $donationSettingId, 'tenant_id' => env('DEFAULT_TENANT_ID')])
        ->whereNull('deleted_at')->get()->toArray();
        $donationRelatedSettingsArray = config('constants.DONATION_RELATED_SETTINGS');

        // Create donation related setting array
        $params = $this->getParamsArray($donationRelatedSettingsArray);
        $params['settings'][0] = [
            'tenant_setting_id' => $donationSettingId,
            'value' => '0',
        ];

        if (!empty($donationHasSetting)) {
            $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
            ->seeStatusCode(200);
        } else {
            $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
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

    /**
     * @test
     *
     * Change donation related setting if donation setting is enable
     * @return void
     */
    public function it_should_enable_or_disable_donation_related_setting_if_donation_setting_is_enable()
    {

        // Set Doantion setting disable
        $donationSettingData = TenantSetting::get()->where('key', '=', 'donation')->toArray();
        foreach ($donationSettingData as $donationValue) {
            $donationSettingId = $donationValue['tenant_setting_id'];
        }
        $donationRelatedSettingsArray = config('constants.DONATION_RELATED_SETTINGS');

        // Create donation related setting array
        $params = $this->getParamsArray($donationRelatedSettingsArray);

        $params['settings'][0] = [
            'tenant_setting_id' => $donationSettingId,
            'value' => '1',
        ];

        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(200);
    }

    /**
     * @test
     *
     * Return error if donation setting is already disable and donation related setting try to get enable
     * @return void
     */
    public function it_should_try_to_change_donation_related_setting_while_donation_setting_already_disable()
    {

        // Set Doantion setting disable
        $donationSettingData = TenantSetting::get()->where('key', '=', 'donation')->toArray();
        foreach ($donationSettingData as $donationValue) {
            $donationSettingId = $donationValue['tenant_setting_id'];
        }

        $donationHasSetting = TenantHasSetting::where(['tenant_setting_id' => $donationSettingId, 'tenant_id' => env('DEFAULT_TENANT_ID')])
        ->whereNull('deleted_at')->get()->toArray();
        $donationRelatedSettingsArray = config('constants.DONATION_RELATED_SETTINGS');

        $params['settings'][0] = [
            'tenant_setting_id' => $donationSettingId,
            'value' => '0',
        ];

        if (!empty($donationHasSetting)) {
            $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
            ->seeStatusCode(200);
            $params = $this->getParamsArray($donationRelatedSettingsArray);
        }

        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
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
     * Donation setting is enable and donation related setting try to make enable
     * @return void
     */
    public function donation_setting_enable_and_donation_related_setting_try_to_make_enable()
    {

        // Set Doantion setting disable
        $donationSettingData = TenantSetting::get()->where('key', '=', 'donation')->toArray();
        foreach ($donationSettingData as $donationValue) {
            $donationSettingId = $donationValue['tenant_setting_id'];
        }

        $donationRelatedSettingsArray = config('constants.DONATION_RELATED_SETTINGS');
        $params['settings'][0] = [
            'tenant_setting_id' => $donationSettingId,
            'value' => '1',
        ];

        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(200);

        $params = $this->getParamsArray($donationRelatedSettingsArray);
        $this->post(route('tenants.store.settings', ['tenantId' => env('DEFAULT_TENANT_ID')]), $params)
        ->seeStatusCode(200);
    }

    /*
    -----------------------------------------------------------------------------------------

    Helper and common functions

    -----------------------------------------------------------------------------------------
    */

    /**
     * Set parameter for donation related setting
     *
     * @param $donationRelatedSettingsArray
     * @return array
     */
    public function getParamsArray($donationRelatedSettingsArray) : array
    {
        $i = 1;
        foreach ($donationRelatedSettingsArray as $value) {
            $settingIdDetails = TenantSetting::get()->where('key', '=', $value)->toArray();
            foreach ($settingIdDetails as $settingValue) {
                $settingId =  $settingValue['tenant_setting_id'];
            }
            
            $params['settings'][$i] = [
                'tenant_setting_id' => $settingId,
                'value' => rand(0, 1)
            ];
            $i++;
        }
        return $params;
    }
}
