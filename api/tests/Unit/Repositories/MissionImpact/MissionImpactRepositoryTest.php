<?php
    
namespace Tests\Unit\Repositories\MissionImpact;

use DB;
use Mockery;
use TestCase;
use App\Models\Mission;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Helpers\LanguageHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Models\MissionImpact;
use App\Services\Mission\ModelsService;
use App\Models\MissionImpactLanguage;
use App\Repositories\MissionImpact\MissionImpactRepository;

class MissionImpactRepositoryTest extends TestCase
{
    /**
    * @testdox Test store success
    *
    * @return void
    */
    public function testImpactMissionStoreSuccess()
    {
        $data = [
            'icon_path' => str_random(100),
            'sort_key' => rand(50000, 70000),
            'translations' => [
                [
                    'language_code' => 'fr',
                    'content' => str_random(160)
                ]
            ]
        ];

        $languagesData = [
            (object)[
                'language_id'=>1,
                'name'=> 'English',
                'code'=> 'en',
                'status'=> '1',
                'created_at'=> null,
                'updated_at'=> null,
                'deleted_at'=> null,
            ],
            (object)[
                'language_id' => 2,
                'name' => 'French',
                'code' => 'fr',
                'status'=>'1',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ]
        ];

        $collectionLanguageData = collect($languagesData);
        $missionId = 13;
        $defaultTenantLanguageId = 1;

        $mission = $this->mock(Mission::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $missionImpactLanguage = $this->mock(MissionImpactLanguage::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $collection = $this->mock(Collection::class);
        $missionImpact = $this->mock(MissionImpact::class);

        $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collection);

        $missionImpact->shouldReceive('create')
        ->once()
        ->andReturn(new MissionImpact());

        $collection->shouldReceive('where')
        ->once()
        ->with('code', $data['translations'][0]['language_code'])
        ->andReturn($collectionLanguageData);

        $missionImpactLanguage->shouldReceive('create')
        ->once()
        ->andReturn($missionImpactLanguage);
        
        $repository = $this->getRepository(
            $missionImpact,
            $missionImpactLanguage,
            $languageHelper
        );

        $response = $repository->store($data, $missionId, $defaultTenantLanguageId);
    }

    /**
    * @testdox Test update success
    *
    * @return void
    */
    public function testImpactMissionssUpdateSuccess()
    {
        $data = [
            'mission_impact_id' => str_random(36),
            'icon_path' => str_random(100),
            'sort_key' => rand(10000, 100000),
            'translations' => [
                [
                    'language_code' => 'en',
                    'content' => str_random(160)
                ]
            ]
        ];

        $languagesData = [
            (object)[
                'language_id'=>1,
                'name'=> 'English',
                'code'=> 'en',
                'status'=> '1',
                'created_at'=> null,
                'updated_at'=> null,
                'deleted_at'=> null,
            ],
            (object)[
                'language_id' => 2,
                'name' => 'French',
                'code' => 'fr',
                'status'=>'1',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ]
        ];

        $collectionLanguageData = collect($languagesData);
        $missionId = rand(10000, 100000);
        $defaultTenantLanguageId = 1;

        $mission = $this->mock(Mission::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $missionImpactLanguage = $this->mock(MissionImpactLanguage::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $collection = $this->mock(Collection::class);
        $missionImpact = $this->mock(MissionImpact::class);

        $languageData = $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collection);

        $missionImpact->shouldReceive('where')
        ->once()
        ->with(['mission_impact_id'=>$data['mission_impact_id']])
        ->andReturn($missionImpact);

        $missionImpact->shouldReceive('update')
        ->once()
        ->with(['sort_key'=>$data['sort_key']])
        ->andReturn($missionImpact);

        $missionImpact->shouldReceive('where')
        ->once()
        ->with(['mission_impact_id'=>$data['mission_impact_id']])
        ->andReturn($missionImpact);
        
        $missionImpact->shouldReceive('update')
        ->once()
        ->with(['icon'=>$data['icon_path']])
        ->andReturn($missionImpact);

        $collection->shouldReceive('where')
        ->once()
        ->with('code', $data['translations'][0]['language_code'])
        ->andReturn($collectionLanguageData);

        $missionImpactLanguage->shouldReceive('createOrUpdateMissionImpactTranslation')
        ->once()
        ->andReturn();

        $repository = $this->getRepository(
            $missionImpact,
            $missionImpactLanguage,
            $languageHelper
        );

        $response = $repository->update($data, $missionId, $defaultTenantLanguageId);
    }

    /**
     * Create a new ImpactMission repository instance.
     *
     * @param  App\Models\MissionImpact $missionImpact
     * @param  App\Models\MissionImpactLanguage $missionImpactLanguage
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    private function getRepository(
        MissionImpact $missionImpact,
        MissionImpactLanguage $missionImpactLanguage,
        LanguageHelper $languageHelper
    ) {
        return new MissionImpactRepository(
            $missionImpact,
            $missionImpactLanguage,
            $languageHelper,
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