<?php

namespace App\Repositories\DonationIp;

use App\Models\DonationIpWhitelist;

class WhitelistRepository
{
    /**
    * @var DonationIpWhitelist: Model
    */
    private $donationIpWhitelist;

    /**
     * Create a new controller instance.
     *
     * @param DonationIpWhitelist $donationIpWhitelist
     *
     * @return void
     */
    public function __construct(DonationIpWhitelist $donationIpWhitelist)
    {
        $this->donationIpWhitelist = $donationIpWhitelist;
    }

    /**
     * Get whitelisted by id
     *
     * @param array $id
     *
     * @return DonationIpWhitelist
     */
    public function findById($id)
    {
        return $this->donationIpWhitelist->findOrFail($id);
    }

    /**
     * Get list of whitelisted Ips
     *
     * @param array $paginate
     *              $paginate['perPage'] Item limit count per page
     * @param array $filters
     *              $filters['search'] Search for pattern or description
     *
     * @return Object
     */
    public function getList($paginate, $filters)
    {
        return $this->donationIpWhitelist
            ->select(
                'id',
                'pattern',
                'description'
            )
            ->when($filters['search'], function($query) use ($filters) {
                $keyword = $filters['search'];
                $query->where('pattern', 'like', "%$keyword%")
                    ->orWhere('description', 'like', "%$keyword%");
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($paginate['perPage']);
    }

    /**
     * Create whitelisted Ip
     *
     * @param DonationIpWhitelist $whitelistIp
     *
     * @return DonationIpWhitelist
     */
    public function create(DonationIpWhitelist $whitelistIp)
    {
        return $this->donationIpWhitelist->create(
            $whitelistIp->toArray()
        );
    }

    /**
     * Update whitelisted Ip
     *
     * @param string $id
     * @param DonationIpWhitelist $whitelistIp
     *
     * @return bool
     */
    public function update(DonationIpWhitelist $whitelistIp)
    {
        return $this->donationIpWhitelist
            ->find($whitelistIp->id)
            ->update([
                'pattern' => $whitelistIp->pattern,
                'description' => $whitelistIp->description
            ]);
    }

    /**
     * Delete whitelisted Ip
     *
     * @param string $id
     *
     * @return bool
     */
    public function delete($id)
    {
        return $this->donationIpWhitelist
            ->find($id)
            ->delete();
    }

}