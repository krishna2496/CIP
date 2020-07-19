<?php
namespace App\Services\Mission;

use App\Helpers\LanguageHelper;
use App\Models\Mission;

class AdminMissionTransformService
{

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new service instance
     *
     * @param  App\Helpers\LanguageHelper $languageHelper
     */
    public function __construct(
        LanguageHelper $languageHelper
    ) {
        $this->languageHelper = $languageHelper;
    }

    /**
     * Transfrom getting mission data into proper format
     *
     * @param  $mission
     * @return void
     */
    public function transfromAdminMission($mission)
    {
        // Transform impact mission attribute
        $languages = $this->languageHelper->getLanguages();
        $impactMission =  $mission['impactMission']->toArray();
        if ($impactMission != null) {
            $impactMissionDetails = [];
            foreach ($impactMission as $impactMissionKey => $impactMissionValue) {
                $impactMissionDetails['icon'] = $impactMissionValue['icon'];
                $impactMissionDetails["languages"] = [];
                foreach ($impactMissionValue['mission_impact_language_details'] as $impactMissionLanguageValue) {
                    $languageCode = $languages->where('language_id', $impactMissionLanguageValue['language_id'])
                        ->first()->code;
                    $impactMissionLanguage['language_id'] = $impactMissionLanguageValue['language_id'];
                    $impactMissionLanguage['language_code'] = $languageCode;
                    $impactMissionLanguage['content'] = json_decode($impactMissionLanguageValue['content']);
                    array_push($impactMissionDetails["languages"], $impactMissionLanguage);
                }

                $mission['impactMission'][$impactMissionKey] = $impactMissionDetails;
            }
        }
    }
}
