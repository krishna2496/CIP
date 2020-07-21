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
use App\Models\MissionTab;
use App\Models\MissionTabLanguage;
use App\Models\City;
use App\Models\DonationAttribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;


class MissionRepositoryTest extends TestCase
{
    /**
    * @testdox Test donation attribute add success
    *
    * @return void
    */
    public function testAddDonationMissionSuccess()
    {
        \DB::setDefaultConnection('tenant');
        

        $requestParams = [
            "theme_id" => 1,
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2022-10-15 10:40:00",
            "total_seats" => rand(10, 1000),
            "mission_type" => config("constants.mission_type.DONATION"),
            "publication_status" => config("constants.publication_status.APPROVED"),
            "organisation_id" => 1,
            "organisation_name" => str_random(10),
            "availability_id" => 1,
            "location" => [
                "city_id" => 1,
                "country_id" => 233,
                "country_code" => "US"
            ],
            "donation_attribute" => [
                "goal_amount_currency" => "CAD",
                "goal_amount" => 253,
                "show_goal_amount" => 1,
                "show_donation_percentage" => 0,
                "show_donation_meter"=> 0,
                "show_donation_count" =>0,
                "show_donors_count" =>0,
                "disable_when_funded" => 0,
                "is_disabled" => 0
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
              "organisation"=>[
                  "organisation_id"=> '1',
                  "organisation_name"=> 'namee'
              ]
              
        ];

        $languagesArray = [
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

        $requestData = new Request($requestParams);

        $mission = $this->mock(Mission::class);
        // $this->app->instance('Mission', $mission);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $city = $this->mock(City::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelsService = $this->mock(ModelsService::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $collection = $this->mock(Collection::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $donationAttribute = $this->mock(DonationAttribute::class);

        $modelsService = $this->modelService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city,
            $missionTab,
            $missionTabLanguage,
            $donationAttribute
        );
        $missionModel = new Mission();
        //dd($missionModel->mission_id);
        $missionModel->mission_id = 6587;
        $modelsService->mission
        ->shouldReceive('create')
        ->once()
        ->andReturn($missionModel);
       
        // ModelsService
        // $modelsService->shouldReceive('mission')
        //     ->once()
        //     ->andReturn($mission);

        //     $modelsService->shouldReceive('create')
        //     ->once()
        //     ->with($requestData->all())
        //     ->andReturn($mission);
        
        // $missionLanguageData = new Request(array(
        //     'mission_id' => $missionModel->mission_id,
        //     'language_id' => 1,
        //     'title' => 'New Organization Mission created',
        //     'short_description' => 'this is testing api with all mission details',
        //     'description' => [
        //         "title"=> "Section title",
        //         "description"=> "Section description"
        //     ],
        //     'objective' => 'To test and check',
        //     'custom_information' => [
        //         "title"=> "Customer info",
        //         "description"=> "Description of customer info"
        //       ],
        //     'label_goal_achieved' => 'test percentage',
        //     'label_goal_objective' => 'check test percentage'
        // ));
       // dd($missionLanguageData->all());
           

       // dd($modelsService->mission);
        // $modelsService->mission->shouldReceive('create')
        // ->once()
        // ->with($requestData->all())
        // ->andReturn($mission);

        // $modelsService->missionLanguage->shouldReceive('create')
        // ->once()
        // ->with($missionLanguageData->all())
        // ->andReturn(false);

        $collectionLanguageData = collect($languagesArray);
       
        $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collectionLanguageData);

        //     $collection->shouldReceive('where')
        //     ->once()
        //     ->with('code', $languagesArray[0]->code)
        //     ->andReturn($collectionLanguageData);

        //   $collection->shouldReceive('first')
        //     ->once()
        //     ->andReturn($languagesArray[0]);
        
        $countryId= $requestParams['location']['country_id'];
        
        $countryRepository->shouldReceive('getCountryId')
        ->once()
        ->with($requestData->location['country_code'])
        ->andReturn($countryId);

        //save missonLanguage
        
        // $missionLanguageData = array(
        //     'mission_id' => rand(1000,9999),
        //     'language_id' => 1,
        //     'title' => str_random(10),
        //     'short_description' => str_random(10),
        //     'description' => str_random(10),
        //     'objective' => str_random(10),
        //     'custom_information' => str_random(10),
        //     'label_goal_achieved' => str_random(10),
        //     'label_goal_objective' => str_random(10)
        // );
        //  $missionLanguageData = new Request(array(
        //     'mission_id' => $missionModel->mission_id,
        //     'language_id' => 1,
        //     'title' => 'New Organization Mission created',
        //     'short_description' => 'this is testing api with all mission details',
        //     'description' => [
        //         "title"=> "Section title",
        //         "description"=> "Section description"
        //     ],
        //     'objective' => 'To test and check',
        //     'custom_information' => [
        //         "title"=> "Customer info",
        //         "description"=> "Description of customer info"
        //       ],
        //     'label_goal_achieved' => 'test percentage',
        //     'label_goal_objective' => 'check test percentage'
        // ));
        $modelsService->missionLanguage->shouldReceive('create')
        ->once()
        ->andReturn(false);
        
        $modelsService->donationAttribute->shouldReceive('create')
        ->once()
        ->andReturn(false);
            
        // $mission->shouldReceive('donationAttribute')
        // ->once()
        // ->andReturn($donationModel);
        
        
        //->andReturn(false);

       
        
        
        $tenantName = str_random(10);
        $helpers->shouldReceive('getSubDomainFromRequest')
        ->once()
        ->with($requestData)
        ->andReturn($tenantName);

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelsService,
            $missionTabRepository
        );

        $response = $repository->store($requestData);
        
        $this->assertInstanceOf(mission::class, $response);
    }

    /**
    * @testdox Test donation attribute update success
    *
    * @return void
    */
    public function testUpdateDonationMissionSuccess()
    {
        \DB::setDefaultConnection('tenant');
        

        $requestParams = [
            
            "mission_type" => config("constants.mission_type.DONATION"),
            "location" => [
                "city_id" => 1,
                "country_id" => 233,
                "country_code" => "US"
            ],
            "donation_attribute" => [
                "goal_amount_currency" => "CAD",
                "goal_amount" => 253,
                "show_goal_amount" => 1,
                "show_donation_percentage" => 0,
                "show_donation_meter"=> 0,
                "show_donation_count" =>0,
                "show_donors_count" =>0,
                "disable_when_funded" => 0,
                "is_disabled" => 0
            ] 
        ];

        $languagesArray = [
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

        $requestData = new Request($requestParams);

        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);
        $city = $this->mock(City::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $s3Helper = $this->mock(S3Helper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $modelsService = $this->mock(ModelsService::class);
        $missionTabRepository = $this->mock(MissionTabRepository::class);
        $collection = $this->mock(Collection::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $donationAttribute = $this->mock(DonationAttribute::class);
        $hasOne = $this->mock(HasOne::class);
        $modelsService = $this->modelService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city,
            $missionTab,
            $missionTabLanguage,
            $donationAttribute
        );

        $missionModel = new Mission();
        $donationAttributeModel = new DonationAttribute();
        $missionId = 13;
        $missionModel->mission_id = $missionId;
        $modelsService->mission
        ->shouldReceive('findOrFail')
        ->once()
        ->with($missionId)
        ->andReturn($missionModel);
        
        $donationAttributeModel->mission_id = $missionId;

        $collectionLanguageData = collect($languagesArray);
       
        $languageHelper->shouldReceive('getLanguages')
        ->once()
        ->andReturn($collectionLanguageData);

        $countryId= $requestParams['location']['country_id'];
        
        $countryRepository->shouldReceive('getCountryId')
        ->once()
        ->with($requestData->location['country_code'])
        ->andReturn($countryId);
        
        $tenantName = str_random(10);
        $helpers->shouldReceive('getSubDomainFromRequest')
        ->once()
        ->with($requestData)
        ->andReturn($tenantName);

        $repository = $this->getRepository(
            $languageHelper,
            $helpers,
            $s3Helper,
            $countryRepository,
            $missionMediaRepository,
            $modelsService,
            $missionTabRepository
        );
        
        $response = $repository->update($requestData, $missionId);
        
        $this->assertInstanceOf(mission::class, $response);
    }

    /**
     * Create a new respository instance.
     *
     * @param  App\Models\mission
     * @return void
     */
    private function getRepository(
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        CountryRepository $countryRepository,
        MissionMediaRepository $missionMediaRepository,
        ModelsService $modelsService,
        MissionTabRepository $missionTabRepository
    ) {
        return new MissionRepository(
            $languageHelper,
            $helpers,
            $s3helper,
            $countryRepository,
            $missionMediaRepository,
            $modelsService,
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
     * @param  App\Models\MissionTab $missionTab
     * @param  App\Models\MissionTabLanguage $missionTabLanguage
     * @param  App\Models\DonationAttribute $donationAttribute
     * @return void
     */

    private function modelService(
        Mission $mission,
        TimeMission $timeMission,
        MissionLanguage $missionLanguage,
        MissionDocument $missionDocument,
        FavouriteMission $favouriteMission,
        MissionSkill $missionSkill,
        MissionRating $missionRating,
        MissionApplication $missionApplication,
        City $city,
        MissionTab $missionTab,
        MissionTabLanguage $missionTabLanguage,
        DonationAttribute $donationAttribute
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
            $missionTab,
            $missionTabLanguage,
            $donationAttribute
        );
    }
}
