<?php
namespace App\Repositories\TenantSetting;

use Illuminate\Http\Request;

interface TenantSettingInterface
{

    /**
     * Update setting value
     *
     * @param  array $data
     * @return void
     */
    public function updateSetting(array $data, int $settingId);

    /**
     *  Get all setting
     *
     * @return void
     */
    public function getAllSettings();
}
