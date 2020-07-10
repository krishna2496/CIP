<?php
    
namespace Tests\Unit\Http\Controllers;

use TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use App\Repositories\Currency\CurrencyRepository;
use App\Helpers\ResponseHelper;
use App\Models\Tenant;
use App\Models\TenantCurrency;
use App\Http\Controllers\TenantCurrencyController;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Repositories\Tenant\TenantRepository;
use App\Traits\RestExceptionHandlerTrait;

class TenantCurrencyControllerTest extends TestCase
{
    /**
    * @testdox Test index with model not found
    *
    * @return void
    */
    public function testIndexModelNotFound()
    {
        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_NOT_FOUND,
                    "type"=> Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    "code"=> config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                    "message"=> trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
                ]
            ]
        ];
        $data = ['perPage' => '10'];
        $request = new Request($data);
        
        $id = rand(5000, 10000);
        
        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('getTenantCurrencyList')
            ->once()
            ->with($request, $id)
            ->andThrow(new ModelNotFoundException());

        $mockResponse = new LengthAwarePaginator([], 0, 10, 1);
        $jsonResponse = $this->getJson($methodResponse);

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
        ->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
            trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
        )->andReturn($jsonResponse);

        $tenantRepository = $this->mock(TenantRepository::class);
        
        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->index($request, $id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test index with success status
    *
    * @return void
    */
    public function testIndexSuccess()
    {
        $data = ['perPage' => '10'];
        $request = new Request($data);
        $items = [
            "code"=> "INR",
            "default"=> 1,
            "is_active"=> 1
        ];

        $mockResponse = new LengthAwarePaginator($items, 0, 10, 1);

        $methodResponse = [
            "status"=> Response::HTTP_OK,
            "data"=> [
                "code"=> "INR",
                "default"=> 1,
                "is_active"=> 1
            ],
            "pagination"=>[
                "total"=> 1,
                "per_page"=> 10,
                "current_page"=> 1,
                "total_pages"=> 1,
                "next_url"=> null
            ],
            "message"=> trans('messages.success.MESSAGE_TENANT_CURRENCY_LISTING')
        ];
        $jsonResponse = $this->getJson($methodResponse);

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('getTenantCurrencyList')
            ->once()
            ->with($request, 1)
            ->andReturn($mockResponse);

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('successWithPagination')
            ->once()
            ->with(
                $mockResponse,
                Response::HTTP_OK,
                trans('messages.success.MESSAGE_TENANT_CURRENCY_LISTING')
            )
            ->andReturn($jsonResponse);

        $tenantRepository = $this->mock(TenantRepository::class);

        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->index($request, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test store tenant model not found
    *
    * @return void
    */
    public function testStoreTenantModelNotFound()
    {
        $data = [
            "code"=> "ZWD",
            "default"=> "0",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantId = rand(50000, 100000);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow(new ModelNotFoundException());

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> 404,
                    "type"=> "Not Found",
                    "code"=> 200003,
                    "message"=> "Tenant not found in the system"
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);

        $responseHelper
        ->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
            trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
        )->andReturn($jsonResponse);

        $repository = $this->mock(CurrencyRepository::class);
        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->store($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test store with validation failure
    *
    * @return void
    */
    public function testStoreValidationFalure()
    {
        $data = [
            "code"=> "ZWD",
            "default"=> "100",
            "is_active"=> "1"
        ];
        $tenantId = 1;
        TenantCurrency::where(['code'=>'ZWD','tenant_id'=>$tenantId])->delete();
        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn(new Tenant());
            
        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_UNPROCESSABLE_ENTITY,
                    "type"=> Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    "code"=> config('constants.error_codes.ERROR_TENANT_CURRENCY_FIELD_REQUIRED'),
                    "message"=> "The selected default is invalid."
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);
        
        $responseHelper
        ->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            config('constants.error_codes.ERROR_TENANT_CURRENCY_FIELD_REQUIRED'),
            'The selected default is invalid.'
        )->andReturn($jsonResponse);

        $repository = $this->mock(CurrencyRepository::class);
        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->store($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test store for is valid currency
    *
    * @return void
    */
    public function testStoreIsValidCurrency()
    {
        $data = [
            "code"=> "FAK",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantId = 1;
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow(new Tenant());

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isValidCurrency')
            ->once()
            ->with($request)
            ->andReturn(false);

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_UNPROCESSABLE_ENTITY,
                    "type"=> Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    "code"=> config('constants.error_codes.ERROR_CURRENCY_CODE_NOT_AVAILABLE'),
                    "message"=> trans('messages.custom_error_message.ERROR_CURRENCY_CODE_NOT_AVAILABLE')
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);
        
        $responseHelper
        ->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            config('constants.error_codes.ERROR_CURRENCY_CODE_NOT_AVAILABLE'),
            trans('messages.custom_error_message.ERROR_CURRENCY_CODE_NOT_AVAILABLE')
        )->andReturn($jsonResponse);

        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->store($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test store with success
    *
    * @return void
    */
    public function testStoreSuccess()
    {
        $tenantId = 1;
        TenantCurrency::where(['code'=>'USD','tenant_id'=>$tenantId])->delete();
        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn(new Tenant());

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isValidCurrency')
            ->once()
            ->with($request)
            ->andReturn(true);

        $repository->shouldReceive('store')
            ->once()
            ->with($request, $tenantId)
            ->andReturn();

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_OK,
                    "message"=> trans('messages.custom_error_message.MESSAGE_TENANT_CURRENCY_ADDED')
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);
        
        $responseHelper
        ->shouldReceive('success')
        ->once()
        ->with(
            Response::HTTP_OK,
            trans('messages.success.MESSAGE_TENANT_CURRENCY_ADDED')
        )->andReturn($jsonResponse);

        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->store($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test update tenant model not found
    *
    * @return void
    */
    public function testUpdateTenantModelNotFound()
    {
        $data = [
            "code"=> "ZWD",
            "default"=> "0",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantId = rand(50000, 100000);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow(new ModelNotFoundException());

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_NOT_FOUND,
                    "type"=> Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    "code"=> config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                    "message"=> trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);

        $responseHelper
        ->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
            trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
        )->andReturn($jsonResponse);

        $repository = $this->mock(CurrencyRepository::class);
        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->update($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test update with validation failure
    *
    * @return void
    */
    public function testUpdateValidationFalure()
    {
        $data = [
            "code"=> "ZWD",
            "default"=> "100",
            "is_active"=> "1"
        ];
        $tenantId = 1;
        TenantCurrency::where(['code'=>'ZWD','tenant_id'=>$tenantId])->delete();
        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn(new Tenant());
            
        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_UNPROCESSABLE_ENTITY,
                    "type"=> Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    "code"=> config('constants.error_codes.ERROR_TENANT_CURRENCY_FIELD_REQUIRED'),
                    "message"=> "The selected default is invalid."
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);
        
        $responseHelper
        ->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            config('constants.error_codes.ERROR_TENANT_CURRENCY_FIELD_REQUIRED'),
            'The selected default is invalid.'
        )->andReturn($jsonResponse);

        $repository = $this->mock(CurrencyRepository::class);
        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->update($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test update for is valid currency
    *
    * @return void
    */
    public function testUpdateIsValidCurrency()
    {
        $data = [
            "code"=> "FAK",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantId = 1;
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow(new Tenant());

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isValidCurrency')
            ->once()
            ->with($request)
            ->andReturn(false);

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_UNPROCESSABLE_ENTITY,
                    "type"=> Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    "code"=> config('constants.error_codes.ERROR_CURRENCY_CODE_NOT_AVAILABLE'),
                    "message"=> trans('messages.custom_error_message.ERROR_CURRENCY_CODE_NOT_AVAILABLE')
                ]
            ]
        ];
        
        $jsonResponse = $this->getJson($methodResponse);
        
        $responseHelper
        ->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            config('constants.error_codes.ERROR_CURRENCY_CODE_NOT_AVAILABLE'),
            trans('messages.custom_error_message.ERROR_CURRENCY_CODE_NOT_AVAILABLE')
        )->andReturn($jsonResponse);

        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->update($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test update currency not found
    *
    * @return void
    */
    public function testUpdateCurrencyNotFound()
    {
        $tenantId = 1;
        TenantCurrency::where(['code'=>'USD','tenant_id'=>$tenantId])->delete();
        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn(new Tenant());

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isValidCurrency')
            ->once()
            ->with($request)
            ->andReturn(true);

        $repository->shouldReceive('update')
            ->once()
            ->with($request, $tenantId)
            ->andThrow(new ModelNotFoundException());

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_NOT_FOUND,
                    "type"=> Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    "code"=> config('constants.error_codes.CURRENCY_CODE_NOT_FOUND'),
                    "message"=> trans('messages.custom_error_message.ERROR_CURRENCY_CODE_NOT_FOUND')
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);
        
        $responseHelper
        ->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            config('constants.error_codes.CURRENCY_CODE_NOT_FOUND'),
            trans('messages.custom_error_message.ERROR_CURRENCY_CODE_NOT_FOUND')
        )->andReturn($jsonResponse);

        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->update($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test update with success
    *
    * @return void
    */
    public function testUpdateSuccess()
    {
        $tenantId = 1;
        TenantCurrency::where(['code'=>'USD','tenant_id'=>$tenantId])->delete();
        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn(new Tenant());

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isValidCurrency')
            ->once()
            ->with($request)
            ->andReturn(true);

        $repository->shouldReceive('update')
            ->once()
            ->with($request, $tenantId)
            ->andReturn();

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_OK,
                    "message"=> trans('messages.custom_error_message.MESSAGE_TENANT_CURRENCY_UPDATED')
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);
        
        $responseHelper
        ->shouldReceive('success')
        ->once()
        ->with(
            Response::HTTP_OK,
            trans('messages.success.MESSAGE_TENANT_CURRENCY_UPDATED')
        )->andReturn($jsonResponse);

        $service = $this->getController(
            $responseHelper,
            $repository,
            $tenantRepository
        );

        $response = $service->update($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
     * Create a new controller instance.
     *
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Repositories\Currency\CurrencyRepository $currencyRepository
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @return void
     */
    private function getController(
        ResponseHelper $responseHelper,
        CurrencyRepository $currencyRepository,
        TenantRepository $tenantRepository
    ) {
        return new TenantCurrencyController(
            $responseHelper,
            $currencyRepository,
            $tenantRepository
        );
    }

    /**
    * Mock an object
    *
    * @param string name
    *
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
    *
    * @return JsonResponse
    */
    private function getJson($class)
    {
        return new JsonResponse($class);
    }
}
