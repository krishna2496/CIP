<?php
namespace App\Repositories\ImpactDonationMission;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Models\Mission;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use App\Services\Mission\ModelsService;
use App\Models\MissionImpactDonationLanguage;
use App\Helpers\LanguageHelper;

class ImpactDonationMissionRepository
{
    /**
     * @var ResponseHelper
     */
    private $responseHelper;

    /**
     * @var Mission
     */
    public $mission;

    /**
     * @var App\Models\MissionImpactDonationLanguage MissionImpactDonationLanguage
     */
    private $missionImpactDonationLanguage;

    /**
    * @var App\Services\Mission\ModelsService
    */
    private $modelsService;

    /**
    * @var App\Helpers\LanguageHelper
    */
    private $languageHelper;

    /**
     * Create a new ImpactDonationMission repository instance.
     *
     * @param  Mission $mission
     * @param  ResponseHelper $responseHelper
     * @param  App\Services\Mission\ModelsService $modelsService
     * @param  App\Models\MissionImpactDonationLanguage $missionImpactDonationLanguage
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        Mission $mission,
        ResponseHelper $responseHelper,
        ModelsService $modelsService,
        MissionImpactDonationLanguage $missionImpactDonationLanguage,
        LanguageHelper $languageHelper
    ) {
        $this->mission = $mission;
        $this->responseHelper = $responseHelper;
        $this->modelsService = $modelsService;
        $this->missionImpactDonationLanguage = $missionImpactDonationLanguage;
        $this->languageHelper = $languageHelper;
    }

    /**
     * Save impact donation mission details
     *
     * @param array $impactDonationValue
     * @param int $missionId
     * @param int $defaultTenantLanguageId
     * @return void
     */

    public function store(array $impactDonationValue, int $missionId, int $defaultTenantLanguageId)
    {
        $languages = $this->languageHelper->getLanguages();
        $impactDonationArray = [
            'mission_impact_donation_id' => (String) Str::uuid(),
            'mission_id' => $missionId,
            'amount' => $impactDonationValue['amount']
        ];

        $missionImpactDonationModelData = $this->modelsService->missionImpactDonation->create($impactDonationArray);
        foreach ($impactDonationValue['translations'] as $impactDonationLanguageValue) {
            $language = $languages->where('code', $impactDonationLanguageValue['language_code'])->first();
            $impactDonationLanguageArray = [
                    'mission_impact_donation_language_id' => (String) Str::uuid(),
                    'impact_donation_id' => $missionImpactDonationModelData['mission_impact_donation_id'],
                    'language_id' => !empty($language) ? $language->language_id : $defaultTenantLanguageId,
                    'content' => json_encode($impactDonationLanguageValue['content'])
                ];
            $impactDonationLanguage = $this->missionImpactDonationLanguage->create($impactDonationLanguageArray);
            unset($impactDonationLanguageArray);
        }
        unset($impactDonationArray);
    }

    /**
    * Update impact donation mission details
    *
    * @param array $missionDonationValue
    * @param int $missionId
    * @param int $defaultTenantLanguageId
    * @return void
    */
    public function update(array $missionDonationValue, int $missionId, int $defaultTenantLanguageId)
    {
        $languages = $this->languageHelper->getLanguages();
        $missionImpactDonationId = $missionDonationValue['impact_donation_id'];
        if (isset($missionDonationValue['amount'])) {
            $this->modelsService->missionImpactDonation
            ->where(["mission_impact_donation_id"=>$missionImpactDonationId])
            ->update(['amount'=>$missionDonationValue['amount']]);
        }

        if (isset($missionDonationValue['translations'])) {
            foreach ($missionDonationValue['translations'] as $impactDonationLanguageValue) {
                $language = $languages->where('code', $impactDonationLanguageValue['language_code'])->first();
                $impactDonationArray['mission_impact_donation_language_id'] = (String) Str::uuid();
                $impactDonationArray['impact_donation_id'] = $missionImpactDonationId;
                $impactDonationArray['language_id'] = !empty($language)  ? $language->language_id : $defaultTenantLanguageId;
                                
                if (isset($impactDonationLanguageValue['content'])) {
                    $impactDonationArray['content'] = json_encode($impactDonationLanguageValue['content']);
                }

                $languageId = !empty($language)  ? $language->language_id : $defaultTenantLanguageId;
                $impactDonationTranslation = $this->missionImpactDonationLanguage
                    ->createOrUpdateDonationImpactTranslation(['impact_donation_id' => $missionImpactDonationId,
                    'language_id' => $languageId], $impactDonationArray);
                unset($impactDonationArray);
            }
        }
    }
}
