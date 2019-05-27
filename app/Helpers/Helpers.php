<?php

namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class Helpers
{
    public static function getSubDomainFromUrl(Request $request)
    {
    	try{    		
        	return parse_url($request->headers->all()['referer'][0])['host'];
        } catch (\Exception $e) {
        	return $e->getMessage();
        }
    }   
}
