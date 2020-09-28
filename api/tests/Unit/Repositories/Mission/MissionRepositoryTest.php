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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Mockery;
use Ramsey\Uuid\Uuid;
use TestCase;

class MissionRepositoryTest extends TestCase
{
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
            $missionImpactRepository,
            $tenantActivatedSettingRepository,
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

        $response = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $missionImpactRepository,
            $tenantActivatedSettingRepository,
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
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $missionImpactRepository = $this->mock(MissionImpactRepository::Class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::Class);
        $missionImpact = $this->mock(MissionImpact::class);

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

        $response = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelService,
            $missionImpactRepository,
            $tenantActivatedSettingRepository,
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
     * @param  App\Repositories\MissionImpact\MissionImpactRepository $missionImpactRepository
     * @param  App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository $tenantActivatedSettingRepository
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
        MissionImpactRepository $missionImpactRepository,
        TenantActivatedSettingRepository $tenantActivatedSettingRepository,
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
            $missionImpactRepository,
            $tenantActivatedSettingRepository,
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
            $missionTab,
            $missionTabLanguage
        );
    }
}
