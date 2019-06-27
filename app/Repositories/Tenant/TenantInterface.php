<?php
namespace App\Repositories\Tenant;

use Illuminate\Http\Request;

interface TenantInterface
{
    /**
     * Store a new resource.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function store(Request $request);

    /**
     * Update resource.
     *
     * @param  array $requestarray
     * @param  int $id
     * @return void
     */
    public function update(array $requestarray, int $id);

    /**
     * Listing of a all resources.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function tenantList(Request $request);

    /**
     * Find a specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function find(int $id);

    /**
     * Delete a specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function delete(int $id);
}
