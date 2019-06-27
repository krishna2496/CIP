<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use DB;
use PDOException;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\DatabaseHelper;
use App\Helpers\Helpers;

class LanguageHelper
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Helpers\DatabaseHelper
     */
    private $databaseHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new helper instance.
     *
     * @param App\Helpers\DatabaseHelper $databaseHelper
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(DatabaseHelper $databaseHelper, Helpers $helpers)
    {
        $this->databaseHelper = $databaseHelper;
        $this->helpers = $helpers;
    }

    /**
     * Get languages from `ci_admin` table
     *
     * @param string $tenantName
     * @return mix
     */
    public function getLanguages(Request $request)
    {
        try {
            // Connect master database to get language details
            $this->databaseHelper->switchDatabaseConnection('mysql', $request);
            $languages = DB::table('language')->get();

            // Connect tenant database
            $this->databaseHelper->switchDatabaseConnection('tenant', $request);
            
            return $languages;
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
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
            $this->databaseHelper->switchDatabaseConnection('mysql', $request);
            
            $tenantLanguages = DB::table('tenant_language')
            ->select('language.language_id', 'language.code', 'language.name', 'tenant_language.default')
            ->leftJoin('language', 'language.language_id', '=', 'tenant_language.language_id')
            ->where('tenant_id', $tenant->tenant_id)
            ->get();

            // Connect tenant database
            $this->databaseHelper->switchDatabaseConnection('tenant', $request);
            
            return $tenantLanguages;
        } catch (PDOException $e) {
            throw new \Exception(trans(
                'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
            ));
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }
}
