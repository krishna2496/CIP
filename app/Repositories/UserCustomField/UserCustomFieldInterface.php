<?php
namespace App\Repositories\UserCustomField;

use Illuminate\Http\Request;

interface UserCustomFieldInterface
{
    public function store(array $request);

    public function update(array $request, int $id);

    public function userCustomFieldList(Request $request);

    public function find(int $id);
    
    public function delete(int $id);
}
