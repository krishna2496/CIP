<?php

namespace App\Http\Controllers\App\Language;

use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{

    /**
    * Fetch language file
    *  
    * @param $language
    * @return mixed
    */
    public function fetchLangaugeFile($language) 
    {
        $response = array ();
        $frontEndFolder = config('constants.FRONTEND_LANGUAGE_FOLDER');
        $file_path = realpath(resource_path(). '\lang\\'.$language."\\".$frontEndFolder."\\".$language.".json");
       
        if (!$file_path) {
           $language = strtolower(config('constants.DEFAULT_LANGUAGE'));
           $file_path = realpath(resource_path(). '\lang\\'.$language."\\".$frontEndFolder."\\".$language.".json");
        } 

        $response['locale'] = $language;
        $response['data'] = json_decode(file_get_contents($file_path), true);
        return $response;
    }

}
