<?php
namespace App\Repositories\MissionImpact;

interface MissionImpactInterface
{
     /**
     * Store a newly created resource into database
     *
     * @param array $missionImpact
     * @param int $missionId
     * @param int $defaultTenantLanguageId
     * @return void
     */
    public function store(array $missionImpact, int $missionId, int $defaultTenantLanguageId);

    /**
     * Update a resource into database
     *
     * @param array $missionImpact
     * @param int $missionId
     * @return void
     */
    // public function update(array $missionImpact, int $missionId);
}