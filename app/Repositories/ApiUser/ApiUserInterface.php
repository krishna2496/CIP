<?php
namespace App\Repositories\ApiUser;

use Illuminate\Http\Request;

interface ApiUserInterface
{
    /**
     * Store a new resource.
     *
     * @param  int $id
     * @param  array $apiKeys
     * @return void
     */
    public function store(int $id, array $apiKeys);

    /**
     * Update resource.
     *
     * @param  int $tenantId
     * @param  int $id
     * @param  string $apiSecret
     * @return void
     */
    public function update(int $tenantId, int $id, string $apiSecret);

    /**
     * Listing of a all resources.
     *
     * @param  int $tenantId
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
