<?php
namespace App\Repositories\ApiUser;

use Illuminate\Http\Request;

interface ApiUserInterface
{
    /**
     * Store a new resource.
     *
     * @param  int $id
     * @return void
     */
    public function store(int $id);

    /**
     * Update resource.
     *
     * @param  int $tenantId
     * @param  int $id
     * @return void
     */
    public function update(int $tenantId, int $id);

    /**
     * Listing of a all resources.
     *
     * @return void
     */
    public function apiUserList(int $tenantId);

    /**
     * Find a specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function findApiUser(int $id);

    /**
     * Delete a specified resource.
     * @param  int $tenantId
     * @param  int $id
     * @return void
     */
    public function delete(int $tenantId, int $id);
}
