<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use DB, PDOException;

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

    /**
     * Get languages from `ci_admin` table
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public static function getTenantLanguages(Request $request)
    {
        try {
            $tenant = Helpers::getTenantDetail($request);		
            // Connect master database to get language details
            DatabaseHelper::switchDatabaseConnection('mysql', $request);
            
            $tenantLanguages = DB::table('tenant_language')
            ->select('language.language_id', 'language.code', 'language.name', 'tenant_language.default')
            ->leftJoin('language', 'language.language_id', '=', 'tenant_language.language_id')
            ->where('tenant_id', $tenant->tenant_id)
            ->get();

            // Connect tenant database
            DatabaseHelper::switchDatabaseConnection('tenant', $request);
            
            return $tenantLanguages;
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        
    }
}
