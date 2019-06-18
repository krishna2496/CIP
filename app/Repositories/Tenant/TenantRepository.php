<?php
namespace App\Repositories\Tenant;

use App\Repositories\Tenant\TenantInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use PDOException;
use App\Models\Tenant;
use App\Jobs\TenantDefaultLanguageJob;
use App\Jobs\TenantMigrationJob;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TenantRepository implements TenantInterface
{
    /**
     * @var App\Models\Tenant
     */
    public $tenant;

    /**
     * @var Illuminate\Http\Response
     */
    private $response;

    /**
     * Create a new Tenant repository instance.
     *
     * @param  App\Models\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant, Response $response)
    {
        $this->tenant = $tenant;
        $this->response = $response;
    }

    /**
     * Get listing of the tenants.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function tenantList(Request $request)
    {
        try {
            $tenantQuery = $this->tenant->with('options', 'tenantLanguages', 'tenantLanguages.language');

            if ($request->has('search')) {
                $tenantQuery->where('name', 'like', '%' . $request->input('search') . '%');
            }
            if ($request->has('order')) {
                $orderDirection = $request->input('order', 'asc');
                $tenantQuery->orderBy('tenant_id', $orderDirection);
            }

            $tenantList = $tenantQuery->paginate(config('constants.PER_PAGE_LIMIT'));
            $responseMessage = (count($tenantList) > 0) ? trans('messages.success.MESSAGE_TENANT_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            return ResponseHelper::successWithPagination($this->response->status(), $responseMessage, $tenantList);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->toArray(), $this->tenant->createRules);

            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_422'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_200001'),
                    $validator->errors()->first()
                );
            }

            $tenant = $this->tenant->create($request->toArray());

            dispatch(new TenantDefaultLanguageJob($tenant));

            // ONLY FOR TESTING START Create api_user data (PLEASE REMOVE THIS CODE IN PRODUCTION MODE)
            if (env('APP_ENV')=='local') {
                $apiUserData['api_key'] = base64_encode($tenant->name.'_api_key');
                $apiUserData['api_secret'] = base64_encode($tenant->name.'_api_secret');
                // Insert api_user data into table
                $tenant->apiUsers()->create($apiUserData);
            }
            // ONLY FOR TESTING END

            // Add options data into `tenant_has_option` table
            if (isset($request->options) && count($request->options) > 0) {
                foreach ($request->options as $option_name => $option_value) {
                    $tenantOptionData['option_name'] = $option_name;
                    $tenantOptionData['option_value'] = $option_value;
                    $tenant->options()->create($tenantOptionData);
                }
            }

            // Set response data
            $apiStatus = trans('messages.status_code.HTTP_CREATED');
            $apiData = ['tenant_id' => $tenant->tenant_id];
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_CREATED');

            // Job dispatched to create new tenant's database and migrations
            dispatch(new TenantMigrationJob($tenant));

            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function find(int $id)
    {
        try {
            $tenantDetail = $this->tenant->findTenant($id);

            $apiStatus = $this->response->status();
            $apiData = $tenantDetail->toArray();
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_FOUND');
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.200003'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(int $id)
    {
        try {
            $this->tenant->deleteTenant($id);
            // Set response data
            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_TENANT_DELETED');

            return ResponseHelper::success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.200003'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $rules = $this->tenant->updateRules;
            $rules['name'] = $rules['name'] . ', ' . $id . ', tenant_id, deleted_at, NULL';
            $validator = Validator::make($request->toArray(), $rules);

            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_422'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_200001'),
                    $validator->errors()->first()
                );
            }

            $tenant = Tenant::findOrFail($id);
            $tenant->update($request->toArray());

            // Add options data into `tenant_has_option` table
            if (isset($request->options) && count($request->options) > 0) {
                foreach ($request->options as $option_name => $option_value) {
                    $tenantOptionData['option_name'] = $option_name;
                    $tenantOptionData['option_value'] = $option_value;
                    $tenant->options()->where('option_name', $option_name)
                        ->update($tenantOptionData);
                }
            }
            $apiStatus = $this->response->status();
            $apiData = ['tenant_id' => $id];
            $apiMessage = trans('messages.success.MESSAGE_TENANT_UPDATED');

            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.200003'));
        } catch (PDOException $e) {
            $this->delete($tenant->tenant_id);
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            $this->delete($tenant->tenant_id);
            throw new \Exception($e->getMessage());
        }
    }
}
