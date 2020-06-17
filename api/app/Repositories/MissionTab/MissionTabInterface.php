<?php
namespace App\Repositories\MissionTab;

use Illuminate\Http\Request;

interface MissionTabInterface
{
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @return array
     */
    public function store(Request $request, int $missionId);

        /**
     * Store a newly created resource into database
     *
     * @param array $missionTabValue
     * @param int $missionId
     * @return array
     */
    public function update(array $missionTabValue, int $missionId);

}