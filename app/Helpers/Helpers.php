<?php

namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class Helpers
{
    public static function getSubDomainFromUrl(Request $request)
    {
    	try{    	
        	return explode(".",parse_url($request->headers->all()['referer'][0])['host'])[0];
        } catch (\Exception $e) {
        	return $e->getMessage();
        }
    }   
}
