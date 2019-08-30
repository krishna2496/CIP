<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\TenantSetting\TenantSettingRepository;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;

class TenantSettingController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\TenantSetting\TenantSettingRepository
     */
    private $tenantSettingRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;


    /**
     * Create a new Tenant controller instance.
     *
     * @param  App\Repositories\TenantSetting\TenantSettingRepository $tenantSettingRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        TenantSettingRepository $tenantSettingRepository,
        ResponseHelper $responseHelper
    ) {
        $this->tenantSettingRepository = $tenantSettingRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = $this->tenantSettingRepository->getAllSettings();

        $responseMessage = ($settings->count() > 0) ? trans('messages.success.MESSAGE_ALL_SETTING_LISTING') :
        trans('messages.success.MESSAGE_NO_RECORD_FOUND');

        return $this->responseHelper->success(Response::HTTP_OK, $responseMessage, $settings->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
