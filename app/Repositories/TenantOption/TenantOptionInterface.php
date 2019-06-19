<?php
namespace App\Repositories\TenantOption;

use Illuminate\Http\Request;

interface TenantOptionInterface
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
     * @param  Illuminate\Http\Request $request
     * @param  int $id
     * @return void
     */
    public function update(Request $request, int $id);
    
    /**
     * Listing of a all resources.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function tenantOptionList(Request $request);

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

    /**
     * Store tenant slider data.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function storeSlider(Request $request);

    /**
     * Store tenant slider data.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function resetStyleSettings(Request $request);

    /**
     * Store tenant slider data.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function updateStyleSettings(Request $request);
}
