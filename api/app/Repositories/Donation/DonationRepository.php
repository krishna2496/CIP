<?php

namespace App\Repositories\Donation;

use App\Libraries\Amount;
use App\Models\Donation;

class DonationRepository
{
    /**
     * @var App\Models\Donation
     */
    private $donation;

    /**
     * Creates donation repository instance
     *
     * @param Donation $donation
     *
     * @return void
     */
    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    /**
     * Create donation record
     * @param Donation $donation
     *
     * @return Donation
     */
    public function create(Donation $donation): Donation
    {
        return $this->donation->create($donation->getAttributes());
    }

    /**
     * Get total donations amount by mission id
     * @param int $missionId
     *
     * @return Amount $amount
     */
    public function getMissionTotalDonationAmount(int $missionId)
    {
        // TODO: consider different currencies and exchange rate conversions
        $amount = $this->donation
            ->join('payment', 'payment.id', '=', 'donation.payment_id')
            ->where('donation.mission_id', '=', $missionId)
            ->where('payment.status', config('constants.payment_statuses.SUCCESS'))
            ->sum('payment.transfer_amount');

        return new Amount($amount);
    }
}