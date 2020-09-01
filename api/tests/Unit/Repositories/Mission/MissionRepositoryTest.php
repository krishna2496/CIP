<?php

namespace Tests\Unit\Http\Repositories\Mission;

<<<<<<< HEAD
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
use App\Models\MissionImpactDonation;
use App\Repositories\ImpactDonationMission\ImpactDonationMissionRepository;
use DB;
=======
use App\Repositories\MissionMedia\MissionMediaRepository;
use App\Repositories\MissionTab\MissionTabRepository;
use App\Repositories\Country\CountryRepository;
use App\Repositories\Mission\MissionRepository;
use App\Services\Mission\ModelsService;
use App\Models\MissionApplication;
use App\Models\MissionTabLanguage;
use App\Models\FavouriteMission;
use App\Models\MissionLanguage;
use App\Models\MissionDocument;
use App\Helpers\LanguageHelper;
use App\Models\MissionRating;
use App\Models\MissionSkill;
use App\Models\Organization;
use App\Models\TimeMission;
use App\Models\MissionTab;
use App\Helpers\S3Helper;
use App\Helpers\Helpers;
use App\Models\Mission;
use App\Models\City;
use TestCase;
use Mockery;
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8

class MissionRepositoryTest extends TestCase
{
    /**
<<<<<<< HEAD
    * @testdox Test impact mission donation success
    *
    * @return void
    */
    public function testStoreImpactDonationMissionSuccess()
    {
        $data = [
            'theme_id' => 1,
            'city_id' => 1,
            'country_id' => 233,
            'start_date' => '2019-05-15 10:40:00',
            'end_date' => '2022-10-15 10:40:00',
            'total_seats' => rand(10, 1000),
            'mission_type' => 'DONATION',
            'publication_status' => 'APPROVED',
            'availability_id' => 1,
            'organisation' => [
                'organisation_id' => 1,
                'organisation_name' => str_random(10)
            ],
            'location' => [
                'city_id' => 1,
                'country_id' => 233,
                'country_code' => 'US'
            ],
            'donation_attribute' => [
                'goal_amount_currency' => 'CAD',
                'goal_amount' => 253,
                'show_goal_amount' => 1,
                'show_donation_percentage' => 0,
                'show_donation_meter'=> 0,
                'show_donation_count' =>0,
                'show_donors_count' =>0,
                'disable_when_funded' => 0,
                'is_disabled' => 0
            ],
            'mission_detail' => [
                [
                    'lang' => 'en',
                    'title' => 'New Organization Mission created',
                    'short_description' => 'this is testing api with all mission details',
                    'objective' => 'To test and check',
                    'label_goal_achieved' => 'test percentage',
                    'label_goal_objective' => 'check test percentage',
                    'section' => [
                        [
                            'title' => 'Section title',
                            'description' => 'Section description'
                        ]
                    ],
                    'custom_information' => [
                        [
                            'title' => 'Customer info',
                            'description' => 'Description of customer info'
                        ]
                    ]
                ]
            ],
            'impact_donation' => [
                [
                    'amount' => rand(100000, 200000),
                    'translations' => [
                        [
                            'language_code' => 'en',
                            'content' => str_random(160)
                        ]
                    ]
                ]
            ]
        ];

        $requestData = new Request($data);

        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $missionImpactDonationRepository = $this->mock(ImpactDonationMissionRepository::class);
=======
    * @testdox Test mission tab deleted by mission_tab_id success
    *
    * @return void
    */
    public function testDeleteMissionTabByMissionTabIdSuccess()
    {
        $missionTabId = str_random(8).'-'.str_random(4).'-'.str_random(4).'-'.str_random(4).'-'.str_random(12);

>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
<<<<<<< HEAD
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $collection = $this->mock(Collection::class);

        $modelService = $this->modelService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city,
            $missionImpactDonation
        );

        $languages = [
            (object)[
                'language_id' => 1,
                'name' => 'English',
                'code' => 'en',
                'status' => '1',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            (object)[
                'language_id' => 2,
                'name' => 'French',
                'code' => 'fr',
                'status' => '1',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ]
        ];

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $collectionLanguages = collect($languages);

        $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collectionLanguages);

        $languageHelper->shouldReceive('getDefaultTenantLanguage')
        ->once()
        ->with($requestData)
        ->andReturn($defaultLanguage);

        $countryId= $data['location']['country_id'];

        $countryRepository->shouldReceive('getCountryId')
        ->once()
        ->with($requestData->location['country_code'])
        ->andReturn($countryId);

        $missionModel = new Mission();
        $missionModel->mission_id = 6587;
        $modelService->mission
        ->shouldReceive('create')
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

        $missionImpactDonationRepository->shouldReceive('store')
        ->once()
        ->andReturn();

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $missionImpactDonationRepository,
            $mission
        );

        $response = $repository->store($requestData);
        
        $this->assertInstanceOf(mission::class, $response);
    }

    /**
    * @testdox Test impact mission donation update success
    *
    * @return void
    */
    public function testUpdateImpactDonationMissionSuccess()
    {
        $data = [
            'impact_donation' => [
                [
                    'impact_donation_id' => str_random(36),
                    'amount' => rand(100000, 200000),
                    'translations' => [
                        [
                            'language_code' => 'es',
                            'content' => str_random(160)
                        ]
                    ]
                ],
                [
                    'amount' => rand(100000, 200000),
                    'translations' => [
                        [
                            'language_code' => 'es',
                            'content' => str_random(160)
                        ]
                    ]
                ]
            ]
        ];
        $missionId = rand(50000, 70000);

        $requestData = new Request($data);

=======
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
<<<<<<< HEAD
        $missionImpactDonationRepository = $this->mock(ImpactDonationMissionRepository::class);
        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $collection = $this->mock(Collection::class);
=======
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $collection = $this->mock(Collection::class);
        $organization = $this->mock(Organization::class);
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8

        $modelService = $this->modelService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city,
<<<<<<< HEAD
            $missionImpactDonation
        );

        $languages = [
            (object)[
                'language_id' => 1,
                'name' => 'English',
                'code' => 'en',
                'status' => '1',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            (object)[
                'language_id' => 2,
                'name' => 'French',
                'code' => 'fr',
                'status' => '1',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ]
        ];

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $collectionLanguages = collect($languages);

        $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collectionLanguages);

        $languageHelper->shouldReceive('getDefaultTenantLanguage')
        ->once()
        ->with($requestData)
        ->andReturn($defaultLanguage);

        $missionModel = new Mission();
        $modelService->mission->shouldReceive('findOrFail')
        ->once()
        ->andReturn($missionModel);

        $tenantName = str_random(10);
        $helpers->shouldReceive('getSubDomainFromRequest')
        ->once()
        ->with($requestData)
        ->andReturn($tenantName);

        $missionImpactDonationRepository->shouldReceive('update')
        ->once()
        ->andReturn();

        $missionImpactDonationRepository->shouldReceive('store')
        ->once()
=======
            $organization,
            $missionTab,
            $missionTabLanguage
        );

        $modelService->missionTab
        ->shouldReceive('deleteMissionTabByMissionTabId')
        ->with($missionTabId)
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
        ->andReturn();

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
<<<<<<< HEAD
            $missionImpactDonationRepository,
            $mission
        );

        $response = $repository->update($requestData, $missionId);

        $this->assertInstanceOf(mission::class, $response);
    }

    /**
     * @testdox tranform imact donation mission attribute in display format
     * 
     * @return void
     */
    public function testTransformImpactDonatiomMissionSuccess()
    {
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $missionImpactDonationRepository = $this->mock(ImpactDonationMissionRepository::class);
        $collection = $this->mock(Collection::class);
        $mission = $this->mock(Mission::class);

        $missionModel = new Mission();
        $missionModel->impactDonation = (object)[
            [
                'mission_impact_donation_id' => str_random(36),
                'amount' => 2,
                'get_mission_impact_donation_detail' => [
                    [
                        'language_id' => 1,
                        'content' => json_encode(str_random(160))
                    ]
                ]
            ]
        ];

        $missionModel->impactDonation = collect($missionModel->impactDonation);

        $languages = [
            (object)[
                'language_id' => 1,
                'name' => 'English',
                'code' => 'en',
                'status' => '1',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            (object)[
                'language_id' => 2,
                'name' => 'French',
                'code' => 'fr',
                'status' => '1',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ]
        ];

        $collectionLanguages = collect($languages);

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $missionImpactDonationRepository,
            $mission
        );

        $response = $repository->impactMissionDonationTransformArray($missionModel, $collectionLanguages);
        $this->assertNull(null);
    }

    /**
     * @testdox mission donation impact linked to mission
     * 
     */
    public function testMissionDonationImpactLinkedToMissionSuccess()
    {
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $missionImpactDonationRepository = $this->mock(ImpactDonationMissionRepository::class);
        $collection = $this->mock(Collection::class);

        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);

        $modelService = $this->modelService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city,
            $missionImpactDonation
        );

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $missionImpactDonationRepository,
            $mission
        );

        $missionId = rand(50000, 70000);
        $missionImpactDonationId = rand(50000, 70000);

        $modelService->missionImpactDonation->shouldReceive("where")
        ->once()
        ->with([['mission_id', '=', $missionId], ['mission_impact_donation_id', '=', $missionImpactDonationId]])
        ->andReturn($missionImpactDonation);

        $modelService->missionImpactDonation->shouldReceive('firstOrFail')
        ->once()
        ->andReturn($missionImpactDonation);

        $response = $repository->isMissionDonationImpactLinkedToMission($missionId, $missionImpactDonationId);
        $this->assertInstanceOf(MissionImpactDonation::class ,$response);
=======
            $missionTabRepository
        );

        $response = $repository->deleteMissionTabByMissionTabId($missionTabId);
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
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
<<<<<<< HEAD
     * @param  App\Repositories\ImpactDonationMission\ImpactDonationMissionRepository $missionImpactDonationRepository
     * @param  App\Models\Mission $mission
=======
     * @param  App\Repositories\MissionMedia\MissionTabRepository $missionTabRepository
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
     * @return void
     */
    private function getRepository(
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        CountryRepository $countryRepository,
        MissionMediaRepository $missionMediaRepository,
        ModelsService $modelsService,
<<<<<<< HEAD
        ImpactDonationMissionRepository $missionImpactDonationRepository,
        Mission $mission
=======
        MissionTabRepository $missionTabRepository
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
    ) {
        return new MissionRepository(
            $languageHelper,
            $helpers,
            $s3helper,
            $countryRepository,
            $missionMediaRepository,
            $modelsService,
<<<<<<< HEAD
            $missionImpactDonationRepository,
            $mission
=======
            $missionTabRepository
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
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
<<<<<<< HEAD
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
=======
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
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
<<<<<<< HEAD
     * @param  App\Models\MissionImpactDonation $missionImpactDonation
=======
     * @param  App\Models\Organization $organization
     * @param  App\Models\MissionTab $missionTab
     * @param  App\Models\MissionTabLanguage $missionTabLanguage
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
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
        City $city,
<<<<<<< HEAD
        MissionImpactDonation $missionImpactDonation
=======
        Organization $organization,
        MissionTab $missionTab,
        MissionTabLanguage $missionTabLanguage
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
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
            $city,
<<<<<<< HEAD
            $missionImpactDonation,
        );
    }
}
=======
            $organization,
            $missionTab,
            $missionTabLanguage
        );
    }
}
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
