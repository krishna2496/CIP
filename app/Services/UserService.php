<?php

namespace App\Services;

use App\Repositories\User\UserRepository;
use App\User;

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

}
