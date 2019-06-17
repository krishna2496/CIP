<?php

namespace App\Repositories\User;

use Illuminate\Http\Request;

interface UserInterface {

	// public function save(array $data);
    
    public function store(Request $request);
	
	public function update(Request $request, int $id);
	
	public function userList(Request $request);

    public function find(int $id);
	
    public function delete(int $id);
	
}
