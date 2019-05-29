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

    /**
     * get referer url 
     *  
     * @param Request $request
     * @return string
     */
 	public static function getRefererFromRequest(Request $request)
    {
    	try{
    		if(isset($request->headers->all()['referer'])){    	
        		$parseUrl = parse_url($request->headers->all()['referer'][0]);
        		return $parseUrl['scheme'].'://'.$parseUrl['host'].':'.$parseUrl['port'];
        	}else{
        		return env('APP_MAIL_BASE_URL');
        	}
        } catch (\Exception $e) {
        	return $e->getMessage();
        }
    }   
}
