<?php

namespace App\Repositories\Tenant;
use Illuminate\Http\Request;

interface TenantInterface {

	// public function save(array $data);
    
	public function tenantList(Request $request);

    public function find($id);
	
    public function store(Request $request);

    public function delete($id);
	
}
