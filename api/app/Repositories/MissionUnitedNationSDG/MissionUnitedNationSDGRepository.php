<?php
namespace App\Repositories\MissionUnitedNationSDG;

use App\Models\UnitedNationSDG;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\MissionUnSdg;

class MissionUnitedNationSDGRepository implements MissionUnitedNationSDGInterface
{
    /**
     * @var App\Models\MissionUnSdg;
     */
    private $missionUnSdg;

    /**
     * Create a new Mission United Nation SDG repository instance.
     *
     * @param  App\Models\MissionUnSdg $missionUnSdg
     * @return void
     */
    public function __construct(
        MissionUnSdg $missionUnSdg
    ) {
        $this->missionUnSdg = $missionUnSdg;
    }

    /**
     * Add UN SDG to mission.
     *
     * @param int $missionId
     * @param Illuminate\Http\Request $request
     */
    public function addUnSdg(int $missionId, Request $request)
    {
        foreach ($request->un_sdg as $key => $value) {
            $this->missionUnSdg->create([
                "mission_id" => $missionId,
                "un_sdg_number" => $value
            ]);
        }
    }

    /**
     * Update UN SDG to mission.
     *
     * @param int $missionId
     * @param Illuminate\Http\Request $request
     */
    public function updateUnSdg(int $missionId, Request $request)
    {
        // delete all mission associated UN SDG
        $this->missionUnSdg->where('mission_id', $missionId)->delete();
        // update new UN SDG for mission
        foreach ($request->un_sdg as $key => $value) {
            $this->missionUnSdg->create([
                "mission_id" => $missionId,
                "un_sdg_number" => $value
            ]);
        }
    }
}
