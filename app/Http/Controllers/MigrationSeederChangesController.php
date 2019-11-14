<?php

namespace App\Http\Controllers;

use App\Repositories\MigrationSeederChanges\MigrationSeederChangesRepository;
use App\Repositories\Tenant\TenantRepository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Helpers\EmailHelper;
use App\Models\Tenant;
use Validator;
use DB;

class MigrationSeederChangesController extends Controller
{
    /**
     * @var App\Repositories\Tenant\TenantRepository
     */
    private $tenantRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\EmailHelper
     */
    private $emailHelper;

    /**
     * @var App\Repositories\MigrationSeederChanges\MigrationSeederChangesRepository
     */
    private $migrationSeederChangesRepository;

    /**
     * Create a new controller instance.
     *
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param App\Repositories\MigrationSeederChanges\MigrationSeederChangesRepository
     * @return void
     */
    public function __construct(
        TenantRepository $tenantRepository,
        ResponseHelper $responseHelper,
        MigrationSeederChangesRepository $migrationSeederChangesRepository
    ) {
        $this->responseHelper = $responseHelper;
        $this->tenantRepository = $tenantRepository;
        $this->migrationSeederChangesRepository = $migrationSeederChangesRepository;
        $this->emailHelper = new EmailHelper();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->toArray(),
            [
                'type' => 'required|in:'.implode(',', config('constants.migration_file_type')),
                'migration_file' => 'required'
            ]
        );

        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MIGRATION_CHANGES_FILE_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }

        $validFileTypesArray = ['text/x-php'];
        $file = $request->file('migration_file');
        if (!in_array($file->getMimeType(), $validFileTypesArray)) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_NOT_VALID_EXTENSION'),
                trans('messages.custom_error_message.ERROR_INVALID_MIGRATION_FILE_EXTENSION')
            );
        }

        $fileName = $request->migration_file->getClientOriginalName();
        $fileType = $request->type;
        
        // Store file details on master table
        $this->migrationSeederChangesRepository->storeDetails($fileName, $fileType);

        if ($request->type === config('constants.migration_file_type.migration')) {
            $request->migration_file->move(
                'database/migrations/tenant/',
                $request->migration_file->getClientOriginalName()
            );
            // Run migration
            $this->runMigration();
        } else {
            $request->migration_file->move(
                'database/seeds/tenant/uploaded/',
                $request->migration_file->getClientOriginalName()
            );
            exec('composer dump-autoload');
            // Run seeder
            $this->runSeeder();
        }
        
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_MIGRATION_CHANGES_APPLIED_SUCCESSFULLY');

        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
    
    /**
     * Run uploaded migration file
     *
     * @return void
     */
    public function runMigration()
    {
        // Get all active tenant
        $tenants = $this->tenantRepository->getAllTenants();
        if ($tenants->count() > 0) {
            foreach ($tenants as $tenant) {
                // Create connection of tenant one by one
                if ($this->createConnection($tenant->tenant_id) !== 0) {
                    try {
                        // Run migration command to apply migration change
                        Artisan::call('migrate --path=database/migrations/tenant');
                    } catch (\Exception $e) {
                        // Failed then send mail to admin
                        $this->sendFailerMail($tenant, config('constants.migration_file_type.migration'));
                        continue;
                    }
                    // Disconnect database and connect with master DB
                    DB::disconnect('tenant');
                    DB::reconnect('mysql');
                }
            }
        }
    }

    /**
     * Run uploaded seeder file
     *
     * @return void
     */
    public function runSeeder()
    {
        // Get all active tenant
        $tenants = $this->tenantRepository->getAllTenants();

        // Get all uploaded seeder files
        $seederFiles = Storage::disk('seeder')->allFiles();
        
        if ($tenants->count() > 0) {
            foreach ($tenants as $tenant) {
                // Create connection of tenant one by one
                if ($this->createConnection($tenant->tenant_id) !== 0) {
                    try {
                        // Get seeder table data from tenant database
                        $seederInDatabase = DB::table('seeders')
                        ->whereNull('deleted_at')
                        ->get()
                        ->pluck('seeder')
                        ->toArray();

                        foreach ($seederFiles as $file) {
                            if (count($seederInDatabase) > 0) {
                                // Check seeder file is exist in databse or not
                                if (array_search($file, $seederInDatabase) !== false) {
                                    continue;
                                }
                            }
                            $seederClassName = explode(".", $file)[0];

                            Artisan::call("db:seed --class=$seederClassName");
                            
                            DB::table('seeders')->insert([
                                'seeder' => $file
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Failed then send mail to admin
                        $this->sendFailerMail($tenant, config('constants.migration_file_type.seeder'));
                        continue;
                    }
                    // Disconnect database and connect with master DB
                    DB::disconnect('tenant');
                    DB::reconnect('mysql');
                }
            }
        }
    }

    /**
     * Send email notification to admin
     *
     * @param App\Models\Tenant $tenant
     * @param string $type
     * @return void
     */
    public function sendFailerMail(Tenant $tenant, string $type)
    {
        if ($type === config('constants.migration_file_type.migration')) {
            $message = "Migration changes filed for tenant : ". $tenant->name. '.';
            $params['subject'] = 'Error in migration changes';
        } else {
            $message = "Seeder changes filed for tenant : ". $tenant->name. '.';
            $params['subject'] = 'Error in seeder changes';
        }

        $message .= "<br> Database name : ". "ci_tenant_". $tenant->tenant_id;

        $data = array(
            'message'=> $message,
            'tenant_name' => $tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'
        .config('constants.EMAIL_TEMPLATE_MIGRATION_NOTIFICATION'); //path to the email template

        $params['data'] = $data;

        $this->emailHelper->sendEmail($params);
    }

    /**
     * Create connection with tenant's database
     *
     * @param int $tenantId
     * @return int
     */
    public function createConnection(int $tenantId): int
    {
        DB::purge('tenant');
        
        // Set configuration options for the newly create tenant
        Config::set(
            'database.connections.tenant',
            array(
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'ci_tenant_'.$tenantId,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
            )
        );

        // Set default connection with newly created database
        DB::setDefaultConnection('tenant');

        try {
            DB::connection('tenant')->getPdo();
        } catch (\Exception $exception) {
            return 0;
        }

        return $tenantId;
    }
}
