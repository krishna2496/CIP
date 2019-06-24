<?php
namespace App\Repositories\User;

use Illuminate\Http\Request;

interface UserInterface
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
     * Listing of all resources.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function userList(Request $request);

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
     * Display a listing of the resource.
     *
     * @param  Illuminate\Http\Request $userId
     * @return void
     */
    public function userSkills(int $userId);

    /**
     * Store a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function linkSkill(Request $request);

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function unlinkSkill(Request $request);
}
