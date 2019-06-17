<?php

namespace App\Repositories\Tenant;
use Illuminate\Http\Request;

interface TenantInterface {

	// public function save(array $data);
    
    public function store(Request $request);
	
	 public function update(Request $request, int $id);
	
	public function tenantList(Request $request);

    public function find(int $id);
	
    public function delete(int $id);
	
}
