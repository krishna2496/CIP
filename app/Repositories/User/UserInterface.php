<?php
namespace App\Repositories\User;

use Illuminate\Http\Request;
use App\User;
use App\Models\UserCustomFieldValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface
{
    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @return App\User
     */
    public function store(array $request): User;
    
    /**
     * Update the specified resource in storage.
     *
     * @param  array  $request
     * @param  int  $id
     * @return App\User
     */
    public function update(array $request, int $id): User;
    
    /**
     * Get listing of users
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function userList(Request $request): LengthAwarePaginator;

    /**
     * Find specified resource in storage.
     *
     * @param  int  $id
     * @return App\User
     */
    public function find(int $id): User;
    
    /**
     * Remove specified resource in storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Display a listing of specified resources.
     *
     * @param int $userId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function userSkills(int $userId): Collection;

    /**
     * Store a newly created resource into database
     *
     * @param array $request
     * @param int $id
     * @return bool
     */
    public function linkSkill(array $request, int $id): bool;

    /**
     * Remove the specified resource from storage
     *
     * @param array $request
     * @param int $id
     * @return bool
     */
    public function unlinkSkill(array $request, int $id): bool;

    /**
     * List all the users
     *
     * @param int $userId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function listUsers(int $userId) : Collection;

    /**
     * Search user
     *
     * @param string $text
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function searchUsers(string $text = null, int $userId): Collection;

    /**
    * Add/Update user custom field value.
    *
    * @param array $userCustomFields
    * @param int $id
    * @return null|App\Models\UserCustomFieldValue
    */
    public function updateCustomFields(array $userCustomFields, int $id): ?UserCustomFieldValue;

    /**
     * Delete skills by userId
     *
     * @param int $userId
     * @return bool
     */
    public function deleteSkills(int $userId): bool;
    
    /**
     * Change user password
     *
     * @param int $id
     * @param string $password
     *
     * @return bool
     */
    public function changePassword(int $id, string $password): bool;

    /**
     * Get user goal hours
     *
     * @param int $userId
     * @return null|int
     */
    public function getUserGoalHours(int $userId): ?int;

    /**
     * Update cookie agreement date
     *
     * @param int $userId
     * @return bool
     */
    public function updateCookieAgreement(int $userId): bool;
}
