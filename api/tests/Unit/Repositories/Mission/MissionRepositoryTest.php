<?php

namespace Tests\Unit\Http\Repositories\Mission;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use App\Repositories\Country\CountryRepository;
use App\Repositories\MissionMedia\MissionMediaRepository;
use App\Services\Mission\ModelsService;
use App\Repositories\MissionTab\MissionTabRepository;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Mission\MissionRepository;
use App\Models\Mission;
use App\Models\MissionLanguage;
use App\User;
use Mockery;
use TestCase;
use Validator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\TimeMission;
use App\Models\MissionDocument;
use App\Models\FavouriteMission;
use App\Models\MissionSkill;
use App\Models\MissionRating;
use App\Models\MissionApplication;
use App\Models\City;
use DB;
use App\Repositories\MissionUnitedNationSDG\MissionUnitedNationSDGRepository;

class MissionRepositoryTest extends TestCase
{
    
    /**
    * @testdox Test store add UN sdg with store method
    *
    * @return void
    */
    public function testAddUnSDGMissionRepositorySuccess()
    {
        $data = [
            "location" => [
                "city_id" => 1,
                "country_id" => 233,
                "country_code" => "US"
            ],
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10)
            ],
            "mission_detail"=> [
                [
                    "lang"=> "en",
                    "title"=> "New Organization Mission created",
                    "short_description"=> "this is testing api with all mission details",
                    "objective"=> "To test and check",
                    "label_goal_achieved"=> "test percentage",
                    "label_goal_objective"=> "check test percentage",
                    "section"=> [
                        [
                            "title"=> "Section title",
                            "description"=> "Section description"
                        ]
                    ],
                    "custom_information"=> [
                        [
                            "title"=> "Customer info",
                            "description"=> "Description of customer info"
                        ]
                    ]
                ]
            ],
            "un_sdg"=> [3,6,8,15]
        ];
        $requestData = new Request($data);
        $missionModel = new Mission();

        $languages = collect([
            [
                "language_id"=>1,
                "name"=> "English",
                "code"=> "en",
                "status"=> "1",
                "created_at"=> null,
                "updated_at"=> null,
                "deleted_at"=> null,
            ],
            [
                "language_id" => 2,
                "name" => "French",
                "code" => "fr",
                "status"=>"1",
                "created_at" => null,
                "updated_at" => null,
                "deleted_at" => null,
            ]
        ]);
        

        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $collection = $this->mock(Collection::class);
        $unitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);
        $missionModel = new Mission();
        $missionModel->mission_id = rand(10, 100);

        $modelService = $this->modelService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city
        );

        $languages = [
            (object)[
                "language_id"=>1,
                "name"=> "English",
                "code"=> "en",
                "status"=> "1",
                "created_at"=> null,
                "updated_at"=> null,
                "deleted_at"=> null,
            ],
            (object)[
                "language_id" => 2,
                "name" => "French",
                "code" => "fr",
                "status"=>"1",
                "created_at" => null,
                "updated_at" => null,
                "deleted_at" => null,
            ]
        ];
        $collectionLanguages = collect($languages);
        $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collectionLanguages);

        $countryRepository->shouldReceive('getCountryId')
        ->once()
        ->with($requestData->location['country_code'])
        ->andReturn(1);

        $modelService->mission->shouldReceive('create')
        ->once()
        ->andReturn($missionModel);
        
        $modelService->missionLanguage->shouldReceive('create')
        ->once()
        ->andReturn(false);

        $tenantName = str_random(10);
        $helpers->shouldReceive('getSubDomainFromRequest')
        ->once()
        ->with($requestData)
        ->andReturn($tenantName);

        $unitedNationSDGRepository->shouldReceive('addUnSdg')
        ->once()
        ->with($missionModel->mission_id, $requestData)
        ->andReturn(false);


        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $unitedNationSDGRepository
        );

        $response = $repository->store(rand(10,100), $requestData);
        $this->assertInstanceOf(Mission::class, $response);
    }

    /**
    * @testdox Test update UN sdg with update method
    *
    * @return void
    */
    public function testUpdateUnSDGMissionRepositorySuccess()
    {
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $collection = $this->mock(Collection::class);
        $unitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);
        $missionModel = new Mission();
        $missionModel->mission_id = rand(10, 100);

        $data = [
            "un_sdg"=> [3,6,8,15]
        ];
        $requestData = new Request($data);

        $modelService = $this->modelService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city
        );

        $languages = [
            (object)[
                "language_id"=>1,
                "name"=> "English",
                "code"=> "en",
                "status"=> "1",
                "created_at"=> null,
                "updated_at"=> null,
                "deleted_at"=> null,
            ],
            (object)[
                "language_id" => 2,
                "name" => "French",
                "code" => "fr",
                "status"=>"1",
                "created_at" => null,
                "updated_at" => null,
                "deleted_at" => null,
            ]
        ];
        $collectionLanguages = collect($languages);
        $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collectionLanguages);

        $modelService->mission->shouldReceive('findOrFail')
        ->once()
        ->andReturn($missionModel);

        $tenantName = str_random(10);
        $helpers->shouldReceive('getSubDomainFromRequest')
        ->once()
        ->with($requestData)
        ->andReturn($tenantName);

        $unitedNationSDGRepository->shouldReceive('updateUnSdg')
        ->once()
        ->with($missionModel->mission_id, $requestData)
        ->andReturn(false);

        
        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $unitedNationSDGRepository
        );

        $response = $repository->update($requestData, rand(10, 100));
        $this->assertInstanceOf(Mission::class, $response);
    }

    /**
     * Create a new respository instance.
     *
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\S3Helper $s3helper
     * @param  App\Repositories\Country\CountryRepository $countryRepository
     * @param  App\Repositories\MissionMedia\MissionMediaRepository $missionMediaRepository
     * @param  App\Services\Mission\ModelsService $modelsService
     * @param  App\Repositories\UnitedNationSDG\UnitedNationSDGRepository $unitedNationSDGRepository
     * @return void
     */
    private function getRepository(
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        CountryRepository $countryRepository,
        MissionMediaRepository $missionMediaRepository,
        ModelsService $modelsService,
        MissionUnitedNationSDGRepository $unitedNationSDGRepository
    ) {
        return new MissionRepository(
            $languageHelper,
            $helpers,
            $s3helper,
            $countryRepository,
            $missionMediaRepository,
            $modelsService,
            $unitedNationSDGRepository
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

    /**
     * Create a new service instance.
     *
     * @param  App\Models\Mission $mission
     * @param  App\Models\TimeMission $timeMission
     * @param  App\Models\MissionLanguage $missionLanguage
     * @param  App\Models\MissionDocument $missionDocument
     * @param  App\Models\FavouriteMission $favouriteMission
     * @param  App\Models\MissionSkill $missionSkill
     * @param  App\Models\MissionRating $missionRating
     * @param  App\Models\MissionApplication $missionApplication
     * @param  App\Models\City $city
     * @return void
     */
    public function modelService(
        Mission $mission,
        TimeMission $timeMission,
        MissionLanguage $missionLanguage,
        MissionDocument $missionDocument,
        FavouriteMission $favouriteMission,
        MissionSkill $missionSkill,
        MissionRating $missionRating,
        MissionApplication $missionApplication,
        City $city
    ) {
        return new ModelsService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city
        );
    }
}
