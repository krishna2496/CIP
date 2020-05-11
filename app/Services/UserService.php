<?php

namespace App\Services;

use App\Repositories\User\UserRepository;
use App\User;
use DB;

class UserService
{
    /**
     * @var App\Helpers\Helpers
     */
    private $userRepository;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @return void
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * Get specific user
     *
     * @param Int $userId 
     *
     * @return App\User
     */
    public function findById($userId): User
    {
        return $this->userRepository->find($userId);
    }

    /**
     * Get specific user content statistics
     *
     * @param App\User $user
     * @param Array $params all get parameteres
     *
     * @return Array
     */
    public function statistics($user, $params = null): Array
    {
        // get all the timesheet stasts
        $stats = $this->userRepository->getStatistics($user, $params);

        $data = $stats
            ->first()
            ->toArray();

        // Add organization count
        $data['organization_count'] = $this->organizationCount($user, $params);

        return $data;
    }

    /**
     * Get specific user organization count
     *
     * @param App\User $user
     * @param Array $params all get parameteres
     *
     * @return Array
     */
    public function organizationCount($user, $params = null): Int
    {
        $organization = $this->userRepository->getOrgCount($user, $params);
        
        return $organization
            ->pluck('organization_count')
            ->first();
    }

}
