<?php
    
namespace Tests\Unit\Repositories\ImpactDonationMission;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use TestCase;
use Mockery;
use App\Services\Mission\ModelsService;
use App\Models\MissionImpactDonationLanguage;
use App\Helpers\LanguageHelper;

class ImpactDonationMissionTest extends TestCase
{
    /**
    * @testdox Test store success
    *
    * @return void
    */
    public function testStoreSuccess()
    {

        $data = [
            "impact_donation" => [
                [
                      "amount" => 5,
                      "translations" => [
                          [
                              "language_code" => "tr",
                              "content" => "this is test impact donation mission in english 2 language."
                          ]
                      ]
                ],
            ]
        ];

        $missionId = 13;
            




        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);

        $service = $this->getRepository(
            $tenantCurrency,
            $tenant
        );
        $tenantId = 1;
        TenantCurrency::where(['code'=>'USD','tenant_id'=>$tenantId])->delete();
        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        
        $currencyData = [
            'tenant_id' => $tenantId,
            'code' => $request['code'],
            'default' => $request['default'],
            'is_active' => $request['is_active']
        ];

        $tenant->shouldReceive('findOrFail')
            ->once()
            ->with($tenantId)
            ->andReturn(new Tenant());

        $tenantCurrency->shouldReceive('where')
            ->once()
            ->with('tenant_id', $tenantId)
            ->andReturn(new TenantCurrency());

        $tenantCurrency->shouldReceive('create')
            ->once()
            ->with($currencyData)
            ->andReturn(new TenantCurrency());

        $response = $service->store($request, $tenantId);
        $this->assertNull($response);
    }

    /**
    * @testdox Test update success
    *
    * @return void
    */
    public function testUpdateSuccess()
    {
        $data = [
            "impact_donation" => [
                [
                      "amount" => 5,
                      "translations" => [
                          [
                              "language_code" => "tr",
                              "content" => "this is test impact donation mission in english 2 language."
                          ]
                      ]
                ],
            ]
        ];

        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);

        $service = $this->getRepository(
            $tenantCurrency,
            $tenant
        );

        $tenantId = 1;
        TenantCurrency::where(['code'=>'USD','tenant_id'=>$tenantId])->delete();
        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);
        
        $currencyData = [
            'tenant_id' => $tenantId,
            'code' => $request['code'],
            'default' => $request['default'],
            'is_active' => $request['is_active']
        ];

        $tenantCurrency->shouldReceive('where')
            ->once()
            ->with(['tenant_id' => $tenantId, 'code' => $request['code']])
            ->andReturn(new TenantCurrency());

        $tenantCurrency->shouldReceive('where')
            ->once()
            ->with('tenant_id', $tenantId)
            ->andReturn(new TenantCurrency());

        $tenantCurrency->shouldReceive('where')
            ->once()
            ->with(['tenant_id' => $tenantId, 'code' => $request['code']])
            ->andReturn(new TenantCurrency());

        $response = $service->update($request, $tenantId);
        $this->assertNull($response);
    }

    /**
    * @testdox Test get tenant currency list
    *
    * @return void
    */
    public function testGetTenantCurrencyListSuccess()
    {
        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);
        $service = $this->getRepository(
            $tenantCurrency,
            $tenant
        );
        $data = ['perPage' => '10'];
        $request = new Request($data);
        $tenantId = 1;

        $tenant->shouldReceive('findOrFail')
            ->once()
            ->with($tenantId)
            ->andReturn(new Tenant());
        
        $tenantCurrency->shouldReceive('where')
             ->once()
             ->with(['tenant_id' => $tenantId])
             ->andReturn(new TenantCurrency());

        $items = [
            "code"=> "INR",
            "default"=> 1,
            "is_active"=> 1
        ];
    
        $mockResponse = new LengthAwarePaginator($items, 0, 10, 1);
        $jsonResponse = new JsonResponse($mockResponse);
        $response = $service->getTenantCurrencyList($request, $tenantId);
        $this->assertInstanceOf(LengthAwarePaginator::class, $response);
    }

    /**
    * @testdox Test get tenant currency list false
    *
    * @return void
    */
    public function testIsValidCurrencyFalse()
    {
        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);

        $service = $this->getRepository(
            $tenantCurrency,
            $tenant
        );

        $tenantId = 1;
        TenantCurrency::where(['code'=>'USD','tenant_id'=>$tenantId])->delete();
        $data = [
            "code"=> "FAK",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);

        $allCurrencies = [
            new Currency('INR', '₹'),
            new Currency('EUR', '€'),
            new Currency('USD', '$'),
            new Currency('BRL', 'R$'),
            new Currency('ZWD', 'Z$'),
        ];

        $this->assertEquals($allCurrencies, $service->findAll());
        $response = $service->isValidCurrency($request['code']);
        $this->assertEquals(false, $response);
    }

    /**
    * @testdox Test get tenant currency list
    *
    * @return void
    */
    public function testIsValidCurrencySuccess()
    {
        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);

        $service = $this->getRepository(
            $tenantCurrency,
            $tenant
        );

        $tenantId = 1;
        TenantCurrency::where(['code'=>'USD','tenant_id'=>$tenantId])->delete();
        $data = [
            "code"=> "USD",
            "default"=> "1",
            "is_active"=> "1"
        ];
        $request = new Request($data);

        $allCurrencies = [
            new Currency('INR', '₹'),
            new Currency('EUR', '€'),
            new Currency('USD', '$'),
            new Currency('BRL', 'R$'),
            new Currency('ZWD', 'Z$'),
        ];

        $this->assertEquals($allCurrencies, $service->findAll());
        $response = $service->isValidCurrency($request['code']);
        $this->assertEquals(true, $response);
    }

    /**
     * Create a new ImpactDonationMission repository instance.
     *
     * @param  Mission $mission
     * @param  ResponseHelper $responseHelper
     * @param  App\Services\Mission\ModelsService $modelsService
     * @param  App\Models\MissionImpactDonationLanguage $missionImpactDonationLanguage
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    private function getRepository(
        Mission $mission,
        ResponseHelper $responseHelper,
        ModelsService $modelsService,
        MissionImpactDonationLanguage $missionImpactDonationLanguage,
        LanguageHelper $languageHelper
    ) {
        return new ImpactDonationMissionRepository(
            $mission,
            $responseHelper,
            $modelsService,
            $missionImpactDonationLanguage,
            $languageHelper
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
}
