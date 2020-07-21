<?php
namespace App\Repositories\UnitedNationSDG;

use App\Models\UnitedNationSDG;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\MissionUnSdg;

class UnitedNationSDGRepository implements UnitedNationSDGInterface
{
    /**
     * @var App\Models\MissionUnSdg;
     */
    private $missionUnSdg;

    /**
     * Create a new United Nation SDG repository instance.
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
     * Display a listing of the United Nation SDG.
     *
     * @param App\Models\UnitedNationSDG
     */
    public function find(): Collection
    {
        $return = [];
        $allUnSdg = config('constants.UN_SDG');
        foreach ($allUnSdg as $key => $value) {
            $return[] = new UnitedNationSDG($key, $value);
        }
        return collect($return);
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
