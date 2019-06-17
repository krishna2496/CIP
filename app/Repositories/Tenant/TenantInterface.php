<?php

namespace App\Repositories\Tenant;
use Illuminate\Http\Request;

interface TenantInterface {

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
