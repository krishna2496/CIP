<?php
namespace App\Repositories\UserCustomField;

use Illuminate\Http\Request;

interface UserCustomFieldInterface {

	public function store(Request $request);
	
	public function update(Request $request, int $id);
	
	public function UserCustomFieldList(Request $request);

    public function find(int $id);
	
    public function delete(int $id);
	
}
