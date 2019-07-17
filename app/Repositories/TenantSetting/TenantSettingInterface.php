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
     * @param Illuminate\Http\Request $request
     * @return void
     */
    public function getAllSettings(Request $request);
}
