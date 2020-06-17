<?php
namespace App\Repositories\MissionTab;

use App\Repositories\MissionTab\MissionTabInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use Illuminate\Support\Str;
use App\Services\Mission\ModelsService;

class MissionTabRepository implements MissionTabInterface
{
    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
    * @var App\Services\Mission\ModelsService
    */
    private $modelsService;

    /**
    * Create a new Mission repository instance.
    *
    * @param  App\Helpers\LanguageHelper $languageHelper
    * @param  App\Helpers\Helpers $helpers
    * @param  App\Helpers\S3Helper $s3helper
    * @param  App\Services\Mission\ModelsService $modelsService
    * @return void
    */

    public function __construct(
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        ModelsService $modelsService
    ) {
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
        $this->modelsService = $modelsService;
    }

    /**
    * Store a newly created resource into database
    *
    * @param \Illuminate\Http\Request $request
    * @param int $missionId
    * @return array
    */
    public function store(Request $request, int $missionId)
    {
        $languages = $this->languageHelper->getLanguages();
        foreach ($request->mission_tab_details as $missionTabValue) {
            $missionTabArray = [
                'id' => (String) Str::uuid(),
                'mission_id' => $missionId,
                'sort_key' => $missionTabValue['sort_key']
            ];
            $missionTab = $this->modelsService->missionTab->create($missionTabArray);
            foreach ($missionTabValue['translations'] as $missionTabLanguageValue) {
                $language = $languages->where('code', $missionTabLanguageValue['lang'])->first();
                $missionTabLangArray = [
                    'id' => (String) Str::uuid(),
                    'mission_tab_id' => $missionTab['id'],
                    'language_id' => $language->language_id,
                    'name' => $missionTabLanguageValue['name'],
                    'section' => json_encode($missionTabLanguageValue['sections'])
                ];
                $missionTabLanguage = $this->modelsService->missionTabLanguage->create($missionTabLangArray);
                unset($missionTabLangArray);
            }
            unset($missionTabArray);
        }
    }

    /**
    * Store a newly created resource into database
    *
    * @param array $missionTabValue
    * @param int $missionId
    * @return array
    */
    public function update(array $missionTabValue, int $missionId)
    {
        $languages = $this->languageHelper->getLanguages();
        $missionTabId = $missionTabValue['mission_tab_id'];
        if (isset($missionTabValue['sort_key'])) {
            $missionTab = $this->modelsService->missionTab->where(["id"=>$missionTabId])->update(['sort_key'=>$missionTabValue['sort_key']]);
        }

        if (isset($missionTabValue['translations'])) {
            foreach ($missionTabValue['translations'] as $missionTabLangValue) {
                $language = $languages->where('code', $missionTabLangValue['lang'])->first();
                $missionTabLangArray['id'] = (String) Str::uuid();
                $missionTabLangArray['mission_tab_id'] = $missionTabId;
                $missionTabLangArray['language_id'] = $language->language_id;
                                
                if (isset($missionTabLangValue['name'])) {
                    $missionTabLangArray['name'] = $missionTabLangValue['name'];
                }
                if (isset($missionTabLangValue['sections'])) {
                    $missionTabLangArray['section'] = json_encode($missionTabLangValue['sections']);
                }

                $missionTabLanguage = $this->modelsService->missionTabLanguage->createOrUpdateMissionTabLanguage(['mission_tab_id' => $missionTabId,
                                'language_id' => $language->language_id], $missionTabLangArray);
                unset($missionTabLangArray);
            }
        }
    }
}
