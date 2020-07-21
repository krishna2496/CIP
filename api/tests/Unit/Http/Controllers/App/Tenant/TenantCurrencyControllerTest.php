<?php

namespace Tests\Unit\Http\Controllers\App\Tenant;

use Mockery;
use TestCase;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\App\Tenant\TenantCurrencyController;

class TenantCurrencyControllerTest extends TestCase
{

    /**
     * @testdox get tenant currency list success
     */
    public function testGetTenantCurrencyListSuccess()
    {
        $request = new Request();
        $helpers = $this->mock(Helpers::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $collection = $this->mock(Collection::class);

        $currencies = [
            [
                "code"=>1,
                "default"=> "English",
                "is_active"=> "en"
            ]
        ];

        $collectionLanguages = collect($currencies);

        $helpers->shouldReceive('getTenantCurrency')
        ->times()
        ->with($request)
        ->andReturn($collectionLanguages);

        $apiData = $collectionLanguages->toArray();
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_TENANT_CURRENCY_LISTING');

        $methodResponse = [
            "status" => $apiStatus,
            "data" => $apiData,
            "message" => $apiMessage,
        ];

        $jsonResponse = $this->getJson($methodResponse);

        $responseHelper->shouldReceive('success')
        ->once()
        ->with($apiStatus, $apiMessage, $apiData)
        ->andReturn($jsonResponse);

        $callController = $this->getController(
            $helpers,
            $responseHelper
        );

        $response = $callController->index($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
     * Create a new controller instance.
     *
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    private function getController(
        Helpers $helpers,
        ResponseHelper $responseHelper
    ) {
        return new TenantCurrencyController(
            $helpers,
            $responseHelper
        );
    }

    /**
    * Mock an object
    *
    * @param string name
    * @return Mockery
    */
    private function mock($class)
    {
        return Mockery::mock($class);
    }

    /**
    * get json reponse
    *
    * @param class name
    * @return JsonResponse
    */
    private function getJson($class)
    {
        return new JsonResponse($class);
    }
}
