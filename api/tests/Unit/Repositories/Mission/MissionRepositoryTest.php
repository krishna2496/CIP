<?php

namespace Tests\Unit\Http\Repositories\Mission;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use App\Models\City;
use App\Models\FavouriteMission;
use App\Models\Mission;
use App\Models\MissionApplication;
use App\Models\MissionDocument;
use App\Models\MissionImpact;
use App\Models\MissionLanguage;
use App\Models\MissionRating;
use App\Models\MissionSkill;
use App\Models\MissionTab;
use App\Models\MissionTabLanguage;
use App\Models\Organization;
use App\Models\TimeMission;
use App\Repositories\Country\CountryRepository;
use App\Repositories\Mission\MissionRepository;
use App\Repositories\MissionImpact\MissionImpactRepository;
use App\Repositories\MissionMedia\MissionMediaRepository;
use App\Repositories\MissionTab\MissionTabRepository;
use App\Repositories\MissionUnitedNationSDG\MissionUnitedNationSDGRepository;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use App\Services\Mission\ModelsService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use App\User;
use Mockery;
use Validator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Mission\AdminMissionTransformService;
use App\Models\MissionImpactDonation;
use App\Repositories\ImpactDonationMission\ImpactDonationMissionRepository;
use App\Repositories\Currency\CurrencyRepository;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Ramsey\Uuid\Uuid;
use TestCase;

class MissionRepositoryTest extends TestCase
{
    /**
    * @testdox Test impact mission success
    *
    * @return void
    */
    public function testStoreImpactMissionSuccess()
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
            'organization' => [
                'organization_id' => 1,
                'organization_name' => str_random(10)
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
            'mission_detail'=> [
                [
                    'lang'=> 'en',
                    'title'=> 'New Organization Mission created',
                    'short_description'=> 'this is testing api with all mission details',
                    'objective'=> 'To test and check',
                    'label_goal_achieved'=> 'test percentage',
                    'label_goal_objective'=> 'check test percentage',
                    'section'=> [
                        [
                            'title'=> 'Section title',
                            'description'=> 'Section description'
                        ]
                    ],
                    'custom_information'=> [
                        [
                            'title'=> 'Customer info',
                            'description'=> 'Description of customer info'
                        ]
                    ]
                ]
            ],
            'impact' => [
                [
                    'icon_path' => str_random(100),
                    'sort_key' => rand(10000, 200000),
                    'translations' => [
                        [
                            'language_code' => 'en',
                            'content' => str_random(160)
                        ]
                    ]
                ]
            ],
            'volunteering_attribute' => [
                'total_seats' => rand(5000, 10000),
                'availability_id' => 1,
                'is_virtual' => 1
            ]
        ];

        $requestData = new Request($data);

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
        $missionImpact = $this->mock(MissionImpact::class);
        $collection = $this->mock(Collection::class);
        $adminMissionTransformService = $this->mock(AdminMissionTransformService::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $organization = $this->mock(Organization::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $currencyRepository = $this->mock(CurrencyRepository::class);
        $impactDonationMissionRepository = $this->mock(ImpactDonationMissionRepository::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $missionUnitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);
        $collection = $this->mock(Collection::class);
        $organization = $this->mock(Organization::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::Class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::Class);
        $missionImpact = $this->mock(MissionImpact::Class);

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
            $organization,
            $missionImpactDonation,
            $missionImpact,
            $missionTab,
            $missionTabLanguage
        );

        $languages = [
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

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $collectionLanguages = collect($languages);
        $organizationModel = new Organization();
        $organizationModel->organization_id = rand(50000, 70000);

        $modelService->organization->shouldReceive('updateOrCreate')
        ->once()
        ->with(['organization_id'=>$data['organization']['organization_id']], $requestData->organization)
        ->andReturn($organizationModel);

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

        $missionData = [
            'theme_id' => null,
            'city_id' => $requestData->location['city_id'],
            'country_id' => $countryId,
            'start_date' => null,
            'end_date' => null,
            'publication_status' => $requestData->publication_status,
            'organization_id' =>  $organizationModel->organization_id,
            'organisation_detail' => null,
            'mission_type' => $requestData->mission_type,
            'availability_id' => $requestData->volunteering_attribute['availability_id'],
            'total_seats' => $requestData->volunteering_attribute['total_seats'],
            'is_virtual' => $requestData->volunteering_attribute['is_virtual']
        ];

        $missionObject = new Mission();
        $missionObject->setAttribute('mission_id', 1);

        $hasOne = $this->mock(HasOne::class);
        $hasOne->shouldReceive('create')
            ->once()
            ->andReturn(true);

        $mission->shouldReceive('create')
            ->once()
            ->andReturn($mission)
            ->shouldReceive('volunteeringAttribute')
            ->once()
            ->andReturn($hasOne)
            ->shouldReceive('getAttribute')
            ->twice()
            ->with('mission_id')
            ->andReturn($missionObject->mission_id);

        $modelService->missionLanguage->shouldReceive('create')
        ->once()
        ->andReturn();

        $tenantName = str_random(10);
        $helpers->shouldReceive('getSubDomainFromRequest')
        ->once()
        ->with($requestData)
        ->andReturn($tenantName);

        $activatedTenantSetting =  [
            'volunteering',
            'mission_impact'
        ];
        
        $tenantActivatedSettingRepository->shouldReceive('getAllTenantActivatedSetting')
        ->once()
        ->with($requestData)
        ->andReturn($activatedTenantSetting);

        $missionImpactRepository->shouldReceive('store')
        ->once()
        ->andReturn();

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $impactDonationMissionRepository,
            $missionImpactRepository,
            $adminMissionTransformService,
            $tenantActivatedSettingRepository,
            $currencyRepository,
            $missionUnitedNationSDGRepository,
            $missionTabRepository
        );

        $response = $repository->store($requestData);
        
        $this->assertInstanceOf(mission::class, $response);
    }

    /**
    * @testdox Test mission tab deleted by mission_tab_id success
    *
    * @return void
    */
    public function testDeleteMissionTabByMissionTabIdSuccess()
    {
        $missionTabId = str_random(8).'-'.str_random(4).'-'.str_random(4).'-'.str_random(4).'-'.str_random(12);

        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $missionUnitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);
        $collection = $this->mock(Collection::class);
        $organization = $this->mock(Organization::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::Class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::Class);
        $missionImpact = $this->mock(MissionImpact::Class);

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
            $missionImpact,
            $organization,
            $missionTab,
            $missionTabLanguage
        );

        $modelService->missionTab
        ->shouldReceive('deleteMissionTabByMissionTabId')
        ->with($missionTabId)
        ->andReturn();

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $impactDonationMissionRepository,
            $missionImpactRepository,
            $adminMissionTransformService,
            $tenantActivatedSettingRepository,
            $currencyRepository,
            $missionUnitedNationSDGRepository,
            $missionTabRepository
        );

        $response = $repository->store($requestData);
        
        $this->assertInstanceOf(mission::class, $response);
    }

    /**
    * @testdox Test impact mission update success
    *
    * @return void
    */
    public function testUpdateImpactMissionSuccess()
    {
        $data = [
            'impact' => [
                [
                    'mission_impact_id' => str_random(36),
                    'icon_path' => str_random(100),
                    'sort_key' => rand(50000, 70000),
                    'translations' => [
                        [
                            'language_code' => 'fr',
                            'content' => str_random(160)
                        ]
                    ]
                ],
                [
                    'sort_key' => rand(50000, 70000),
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

        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::class);
        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionImpact = $this->mock(MissionImpact::class);
        $collection = $this->mock(Collection::class);
        $adminMissionTransformService = $this->mock(AdminMissionTransformService::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $organization = $this->mock(Organization::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $currencyRepository = $this->mock(CurrencyRepository::class);
        $impactDonationMissionRepository = $this->mock(ImpactDonationMissionRepository::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $collection = $this->mock(Collection::class);
        $organization = $this->mock(Organization::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $missionUnitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);

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
            $organization,
            $missionImpactDonation,
            $missionImpact,
            $missionTab,
            $missionTabLanguage
        );

        $languages = [
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

        $activatedTenantSetting =  [
            'volunteering',
            'mission_impact'
        ];
        
        $tenantActivatedSettingRepository->shouldReceive('getAllTenantActivatedSetting')
        ->times(2)
        ->with($requestData)
        ->andReturn($activatedTenantSetting);

        $missionImpactRepository->shouldReceive('update')
        ->once()
        ->andReturn();

        $missionImpactRepository->shouldReceive('store')
        ->once()
        ->andReturn();

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,$impactDonationMissionRepository,
            $missionImpactRepository,
            $adminMissionTransformService,
            $tenantActivatedSettingRepository,
            $currencyRepository,
            $missionUnitedNationSDGRepository,
            $missionTabRepository
        );

        $response = $repository->update($requestData, $missionId);

        $this->assertInstanceOf(mission::class, $response);
    }

    /**
    * @testdox mission impact linked to mission
    *
    */
    public function testMissionImpactLinkedToMissionSuccess()
    {
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::class);
        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionImpact = $this->mock(MissionImpact::class);
        $collection = $this->mock(Collection::class);
        $adminMissionTransformService = $this->mock(AdminMissionTransformService::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $organization = $this->mock(Organization::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $currencyRepository = $this->mock(CurrencyRepository::class);
        $impactDonationMissionRepository = $this->mock(ImpactDonationMissionRepository::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $missionUnitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);

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
            $organization,
            $missionImpactDonation,
            $missionImpact,
            $missionTab,
            $missionTabLanguage
        );

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $impactDonationMissionRepository,
            $missionImpactRepository,
            $adminMissionTransformService,
            $tenantActivatedSettingRepository,
            $currencyRepository,
            $missionUnitedNationSDGRepository,
            $missionTabRepository
        );

        $missionId = rand(50000, 70000);
        $missionImpactId = rand(50000, 70000);

        $modelService->missionImpact->shouldReceive('where')
        ->once()
        ->with([['mission_id', '=', $missionId], ['mission_impact_id', '=', $missionImpactId]])
        ->andReturn($missionImpact);

        $modelService->missionImpact->shouldReceive('firstOrFail')
        ->once()
        ->andReturn($missionImpact);

        $response = $repository->isMissionImpactLinkedToMission($missionId, $missionImpactId);
        $this->assertInstanceOf(MissionImpact::class, $response);
    }

    /**
    * @testdox Test mission tab deleted by mission_tab_id success
    *
    * @return void
    */
    public function testDeletingMissionTabByMissionTabIdSuccess()
    {
        $missionTabId = str_random(8).'-'.str_random(4).'-'.str_random(4).'-'.str_random(4).'-'.str_random(12);

        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $missionUnitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);
        $collection = $this->mock(Collection::class);
        $organization = $this->mock(Organization::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $missionImpact = $this->mock(MissionImpact::class);
        $impactDonationMissionRepository= $this->mock(ImpactDonationMissionRepository::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::class);
        $adminMissionTransformService = $this->mock(AdminMissionTransformService::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::Class);
        $currencyRepository = $this->mock(CurrencyRepository::class);

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
            $organization,
            $missionImpactDonation,
            $missionImpact,
            $missionTab,
            $missionTabLanguage,
        );

        $modelService->missionTab
        ->shouldReceive('deleteMissionTabByMissionTabId')
        ->with($missionTabId)
        ->andReturn();

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $impactDonationMissionRepository,
            $missionImpactRepository,
            $adminMissionTransformService,
            $tenantActivatedSettingRepository,
            $currencyRepository,
            $missionUnitedNationSDGRepository,
            $missionTabRepository
        );

        $response = $repository->deleteMissionTabByMissionTabId($missionTabId);
    }

    /**
     * @testdox Test store method focus on document upload on repository
     */
    public function testStoreDocumentUpload()
    {
        $organizationId = Uuid::uuid4()->toString();
        $params = [
            'organization' => [
                'organization_id' => $organizationId
            ],
            'location' => [
                'city_id' => 1,
                'country_code' => 'PH'
            ],
            'theme_id' => 1,
            'publication_status' => true,
            'availability_id' => 1,
            'mission_type' => config('constants.mission_type.GOAL'),
            'mission_detail' => [],
            'documents' => [
                [
                    'sort_order' => 0,
                    'document_path' => 'http://admin-m7pww5ymmj28.back.staging.optimy.net/assets/images/optimy-logo.png'
                ]
            ],
            'volunteering_attribute' => [
                'total_seats' => rand(5000, 10000),
                'availability_id' => 1,
                'is_virtual' => 1
            ]
        ];

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $request = new Request();
        $request->query->add($params);

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $organizationObject = factory(Organization::class)->make([
            'organization_id' => $request->organization['organization_id'],
            'name' => 'organizationName'
        ]);

        $organization = $this->mock(Organization::class);
        $organization->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'organization_id' => $organizationObject->organization_id
                ],
                $request->organization
            )
            ->andReturn($organizationObject);

        $languages = new Collection([
            [
                'code' => 'en'
            ]
        ]);

        $languageHelper = $this->mock(LanguageHelper::class);
        $languageHelper->shouldReceive('getLanguages')
            ->once()
            ->andReturn($languages);

        $languageHelper->shouldReceive('getDefaultTenantLanguage')
            ->once()
            ->with($request)
            ->andReturn($defaultLanguage);

        $countryId = 1;
        $countryRepository = $this->mock(CountryRepository::class);
        $countryRepository->shouldReceive('getCountryId')
            ->once()
            ->with($params['location']['country_code'])
            ->andReturn($countryId);

        $missionData = [
            'theme_id' => 1,
            'city_id' => 1,
            'country_id' => $countryId,
            'start_date' => null,
            'end_date' => null,
            'publication_status' => $request->publication_status,
            'organization_id' => $organizationObject->organization_id,
            'organisation_detail' => null,
            'mission_type' => $request->mission_type
        ];

        $missionObject = new Mission();
        $missionObject->setAttribute('mission_id', 1);

        $hasOne = $this->mock(HasOne::class);
        $hasOne->shouldReceive('create')
            ->once()
            ->andReturn(true);
        $modelService = $this->mock(ModelsService::class);
        $mission = $this->mock(Mission::class);
        $mission->shouldReceive('create')
            ->once()
            ->with($missionData)
            ->andReturn($mission)
            ->shouldReceive('volunteeringAttribute')
            ->once()
            ->andReturn($hasOne)
            ->shouldReceive('getAttribute')
            ->twice()
            ->with('mission_id')
            ->andReturn($missionObject->mission_id);

        $tenantName = 'tenantName';

        $helpers = $this->mock(Helpers::class);
        $helpers->shouldReceive('getSubDomainFromRequest')
            ->once()
            ->with($request)
            ->andReturn($tenantName);

        $documentId = 1;
        $documentObject = factory(MissionDocument::class)->make([
            'mission_document_id' => $documentId,
            'sort_order' => $request->documents[0]['sort_order'],
            'document_path' => $request->documents[0]['document_path']
        ]);

        $missionDocument = $this->mock(MissionDocument::class);
        $missionDocument->shouldReceive('create')
            ->once()
            ->with([
                'mission_id' => $missionObject->mission_id,
                'sort_order' => $documentObject->sort_order
            ])
            ->andReturn($documentObject);

        $s3Helper = $this->mock(S3Helper::class);
        $s3Helper->shouldReceive('uploadFileOnS3Bucket')
            ->once()
            ->with(
                $documentObject->document_path,
                $tenantName,
                "missions/$missionObject->mission_id/documents/$documentId"
            )
            ->andReturn($documentObject->document_path);

        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $missionUnitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $missionImpact = $this->mock(MissionImpact::class);
        $impactDonationMissionRepository = $this->mock(ImpactDonationMissionRepository::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::class);
        $adminMissionTransformService = $this->mock(AdminMissionTransformService::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $currencyRepository = $this->mock(CurrencyRepository::class);

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
            $missionImpact,
            $organization,
            $missionImpactDonation,
            $missionImpact,
            $missionTab,
            $missionTabLanguage
        );

        $response = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $impactDonationMissionRepository,
            $missionImpactRepository,
            $adminMissionTransformService,
            $tenantActivatedSettingRepository,
            $currencyRepository,
            $missionUnitedNationSDGRepository,
            $missionTabRepository
        )->store($request);
    }

    /**
     * @testdox Test update method focus on document upload on repository
     */
    public function testUpdateDocumentUpload()
    {
        $params = [
            'publication_status' => true,
            'mission_type' => config('constants.mission_type.GOAL'),
            'documents' => [
                [
                    'document_id' => 1,
                    'sort_order' => 0,
                    'document_path' => 'http://admin-m7pww5ymmj28.back.staging.optimy.net/assets/images/optimy-logo.png'
                ]
            ]
        ];
        $request = new Request();
        $request->query->add($params);

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $languages = new Collection([
            [
                'code' => 'en'
            ]
        ]);

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $languageHelper = $this->mock(LanguageHelper::class);
        $languageHelper->shouldReceive('getLanguages')
            ->once()
            ->andReturn($languages);

        $languageHelper->shouldReceive('getDefaultTenantLanguage')
            ->once()
            ->with($request)
            ->andReturn($defaultLanguage);

        $missionId = 1;
        $missionObject = new Mission();
        $missionObject->setAttribute('mission_id', $missionId);

        $mission = $this->mock(Mission::class);
        $mission->shouldReceive('findOrFail')
            ->once()
            ->with($missionId)
            ->andReturn($missionObject);

        $tenantName = 'tenantName';

        $helpers = $this->mock(Helpers::class);
        $helpers->shouldReceive('getSubDomainFromRequest')
            ->once()
            ->with($request)
            ->andReturn($tenantName);

        $documentId = $request->documents[0]['document_id'];
        $documentObject = factory(MissionDocument::class)->make([
            'mission_document_id' => $documentId,
            'sort_order' => $request->documents[0]['sort_order'],
            'document_path' => $request->documents[0]['document_path']
        ]);

        $missionDocument = $this->mock(MissionDocument::class);
        $missionDocument->shouldReceive('createOrUpdateDocument')
            ->once()
            ->with([
                'mission_id' => $missionId,
                'mission_document_id' => $documentId
            ], [
                'mission_id' => $missionId,
                'sort_order' => 0
            ])
            ->andReturn($documentObject);

        $s3Helper = $this->mock(S3Helper::class);
        $s3Helper->shouldReceive('uploadFileOnS3Bucket')
        ->once()
        ->with(
            $documentObject->document_path,
            $tenantName,
            "missions/$missionId/documents/$documentId"
        )
        ->andReturn($documentObject->document_path);

        $countryRepository = $this->mock(CountryRepository::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $missionUnitedNationSDGRepository = $this->mock(MissionUnitedNationSDGRepository::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $organization = $this->mock(Organization::class);
        $missionImpactDonation = $this->mock(MissionImpactDonation::class);
        $missionImpact = $this->mock(MissionImpact::class);
        $impactDonationMissionRepository = $this->mock(ImpactDonationMissionRepository::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::class);
        $adminMissionTransformService = $this->mock(AdminMissionTransformService::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $currencyRepository = $this->mock(CurrencyRepository::class);

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
            $missionImpact,
            $organization,
            $missionImpactDonation,
            $missionImpact,
            $missionTab,
            $missionTabLanguage
        );

        $response = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $impactDonationMissionRepository,
            $missionImpactRepository,
            $adminMissionTransformService,
            $tenantActivatedSettingRepository,
            $currencyRepository,
            $missionUnitedNationSDGRepository,
            $missionTabRepository
        )->update($request, $missionId);
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
     * @param  App\Repositories\MissionMedia\ImpactDonationMissionRepository $impactDonationMissionRepository
     * @param  App\Repositories\MissionImpact\MissionImpactRepository $missionImpactRepository
     * @param  App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository $tenantActivatedSettingRepository
     * @param  App\Repositories\Currency\CurrencyRepository $currencyRepository
     * @param  App\Repositories\MissionMedia\MissionTabRepository $missionTabRepository
     * @param  App\Repositories\MissionMedia\MissionUnitedNationSDGRepository $missionUnitedNationSDGRepository
     * @return void
     */
    private function getRepository(
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        CountryRepository $countryRepository,
        MissionMediaRepository $missionMediaRepository,
        ModelsService $modelsService,
        ImpactDonationMissionRepository $impactDonationMissionRepository,
        MissionImpactRepository $missionImpactRepository,
        AdminMissionTransformService $adminMissionTransformService,
        TenantActivatedSettingRepository $tenantActivatedSettingRepository,
        CurrencyRepository $currencyRepository,
        MissionUnitedNationSDGRepository $missionUnitedNationSDGRepository,
        MissionTabRepository $missionTabRepository
    ) {
        return new MissionRepository(
            $languageHelper,
            $helpers,
            $s3helper,
            $countryRepository,
            $missionMediaRepository,
            $modelsService,
            $impactDonationMissionRepository,
            $missionImpactRepository,
            $adminMissionTransformService,
            $tenantActivatedSettingRepository,
            $currencyRepository,
            $missionUnitedNationSDGRepository,
            $missionTabRepository
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
     * @param  App\Models\MissionImpact $missionImpact
     * @param  App\Models\Organization $organization
     * @param  App\Models\MissionImpactDonation $missionImpactDonation
     * @param  App\Models\MissionImpact $missionImpact
     * @param  App\Models\MissionTab $missionTab
     * @param  App\Models\MissionTabLanguage $missionTabLanguage
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
        MissionImpact $missionImpact,
        Organization $organization,
        MissionImpactDonation $missionImpactDonation,
        MissionImpact $missionImpact,
        MissionTab $missionTab,
        MissionTabLanguage $missionTabLanguage
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
            $missionImpact,
            $organization,
            $missionImpactDonation,
            $missionImpact,
            $missionTab,
            $missionTabLanguage
        );
    }
}
