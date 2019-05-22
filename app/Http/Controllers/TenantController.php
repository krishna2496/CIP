<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\ApiController;
use App\Jobs\TenantMigrationJob;
use App\Tenant;
use Illuminate\Http\Request;
use Validator;

class TenantController extends ApiController
{
    /**
     * Display a listing of the tenants.
     *
     * @return mixed
     */
    public function index()
    {
        // Get request parameter from URL
        $search_string = Input::get('search','');
        $order_type = Input::get('order','asc');

        // Create basic query for tenant list
        $tenant_query = Tenant::select('tenant_id','name','created_at')
        ->whereNull('deleted_at');

        // Check if search parameter passed in URL then search parameter will search in name field of tenant table.
        if (!empty($search_string)) {
            $tenant_query->where('name', 'like', '%' . $search_string . '%');
        }

        // Order by passed order or default order asc.
        $tenants = $tenant_query->orderBy('tenant_id',$order_type)->paginate(10);

        if (count($tenants)>0) {
            // Set response data
            $this->apiData = $tenants;
            $this->apiStatus = app('Illuminate\Http\Response')->status();
            $this->apiMessage = "Tenant listing successfully";
        } else {
            // Set response data                        
            $this->apiStatus = app('Illuminate\Http\Response')->status();
            $this->apiMessage = "No data found";
        }

        // Send API reponse
        return $this->response();
    }

    /**
     * Store a newly created tenant into database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {        
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
            'name' => 'required',
            'sponsor_id'  => 'required',
        ]);

        // If request parameter have any error
        if ($validator->fails()) {

            $this->errorType = config('errors.type.ERROR_TYPE_422');
            $this->apiStatus = 422;
            $this->apiErrorCode = 10001;
            $this->apiMessage = $validator->errors()->first();
            return $this->errorResponse();
        }

        try {

            $created_tenant = Tenant::create($request->toArray());

            // Set response data
            $this->apiCode    = app('Illuminate\Http\Response')->status();
            $this->apiData    = ['tenant_id' => $created_tenant->tenant_id];
            $this->apiMessage = "Tenant created successfully";

            // Job dispatched for to create tenant's database and migrations
            dispatch(new TenantMigrationJob($created_tenant));

            return $this->response();

        } catch (\Exception $e) {            
            // Error for duplicate tenant name, trying to store in database.
            if (isset($e->errorInfo[1]) && $e->errorInfo[1]==1062) {
                $this->errorType  = config('errors.code.10002');
                $this->apiErrorCode = 10002;
                $this->apiStatus  = 422;
                $this->apiMessage = "Tenant name is already taken, Please try with different name.";
            } else { // Any other error occured when trying to insert data into database for tenant.
                $this->errorType  = config('errors.code.10006');
                $this->apiErrorCode = 10006;
                $this->apiStatus  = 422;
                $this->apiMessage = $e->getMessage();
            }
            return $this->errorResponse();
        }
    }

    /**
     * Display specific tenant details.
     *
     * @param  int  $id
     * @return mixed
     */
    public function show($tenant_id)
    {
        // Find tenant from database based on passed tenant id.
        $tenant_details = Tenant::select('tenant_id','name','sponsor_id','created_at')->find($tenant_id);

        // Check tenant found or not
        if ($tenant_details) {
            $this->apiStatus = 200;
            $this->apiData   = $tenant_details;
            $this->apiMessage = "Tenant found successfully";
        } else {               
            $this->errorType = config('errors.code.10004');
            $this->apiErrorCode = 10004;
            $this->apiStatus  = 404;
            $this->apiMessage = "Tenant not found";
        }

        return $this->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
