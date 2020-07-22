<?php

namespace Tests\Unit\Services\Admin;

use App\Services\Mission\AdminMissionTransformService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use TestCase;
use App\Helpers\LanguageHelper;
use App\Models\Mission;

class AdminMissionTransformServiceTest extends TestCase
{
    /**
    * @testdox Test mission transform service success
    *
    * @return void
    */
    public function testMissionTransformServiceSuccess()
    {
        $languageHelper = $this->mock(LanguageHelper::class);

        $missionModel = new Mission();
        $missionModel->impactMission = (object)[
            [
                "mission_impact_id" => str_random(36),
                "icon" => str_random(100),
                "sort_key" => rand(100, 200),
                "mission_impact_language_details" => [
                    [
                        "language_id" => 1,
                        "content" => json_encode(str_random(200))
                    ]
                ]
            ]
        ];

        $missionModel->impactMission = collect($missionModel->impactMission);

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

        $service = $this->getService(
            $languageHelper
        );

        $response = $service->transfromAdminMission($missionModel);
        $this->assertNull(null);
    }

    /**
     * Create a new service instance.
     *
     * @param  App\Helpers\LanguageHelper $languageHelper
     *
     * @return void
     */
    private function getService(
        LanguageHelper $languageHelper
    ) {
        return new AdminMissionTransformService(
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
