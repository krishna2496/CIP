<?php

namespace App\Http\Controllers\App\Styling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\TenantOption;
use DB;

class StylingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Return style settings
        $tenantData = $responseData = array(); 

        //flag to check value is serialize or not
        $isSerialize = true;

        // find tenant options data from table `tenant_option`
        $tenantData = TenantOption::get(['option_name', 'option_value'])
					->where('deleted_at', NULL)
					->toArray();

        // If tenant data exist
        if ($tenantData) {
			foreach ($tenantData as $key =>$value) {
                //check if value is serialize or not
                $isSerialize = @unserialize($value['option_value']);
				$responseData[$value['option_name']] = $value['option_value'];
                if ($isSerialize == true) {
                    $responseData[$value['option_name']] = unserialize($value['option_value']);
				}
			}
        }

        $apiData = $responseData;
        $apiStatus = app('Illuminate\Http\Response')->status();
        return Helpers::response($apiStatus, '', $apiData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
