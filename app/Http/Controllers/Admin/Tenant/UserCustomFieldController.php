<?php

namespace App\Http\Controllers\Admin\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\UserCustomField;
use App\Helpers\Helpers;
use Validator;

class UserCustomFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
        {  
           "name":"age",
           "type":"dropdown",
           "is_mandatory":"1",
           "translation":{  
              "name":"Name of the drop-down",
              "values":"['option 1','option 2','option 3']",
              "translations":[  
                 {  
                    "lang":"fr",
                    "name":"French Name of the drop-down",
                    "values":"['fr option 1','fr option 2','fr option 3']"
                 },
                 {  
                    "lang":"de",
                    "name":"German Name of the drop-down",
                    "values":"['de option 1','de option 2','de option 3']"
                 }
              ]
           }
        }
        */

        // get request
        // check valid or not
        // validate data with type - dropdown/email/text/...
        //

        dd($request->toArray());
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
