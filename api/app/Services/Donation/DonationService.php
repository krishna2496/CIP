<?php

namespace App\Services\Donation;

use App\Libraries\Amount;
use App\Models\Donation;
use App\Repositories\Donation\DonationRepository;

class DonationService
{
    /**
     * @var App\Repositories\Donation\DonationRepository
     */
    private $donationRepository;

    /**
     * Creates a new Donation service instance
     *
     * @param DonationRepository $donationRepository
     *
     * @return void
     */
    public function __construct(DonationRepository $donationRepository)
    {
        $this->donationRepository = $donationRepository;
    }

    /**
     * Creates a donation record
     *
     * @param Donation $donation
     *
     * @return Donation
     */
    public function create(Donation $donation): Donation
    {
        return $this->donationRepository->create($donation);
    }

    /**
     * Get total donations amount by mission id
     *
     * @param int $missionId
     *
     * @return \App\Libraries\Amount
     */
    public function getMissionTotalDonationAmount(int $missionId): Amount
    {
        return $this->donationRepository->getMissionTotalDonationAmount($missionId);
    }
}