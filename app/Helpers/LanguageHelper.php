<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use DB;

class LanguageHelper
{
    /**
     * Get languages from `ci_admin` table
     *
     * @param string $tenantName
     * @return mix
     */
    public static function getLanguages(Request $request)
    {
        try {
            // Connect master database to get language details
			DatabaseHelper::switchDatabaseConnection('mysql', $request);
			$languages = DB::table('language')->get();    
			
			// Connect tenant database
			DatabaseHelper::switchDatabaseConnection('tenant', $request);     
			
			return $languages;
        } catch (\Exception $e) {
           throw new \Exception($e->getMessage());
        }

    }
}
