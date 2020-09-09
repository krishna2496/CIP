<?php

namespace App\Services;

use App\Exceptions\MaximumUsersReachedException;
use App\Models\TenantOption;
use App\Repositories\User\UserRepository;
use App\Services\TenantOptionService;
use App\User;
use DB;
use Exception;

class UserService
{
    /**
     * @var App\Helpers\Helpers
     */
    private $userRepository;

    /**
     * @var  TenantOptionService
     */
    private $tenantOptionService;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository       $userRepository
     * @param  TenentOptionService  $tenentOptionService
     *
     * @return  void
     */
    public function __construct(
        UserRepository $userRepository,
        TenantOptionService $tenantOptionService
    ) {
        $this->userRepository = $userRepository;
        $this->tenantOptionService = $tenantOptionService;
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
     * Store a newly created resource in storage.
     *
     * @param  array
     *
     * @return  User
     *
     * @throws  MaximumUsersReachedException
     */
    public function store(array $request): User
    {
        $maxUsers = $this->tenantOptionService->getOptionValueFromOptionName(TenantOption::MAXIMUM_USERS);
        if ($maxUsers && $maxUsers->option_value >= 0) {
            $userCount = $this->getUserCount(true);
            if ($userCount >= $maxUsers->option_value) {
                throw new MaximumUsersReachedException(
                    trans('messages.ERROR_MAXIMUM_USERS_REACHED'),
                    config('constants.error_codes.ERROR_MAXIMUM_USERS_REACHED')
                );
            }
        }

        return $this->userRepository->store($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Array
     * @param  int
     *
     * @return  User
     */
    public function update(Array $request, int $id): User
    {
        return $this->userRepository->update($request, $id);
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
     * Get user's volunteer summary
     *
     * @param App\User $user
     * @param Array $params all get parameteres
     *
     * @return Array
     */
    public function volunteerSummary($user, $params = null): Array
    {
        $data = $this->userRepository->volunteerSummary($user, $params);
        $summary = $data
            ->first()
            ->toArray();

        $missionCount = $this->getUserMissionCount($user, $params);
        $favoriteMission = $this->getUserFavoriteMission($user, $params);

        return array_merge($summary, $missionCount, $favoriteMission);
    }

    /**
     * Get user's missions
     *
     * @param App\User $user
     * @param Array $params all get parameteres
     *
     * @return Array
     */
    private function getUserMissionCount($user, $params): Array
    {
        $mission = $this->userRepository->getMissionCount($user, $params);
        return $mission->first()->toArray();
    }

    /**
     * Get user's favorite missions
     *
     * @param App\User $user
     * @param Array $params all get parameteres
     *
     * @return Array
     */
    private function getUserFavoriteMission($user, $params): Array
    {
        $favorite = $this->userRepository->getFavoriteMission($user, $params);
        return $favorite->first()->toArray();
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

    /**
     * Get the number of users.
     *
     * @param  bool
     *
     * @return  int
     */
    public function getUserCount(
        bool $includeInactive = false,
        bool $includeAdmin = false,
    ): int {
        return $this->userRepository->getUserCount(
            $includeInactive,
            $includeAdmin
        );
    }
}
