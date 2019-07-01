<?php
namespace App\Repositories\FooterPage;

use Illuminate\Http\Request;

interface FooterPageInterface
{
    /**
     * Store a new resource.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
	public function store(Request $request);
	
	/**
     * Find a specified resource.
     *
     * @param  int $id
     * @return void
     */
	public function find(int $id);
    
	/**
     * Update resource.
     *
     * @param  Illuminate\Http\Request $request
     * @param  int $id
     * @return void
     */
    public function update(Request $request, int $id);
    
	/**
     * Get page listing.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function footerPageList(Request $request);
    
	/**
     * Delete specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function delete(int $id);
}
