<?php
namespace App\Repositories\MissionUnitedNationSDG;

use Illuminate\Http\Request;

interface MissionUnitedNationSDGInterface
{
    /**
     * Add UN SDG to mission.
     *
     * @param int $missionId
     * @return Illuminate\Support\Collection
     */
    public function addUnSdg(int $missionId, Request $request);

    /**
     * Update UN SDG to mission.
     *
     * @param int $missionId
     * @return Illuminate\Support\Collection
     */
    public function updateUnSdg(int $missionId, Request $request);
}
