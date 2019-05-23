<?php

namespace App\Http\Controllers;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\TenantOption;
use DB;

class ConnectionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Return style settings.
        $data = $dataResponse = array(); 

        //flag to check value is serialize or not
        $checkForSerialize = FALSE;

        // find custom data
        $tenantOptions = TenantOption::get(['option_name', 'option_value'])->where('deleted_at', NULL);
        $data = $tenantOptions->toArray();

        //if data exist
        if ($data) {

            foreach ($data as $key =>$value) {

                //check if value is serialize or not
                $checkForSerialize = @unserialize($value['option_value']);

                if ($checkForSerialize === FALSE) {      // if not serialize value
                    $dataResponse[$value['option_name']] = $value['option_value'];
                } else {                                 // for serialize value
                    $dataResponse[$value['option_name']] = unserialize($value['option_value']);
                }

            }
        }

        $this->apiData = $dataResponse;
        $this->apiStatus = app('Illuminate\Http\Response')->status();
        $this->apiMessage = 'Tenant options listing successfully';
        return $this->response();

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
