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
     * @param array $missionImpact
     * @param int $missionId
     * @param int $defaultTenantLanguageId
     * @return void
     */
    public function store(array $missionImpact, int $missionId, int $defaultTenantLanguageId)
    {
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

        unset($missionImpactPostData);
    }

    /**
    * Update mission impact details
    *
    * @param array $missionImpact
    * @param int $missionId
    * @param int $defaultTenantLanguageId
    * @return void
    */
    public function update(array $missionImpact, int $missionId, int $defaultTenantLanguageId)
    {
        $languages = $this->languageHelper->getLanguages();
        $missionImpactId = $missionImpact['mission_impact_id'];

        if (isset($missionImpact['sort_key'])) {
            $this->missionImpactModel
            ->where(["mission_impact_id" => $missionImpactId])
            ->update(['sort_key' => $missionImpact['sort_key']]);
        }

        if (isset($missionImpact['translations'])) {
            foreach ($missionImpact['translations'] as $missionImpactLanguageValue) {
                $language = $languages->where('code', $missionImpactLanguageValue['language_code'])
                ->first();
                $missionImpactPostData['mission_impact_id'] = $missionImpactId;
                $missionImpactPostData['language_id'] =
                !empty($language)  ? $language->language_id : $defaultTenantLanguageId;

                if (isset($missionImpactLanguageValue['content'])) {
                    $missionImpactPostData['content'] = json_encode($missionImpactLanguageValue['content']);
                }

                $languageId = !empty($language)  ? $language->language_id : $defaultTenantLanguageId;
                $this->missionImpactLanguageModel
                    ->createOrUpdateMissionImpactTranslation(['mission_impact_id' => $missionImpactId,
                    'language_id' => $languageId], $missionImpactPostData);
                unset($missionImpactPostData);
            }
        }
    }
}
