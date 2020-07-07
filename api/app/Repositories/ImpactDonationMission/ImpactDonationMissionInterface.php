<?php
namespace App\Repositories\ImpactDonationMissionInterface;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ImpactDonationMissionInterface
{
     /**
     * Store a newly created resource into database
     *
     * @param array $missionDonationValue
     * @param int $missionId
     * @return void
     */
    public function store(array $missionDonationValue, int $missionId);

        /**
     * Store a newly created resource into database
     *
     * @param array $missionDonationValue
     * @param int $missionId
     * @return void
     */
    public function update(array $missionDonationValue, int $missionId);
}
