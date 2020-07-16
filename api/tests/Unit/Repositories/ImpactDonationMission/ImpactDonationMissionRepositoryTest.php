<?php
    
namespace Tests\Unit\Repositories\ImpactDonationMission;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use TestCase;
use Mockery;
use App\Services\Mission\ModelsService;
use App\Models\MissionImpactDonationLanguage;
use App\Helpers\LanguageHelper;
use App\Models\Mission;
use App\Repositories\ImpactDonationMission\ImpactDonationMissionRepository;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Collection;
use App\Models\MissionImpactDonation;
use Illuminate\Support\Str;
use DB;
use App\Models\Language;

class ImpactDonationMissionRepositoryTest extends TestCase
{
    /**
    * @testdox Test store success
    *
    * @return void
    */
    public function testImpactDonationStoreSuccess()
    {

        $data = [
            "amount" => 512,
            "translations" => [
                [
                    "language_code" => "tr",
                    "content" => "this is test impact donation mission in english 2 language."
                ]
            ]
        ];

        // $request = new Request($data);
        $missionId = 13;
        $defaultTenantLanguageId = 1;

        $mission = $this->mock(Mission::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $modelService = $this->mock(ModelsService::class);
        $missionImpactDonationLanguage = $this->mock(MissionImpactDonationLanguage::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $collection = $this->mock(Collection::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);

        
        $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collection);

        $impactDonationArray = [
            'mission_impact_donation_id' => (String) Str::uuid(),
            'mission_id' => $missionId,
            'amount' => $data['amount']
        ];

        $missionImpactDonation->shouldReceive('create')
        ->once()
        ->with($impactDonationArray)
        ->andReturn(new MissionImpactDonation());
        
        $repository = $this->getRepository(
            $mission,
            $responseHelper,
            $modelService,
            $missionImpactDonationLanguage,
            $languageHelper,
            $missionImpactDonation
        );

        $response = $repository->store($data, $missionId, $defaultTenantLanguageId);
    }

    /**
    * @testdox Test update success
    *
    * @return void
    */
    public function testImpactDonationUpdateSuccess()
    {
        $data = [
            "impact_donation_id" => "53f994a9-b3c0-454a-b81d-8723a8e31808",
            "amount" => 145,
            "translations" => [
                [
                    "language_code" => "en",
                    "content" => "this is test impact donation mission in english 2 language."
                ]
            ]
        ];
        $missionId = 13;
        $defaultTenantLanguageId = 1;

        $mission = $this->mock(Mission::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $modelService = $this->mock(ModelsService::class);
        $missionImpactDonationLanguage = $this->mock(MissionImpactDonationLanguage::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $collection = $this->mock(Collection::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $languageObjectMock = $this->mock(Language::class);

        $languageData = $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collection);

        $missionImpactDonation->shouldReceive('where')
        ->once()
        ->with(["mission_impact_donation_id"=>$data["impact_donation_id"]])
        ->andReturn($missionImpactDonation);

        $missionImpactDonation->shouldReceive('update')
        ->once()
        ->with(['amount'=>$data['amount']])
        ->andReturn($missionImpactDonation);

        $collection->shouldReceive('where')
        ->once()
        ->with('code', $data['translations'][0]['language_code'])
        ->andReturn($collection);

        $collection->shouldReceive('first')
        ->once()
        ->andReturn($languageObjectMock);

        $repository = $this->getRepository(
            $mission,
            $responseHelper,
            $modelService,
            $missionImpactDonationLanguage,
            $languageHelper,
            $missionImpactDonation
        );

        $response = $repository->update($data, $missionId, $defaultTenantLanguageId);

    }

    /**
     * Create a new ImpactDonationMission repository instance.
     *
     * @param  Mission $mission
     * @param  ResponseHelper $responseHelper
     * @param  App\Services\Mission\ModelsService $modelsService
     * @param  App\Models\MissionImpactDonationLanguage $missionImpactDonationLanguage
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @param  App\Models\MissionImpactDonation $missionImpactDonation
     * @return void
     */
    private function getRepository(
        Mission $mission,
        ResponseHelper $responseHelper,
        ModelsService $modelsService,
        MissionImpactDonationLanguage $missionImpactDonationLanguage,
        LanguageHelper $languageHelper,
        MissionImpactDonation $missionImpactDonation
    ) {
        return new ImpactDonationMissionRepository(
            $mission,
            $responseHelper,
            $modelsService,
            $missionImpactDonationLanguage,
            $languageHelper,
            $missionImpactDonation
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
