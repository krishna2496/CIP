<?php
namespace App\Repositories\MissionTab;

use Illuminate\Http\Request;

interface MissionTabInterface
{
    /**
     * Store a newly created resource into database
     *
     * @param array $missionTabValue
     * @param int $missionId
     * @return array
     */
    public function store(array $missionTabValue, int $missionId);

        /**
     * Store a newly created resource into database
     *
     * @param array $missionTabValue
     * @param int $missionId
     * @return array
     */
    public function update(array $missionTabValue, int $missionId);

}