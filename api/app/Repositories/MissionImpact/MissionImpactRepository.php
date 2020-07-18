<?php
namespace App\Repositories\MissionImpact;

use App\Models\MissionImpact;
use App\Helpers\LanguageHelper;
use App\Models\MissionImpactLanguage;
use App\Repositories\MissionImpact\MissionImpactInterface;

class MissionImpactRepository implements MissionImpactInterface
{
    /**
     * @var App\Models\MissionImpact
     */
    private $missionImpactModel;

    /**
     * @var App\Models\MissionImpactLanguage
     */
    private $missionImpactLanguageModel;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new MissionImpact repository instance.
     *
     * @param App\Models\MissionImpact $missionImpactModel
     * @param App\Models\MissionImpactLanguage $missionImpactLanguageModel
     * @param App\Helpers\LanguageHelper $languageHelper
     */
    public function __construct(
        MissionImpact $missionImpactModel,
        MissionImpactLanguage $missionImpactLanguageModel,
        LanguageHelper $languageHelper
    ) {
        $this->missionImpactModel = $missionImpactModel;
        $this->missionImpactLanguageModel = $missionImpactLanguageModel;
        $this->languageHelper = $languageHelper;
    }

    /**
     * Save impact mission details
     *
     * @param array $impactMission
     * @param int $missionId
     * @param int $defaultTenantLanguageId
     * @return void
     */
    public function store(array $missionImpact, int $missionId, int $defaultTenantLanguageId)
    {
        // dd($missionImpact);

        $languages = $this->languageHelper->getLanguages();
        $missionImpactPostData = [
            'mission_id' => $missionId,
            'icon' => isset($missionImpact['icon_path']) ? $missionImpact['icon_path'] : null,
            'sort_key' => $missionImpact['sort_key']
        ];

        $missionImpactModelData = $this->missionImpactModel->create($missionImpactPostData);

        foreach ($missionImpact['translations'] as $missionImpactValue) {
            $language = $languages->where('code', $missionImpactValue['language_code'])->first();
            $missionImpactLanguagePostData = [
                    'mission_impact_id' => $missionImpactModelData['mission_impact_id'],
                    'language_id' => !empty($language) ? $language->language_id : $defaultTenantLanguageId,
                    'content' => json_encode($missionImpactValue['content'])
                ];
            $this->missionImpactLanguageModel->create($missionImpactLanguagePostData);
            unset($missionImpactLanguagePostData);
        }

        dd("test");

        unset($missionImpactPostData);
    }
}
