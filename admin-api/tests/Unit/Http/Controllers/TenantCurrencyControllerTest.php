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
use App\Repositories\Currency\TenantAvailableCurrencyRepository;
use Validator;

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
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $modelNotFoundException = $this->mock(ModelNotFoundException::class);
        $tenantAvailableCurrencyRepository->shouldReceive('getTenantCurrencyList')
            ->once()
            ->with($request, $id)
            ->andThrow($modelNotFoundException);

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

        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->index($request, $id);
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
            "data"=> [],
            "pagination"=>[],
            "message"=> trans('messages.success.MESSAGE_TENANT_CURRENCY_LISTING')
        ];

        $jsonResponse = $this->getJson($methodResponse);

        $repository = $this->mock(CurrencyRepository::class);
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $tenantAvailableCurrencyRepository->shouldReceive('getTenantCurrencyList')
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
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->index($request, 1);
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
        $modelNotFoundException = $this->mock(ModelNotFoundException::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow($modelNotFoundException);

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
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->store($request, $tenantId);
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

        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenant = $this->mock(Tenant::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn($tenant);
            
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
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->store($request, $tenantId);
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
        $tenant = $this->mock(Tenant::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow($tenant);

        $isAvailableCurrencyResponse = [            
            false,
            'systemCurrencyInvalid' => false,
            'systemCurrency' => $data['code'],
        ];

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isAvailableCurrency')
            ->once()
            ->with($request['code'])
            ->andReturn($isAvailableCurrencyResponse);

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
        
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->store($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test store for is system currency is valid or not
    *
    * @return void
    */
    public function testStoreSystemCurrencyNotValidIsValidCurrency()
    {
        $data = [
            "code"=> "FAK",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantId = 1;
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenant = $this->mock(Tenant::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow($tenant);

        $isAvailableCurrencyResponse = [            
            false,
            'systemCurrencyInvalid' => true,
            'systemCurrency' => $data['code'],
        ];

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isAvailableCurrency')
            ->once()
            ->with($request['code'])
            ->andReturn($isAvailableCurrencyResponse);

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_UNPROCESSABLE_ENTITY,
                    "type"=> Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    "code"=> config('constants.error_codes.ERROR_SYSTEM_CURRENCY_CODE_WRONG'),
                    "message"=> 'Currency code '. $data['code'] .' is invalid.'
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
            config('constants.error_codes.ERROR_SYSTEM_CURRENCY_CODE_WRONG'),
            'Currency code '. $data['code'] .' is invalid.'
        )->andReturn($jsonResponse);
        
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->store($request, $tenantId);
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
        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenant = $this->mock(Tenant::class);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn($tenant);

        Validator::shouldReceive('make')
        ->once()
        ->andReturn(Mockery::mock(['fails' => false]));

        $isAvailableCurrencyResponse = [            
            true,
            $data['code']
        ];

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isAvailableCurrency')
            ->once()
            ->with($request['code'])
            ->andReturn($isAvailableCurrencyResponse);

        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $tenantAvailableCurrencyRepository->shouldReceive('store')
            ->once()
            ->with($data, $tenantId)
            ->andReturn();
        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_CREATED,
                    "message"=> trans('messages.success.MESSAGE_TENANT_CURRENCY_ADDED')
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);
        
        $responseHelper
        ->shouldReceive('success')
        ->once()
        ->with(
            Response::HTTP_CREATED,
            trans('messages.success.MESSAGE_TENANT_CURRENCY_ADDED')
        )->andReturn($jsonResponse);
        
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->store($request, $tenantId);
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
        $modelNotFoundExeption = $this->mock(ModelNotFoundException::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow($modelNotFoundExeption);

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
        
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $repository = $this->mock(CurrencyRepository::class);
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->update($request, $tenantId);
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
        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenant = $this->mock(Tenant::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn($tenant);
            
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
        
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $repository = $this->mock(CurrencyRepository::class);
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->update($request, $tenantId);
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
        $tenant = $this->mock(Tenant::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow($tenant);
        
        $isAvailableCurrencyResponse = [            
            false,
            'systemCurrencyInvalid' => false,
            'systemCurrency' => $data['code'],
        ];

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isAvailableCurrency')
            ->once()
            ->with($request['code'])
            ->andReturn($isAvailableCurrencyResponse);

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
        
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->update($request, $tenantId);
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

        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenant = $this->mock(Tenant::class);
        $modelNotFoundExeption = $this->mock(ModelNotFoundException::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn($tenant);

        $isAvailableCurrencyResponse = [            
            true,
            $data['code']
        ];

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isAvailableCurrency')
            ->once()
            ->with($request['code'])
            ->andReturn($isAvailableCurrencyResponse);

        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $tenantAvailableCurrencyRepository->shouldReceive('update')
            ->once()
            ->with($data, $tenantId)
            ->andThrow($modelNotFoundExeption);

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
        
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->update($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test update for is valid currency
    *
    * @return void
    */
    public function testUpdateSystemCurrencyNotValidError()
    {
        $data = [
            "code"=> "FAK",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantId = 1;
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenant = $this->mock(Tenant::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andThrow($tenant);
        
        $isAvailableCurrencyResponse = [            
            false,
            'systemCurrencyInvalid' => true,
            'systemCurrency' => $data['code'],
        ];

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isAvailableCurrency')
            ->once()
            ->with($request['code'])
            ->andReturn($isAvailableCurrencyResponse);

        $responseHelper = $this->mock(ResponseHelper::class);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_UNPROCESSABLE_ENTITY,
                    "type"=> Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    "code"=> config('constants.error_codes.ERROR_SYSTEM_CURRENCY_CODE_WRONG'),
                    "message"=> 'Currency code '. $data["code"].' is invalid.'
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
            config('constants.error_codes.ERROR_SYSTEM_CURRENCY_CODE_WRONG'),
            'Currency code '. $data["code"].' is invalid.'
        )->andReturn($jsonResponse);
        
        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->update($request, $tenantId);
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

        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        $tenantRepository = $this->mock(TenantRepository::class);
        $tenant = $this->mock(Tenant::class);
        $tenantRepository->shouldReceive('find')
            ->once()
            ->with($tenantId)
            ->andReturn($tenant);

        $isAvailableCurrencyResponse = [            
            true,
            $data['code']
        ];

        $repository = $this->mock(CurrencyRepository::class);
        $repository->shouldReceive('isAvailableCurrency')
            ->once()
            ->with($request['code'])
            ->andReturn($isAvailableCurrencyResponse);

        $tenantAvailableCurrencyRepository = $this->mock(TenantAvailableCurrencyRepository::class);
        $tenantAvailableCurrencyRepository->shouldReceive('update')
            ->once()
            ->with($data, $tenantId)
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
        
        $controller = $this->getController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $repository
        );

        $response = $controller->update($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
     * Create a new controller instance.
     *
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Repositories\Currency\TenantAvailableCurrencyRepository $tenantAvailableCurrencyRepository
     * @param  App\Repositories\Currency\CurrencyRepository $currencyRepository
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @return void
     */
    private function getController(
        ResponseHelper $responseHelper,
        TenantAvailableCurrencyRepository $tenantAvailableCurrencyRepository,
        TenantRepository $tenantRepository,
        CurrencyRepository $currencyRepository
    ) {
        return new TenantCurrencyController(
            $responseHelper,
            $tenantAvailableCurrencyRepository,
            $tenantRepository,
            $currencyRepository
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
