<?php
namespace App\Repositories\MissionImpact;

use App\Repositories\MissionImpact\MissionImpactInterface;
use App\Models\MissionApplication;
use App\Models\MissionDocument;

class MissionImpactRepository implements MissionImpactInterface
{
    public function __construct(){

    }

    /**
     * Save impact mission details
     * 
     * @param array $impactMission
     * @param int $missionId
     * @return void
     */
    public function store(array $impactMission, int $missionId){

    }


}