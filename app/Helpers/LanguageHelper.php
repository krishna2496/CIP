<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use DB;
use PDOException;
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
        try {
            // Connect master database to get language details
            $this->helpers->switchDatabaseConnection('mysql', $request);
            $languages = DB::table('language')->whereNull('deleted_at')->get();
            
            // Connect tenant database
            $this->helpers->switchDatabaseConnection('tenant', $request);

            return $languages;
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Get languages from `ci_admin` table
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public function getTenantLanguages(Request $request)
    {
        try {
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
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
    
    /**
     * Check for valid language_id from `ci_admin` table
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public function validateLanguageId(Request $request)
    {
        try {
            $tenant = $this->helpers->getTenantDetail($request);
            // Connect master database to get language details
            $this->helpers->switchDatabaseConnection('mysql', $request);
            
            $tenantLanguage = DB::table('tenant_language')
            ->where('tenant_id', $tenant->tenant_id)
            ->where('language_id', $request->language_id);
  
            // Connect tenant database
            $this->helpers->switchDatabaseConnection('tenant', $request);
            
            return ($tenantLanguage->count() > 0) ? true : false;
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
