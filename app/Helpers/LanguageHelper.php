<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use DB;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\Helpers;
use Illuminate\Support\Collection;

class LanguageHelper
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new helper instance.
     *
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
    }

    /**
     * Get languages from `ci_admin` table
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Support\Collection
     */
    public function getLanguages(Request $request): Collection
    {
        // Connect master database to get language details
        $this->helpers->switchDatabaseConnection('mysql', $request);
        $languages = DB::table('language')->whereNull('deleted_at')->get();
        
        // Connect tenant database
        $this->helpers->switchDatabaseConnection('tenant', $request);

        return $languages;
    }

    /**
     * Get languages from `ci_admin` table
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public function getTenantLanguages(Request $request)
    {
        $tenant = $this->helpers->getTenantDetail($request);
        // Connect master database to get language details
        $this->helpers->switchDatabaseConnection('mysql', $request);
        
        $tenantLanguages = DB::table('tenant_language')
        ->select('language.language_id', 'language.code', 'language.name', 'tenant_language.default')
        ->leftJoin('language', 'language.language_id', '=', 'tenant_language.language_id')
        ->where('tenant_id', $tenant->tenant_id)
        ->get();

        // Connect tenant database
        $this->helpers->switchDatabaseConnection('tenant', $request);
        
        return $tenantLanguages;
    }
    
    /**
     * Check for valid language_id from `ci_admin` table
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public function validateLanguageId(Request $request)
    {
        $tenant = $this->helpers->getTenantDetail($request);
        // Connect master database to get language details
        $this->helpers->switchDatabaseConnection('mysql', $request);
        
        $tenantLanguage = DB::table('tenant_language')
        ->where('tenant_id', $tenant->tenant_id)
        ->where('language_id', $request->language_id);

        // Connect tenant database
        $this->helpers->switchDatabaseConnection('tenant', $request);
        
        return ($tenantLanguage->count() > 0) ? true : false;
    }

    /**
     * Get languages from `ci_admin` table
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public function getTenantLanguageList(Request $request)
    {
        $tenant = $this->helpers->getTenantDetail($request);
        // Connect master database to get language details
        $this->helpers->switchDatabaseConnection('mysql', $request);

        $tenantLanguages = DB::table('tenant_language')
        ->select('language.language_id', 'language.code', 'language.name', 'tenant_language.default')
        ->leftJoin('language', 'language.language_id', '=', 'tenant_language.language_id')
        ->where('tenant_id', $tenant->tenant_id)
        ->pluck('language.name', 'language.language_id');

        // Connect tenant database
        $this->helpers->switchDatabaseConnection('tenant', $request);
        
        return $tenantLanguages;
    }

    /**
     * Get languages code from `ci_admin` table
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Support\Collection
     */
    public function getTenantLanguageCodeList(Request $request): Collection
    {
        $tenant = $this->helpers->getTenantDetail($request);
        // Connect master database to get language details
        $this->helpers->switchDatabaseConnection('mysql', $request);

        $tenantLanguagesCodes = DB::table('tenant_language')
        ->select('language.language_id', 'language.code', 'language.name', 'tenant_language.default')
        ->leftJoin('language', 'language.language_id', '=', 'tenant_language.language_id')
        ->where('tenant_id', $tenant->tenant_id)
        ->pluck('language.code', 'language.language_id');
        // Connect tenant database
        $this->helpers->switchDatabaseConnection('tenant', $request);

        return $tenantLanguagesCodes;
    }
	
	/**
     * Get language id from request
     *
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    public function getLanguageId(Request $request): int
    {
        $languages = $this->getTenantLanguages($request);
        return $languages->where('code', config('app.locale'))->first()->language_id;
    }

    /**
     * Get language details from request
     *
     * @param \Illuminate\Http\Request $request
     * @return null|Object
     */
    public function getLanguageDetails(Request $request): ?Object
    {
        $languages = $this->getTenantLanguages($request);
        $languageCode = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : $this->getDefaultTenantLanguage($request);
        
        $language = $languages->where('code', $languageCode)->first();

        if (!is_null($language)) {
            return $language;
        }
        
        return $this->getDefaultTenantLanguage($request);
    }

    /**
     * Get language id from request
     *
     * @param \Illuminate\Http\Request $request
     * @return Object
     */
    public function getDefaultTenantLanguage(Request $request): Object
    {
        $languages = $this->getTenantLanguages($request);
        return $languages->where('default', 1)->first();
    }
}
