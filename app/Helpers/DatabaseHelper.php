<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use App\Helpers\ResponseHelper;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Traits\RestExceptionHandlerTrait;
use PDOException;
use DB;
use Throwable;

class DatabaseHelper
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
    }

    /**
     * Switch database connection runtime
     *
     * @param string $connection
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function switchDatabaseConnection(string $connection, Request $request)
    {
        try {
            $domain = $this->helpers->getSubDomainFromRequest($request);
            // Set master connection
            $pdo = DB::connection('mysql')->getPdo();
            Config::set('database.default', 'mysql');

            if ($connection=="tenant") {
                // Set tenant connection
                /*$tenant = DB::table('tenant')->where('name',$domain)->whereNull('deleted_at')->first();
                Self::createConnection($tenant->tenant_id);*/
                $pdo = DB::connection('tenant')->getPdo();
                Config::set('database.default', 'tenant');
            }
        } catch (PDOException $e) {
            throw new \Exception(trans(
                'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
            ));
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }

    /**
     * Create database connection runtime
     *
     * @param int $tenantId
     */
    public static function createConnection(int $tenantId)
    {
        try {
            Config::set('database.connections.tenant', array(
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'ci_tenant_'.$tenantId,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
            ));
            // Create connection for the tenant database
            $pdo = DB::connection('tenant')->getPdo();
            // Set default database
            Config::set('database.default', 'tenant');
        } catch (PDOException $e) {
            throw new \Exception(trans(
                'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
            ));
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }
}
