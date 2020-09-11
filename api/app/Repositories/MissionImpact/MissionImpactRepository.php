<?php
namespace App\Repositories\MissionImpact;

use App\Models\MissionImpact;
use App\Helpers\LanguageHelper;
use App\Models\MissionImpactLanguage;
use App\Repositories\MissionImpact\MissionImpactInterface;
use App\Helpers\S3Helper;

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
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
     * Create a new MissionImpact repository instance.
     *
     * @param App\Models\MissionImpact $missionImpactModel
     * @param App\Models\MissionImpactLanguage $missionImpactLanguageModel
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Helpers\S3Helper $s3helper
     */
    public function __construct(
        MissionImpact $missionImpactModel,
        MissionImpactLanguage $missionImpactLanguageModel,
        LanguageHelper $languageHelper,
        S3Helper $s3helper
    ) {
        $this->missionImpactModel = $missionImpactModel;
        $this->missionImpactLanguageModel = $missionImpactLanguageModel;
        $this->languageHelper = $languageHelper;
        $this->s3helper = $s3helper;
    }

    /**
     * Save impact mission details
     *
     * @param array $missionImpact
     * @param int $missionId
     * @param int $defaultTenantLanguageId
     * @param string $tenantName
     * @return void
     */
    public function store(array $missionImpact, int $missionId, int $defaultTenantLanguageId, string $tenantName)
    {
        $languages = $this->languageHelper->getLanguages();
        $missionImpactPostData = [
            'mission_id' => $missionId,
            'sort_key' => $missionImpact['sort_key']
        ];

        $missionImpactModelData = $this->missionImpactModel->create($missionImpactPostData);
        $missionImpactId = $missionImpactModelData->mission_impact_id;
        
        $iconPath = $this->s3helper->uploadFileOnS3Bucket(
            $missionImpact['icon_path'],
            $tenantName,
            "missions/$missionId/icons/$missionImpactId"
        );

        $missionImpactModelData->update(['icon_path' => $iconPath]);

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
    * @param string $tenantName
    * @return void
    */
    public function update(array $missionImpact, int $missionId, int $defaultTenantLanguageId, string $tenantName)
    {
        $languages = $this->languageHelper->getLanguages();
        $missionImpactId = $missionImpact['mission_impact_id'];

        // Update sort_key
        if (isset($missionImpact['sort_key']) && !empty($missionImpact['sort_key'])) {
            $this->missionImpactModel
            ->where(['mission_impact_id' => $missionImpactId])
            ->update(['sort_key' => $missionImpact['sort_key']]);
        }

        // Update icon
        if (isset($missionImpact['icon_path']) && !empty($missionImpact['icon_path'])) {
            $iconPath = $this->s3helper->uploadFileOnS3Bucket(
                $missionImpact['icon_path'],
                $tenantName,
                "missions/$missionId/icons/$missionImpactId"
            );

            $this->missionImpactModel
            ->where(['mission_impact_id' => $missionImpactId])
            ->update(['icon_path' => $iconPath]);
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
