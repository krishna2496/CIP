<?php

namespace Tests\Unit\Http\Controllers\App\Mission;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\S3Helper;
use App\Http\Controllers\App\Mission\MissionController;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use TestCase;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use App\Repositories\Mission\MissionRepository;
use App\Repositories\UserFilter\UserFilterRepository;
use App\Repositories\MissionTheme\MissionThemeRepository;
use App\Repositories\Skill\SkillRepository;
use App\Repositories\Country\CountryRepository;
use App\Repositories\City\CityRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\State\StateRepository;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use App\Repositories\UnitedNationSDG\UnitedNationSDGRepository;

class AppMissionControllerTest extends TestCase
{
    public function testGetMissionDetailGoalSetting()
    {
        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $userFilterRepository = $this->mock(UserFilterRepository::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $themeRepository = $this->mock(MissionThemeRepository::class);
        $skillRepository = $this->mock(SkillRepository::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $cityRepository = $this->mock(CityRepository::class);
        $userRepository = $this->mock(UserRepository::class);
        $stateRepository = $this->mock(StateRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $unitedNationSDGRepository = $this->mock(UnitedNationSDGRepository::class);
        
        $missionId = rand();
        $request = new Request();

        $languageReturn = (object)[
            'language_id' => 1,
            'code' => 'en'
        ];
        $missionReturn = [];
        $missionReturn[0] = new Mission();
        $missionReturn[0]->mission_type = 'GOAL';

        $missionReturn = new Collection($missionReturn);

        $languageHelper->shouldReceive('getLanguageDetails')
        ->once()
        ->with($request)
        ->andReturn($languageReturn);

        $missionRepository->shouldReceive('getMissionDetail')
        ->once()
        ->with($request, $missionId)
        ->andReturn($missionReturn);

        $tenantActivatedSettingRepository->shouldReceive('checkTenantSettingStatus')
        ->once()
        ->with(config('constants.tenant_settings.VOLUNTEERING_GOAL_MISSION'), $request)
        ->andReturn(false);

        $responseHelper->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_FORBIDDEN,
            Response::$statusTexts[Response::HTTP_FORBIDDEN],
            '',
            trans('messages.custom_error_message.ERROR_UNAUTHORIZED_USER')
        )->andReturn(new JsonResponse());

        $missionController = new MissionController(
            $missionRepository,
            $responseHelper,
            $userFilterRepository,
            $languageHelper,
            $helpers,
            $themeRepository,
            $skillRepository,
            $countryRepository,
            $cityRepository,
            $userRepository,
            $stateRepository,
            $tenantActivatedSettingRepository,
            $unitedNationSDGRepository
        );

        $response = $missionController->getMissionDetail($request, $missionId);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testGetMissionDetailTimeSetting()
    {
        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $userFilterRepository = $this->mock(UserFilterRepository::class);
        $languageHelper = $this->mock(LanguageHelper::class);
        $helpers = $this->mock(Helpers::class);
        $themeRepository = $this->mock(MissionThemeRepository::class);
        $skillRepository = $this->mock(SkillRepository::class);
        $countryRepository = $this->mock(CountryRepository::class);
        $cityRepository = $this->mock(CityRepository::class);
        $userRepository = $this->mock(UserRepository::class);
        $stateRepository = $this->mock(StateRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $unitedNationSDGRepository = $this->mock(UnitedNationSDGRepository::class);
        
        $missionId = rand();
        $request = new Request();

        $languageReturn = (object)[
            'language_id' => 1,
            'code' => 'en'
        ];
        $missionReturn = [];
        $missionReturn[0] = new Mission();
        $missionReturn[0]->mission_type = 'TIME';

        $missionReturn = new Collection($missionReturn);

        $languageHelper->shouldReceive('getLanguageDetails')
        ->once()
        ->with($request)
        ->andReturn($languageReturn);

        $missionRepository->shouldReceive('getMissionDetail')
        ->once()
        ->with($request, $missionId)
        ->andReturn($missionReturn);

        $tenantActivatedSettingRepository->shouldReceive('checkTenantSettingStatus')
        ->once()
        ->with(config('constants.tenant_settings.VOLUNTEERING_TIME_MISSION'), $request)
        ->andReturn(false);

        $responseHelper->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_FORBIDDEN,
            Response::$statusTexts[Response::HTTP_FORBIDDEN],
            '',
            trans('messages.custom_error_message.ERROR_UNAUTHORIZED_USER')
        )->andReturn(new JsonResponse());

        $missionController = new MissionController(
            $missionRepository,
            $responseHelper,
            $userFilterRepository,
            $languageHelper,
            $helpers,
            $themeRepository,
            $skillRepository,
            $countryRepository,
            $cityRepository,
            $userRepository,
            $stateRepository,
            $tenantActivatedSettingRepository,
            $unitedNationSDGRepository
        );

        $response = $missionController->getMissionDetail($request, $missionId);

        $this->assertInstanceOf(JsonResponse::class, $response);
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
