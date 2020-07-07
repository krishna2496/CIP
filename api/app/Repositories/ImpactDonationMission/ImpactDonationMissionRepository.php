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

    /**
    * @var App\Services\Mission\ModelsService
    */
    private $modelsService;

    /**
     * Create a new ImpactDonationMission repository instance.
     *
     * @param  Mission $mission
     * @param  ResponseHelper $responseHelper
    * @param   App\Services\Mission\ModelsService $modelsService
* @param       App\Models\MissionImpactDonationLanguage $missionImpactDonationLanguage 
     * @return void
     */
    public function __construct(
        Mission $mission,
        ResponseHelper $responseHelper,
        ModelsService $modelsService,
        MissionImpactDonationLanguage $missionImpactDonationLanguage
    ) {
        $this->mission = $mission;
        $this->responseHelper = $responseHelper;
        $this->modelsService = $modelsService;
        $this->missionImpactDonationLanguage = $missionImpactDonationLanguage;
    }

    /**
     * Save impact donation mission details
     * 
     * @param array $request
     * @param int $missionId
     * @return void
     */

    public function store(array $impactDonationMission, int $missionId)
    {
        $languages = $this->languageHelper->getLanguages();
        foreach ($impactDonationMission as $impactDonationValue) {
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
                    'language_id' => $language->language_id,
                    'content' => json_encode($impactDonationLanguageValue['sections'])
                ];
                $impactDonationLanguage = $this->missionImpactDonationLanguage->create($impactDonationLanguageArray);
                unset($impactDonationLanguageArray);
            }
            unset($impactDonationArray);
        }
    }

    /**
    * Update impact donation mission details
    *
    * @param array $missionDonationValue
    * @param int $missionId
    * @return array
    */
    public function update(array $missionDonationValue, int $missionId)
    {
        $languages = $this->languageHelper->getLanguages();
        $missionImpactDonationId = $missionDonationValue['impact_donation_id'];
        if (isset($missionTabValue['amount'])) {
            $missionTab = $this->modelsService->missionImpactDonation->where(["mission_impact_donation_id"=>$missionImpactDonationId])->update(['sort_key'=>$missionTabValue['sort_key']]);
        }

        if (isset($missionDonationValue['translations'])) {
            foreach ($missionDonationValue['translations'] as $impactDonationLanguageValue) {
                $language = $languages->where('code', $impactDonationLanguageValue['language_code'])->first();
                $impactDonationArray['mission_impact_donation_language_id'] = (String) Str::uuid();
                $impactDonationArray['impact_donation_id'] = $missionImpactDonationId;
                $impactDonationArray['language_id'] = $language->language_id;
                                
                if (isset($impactDonationLanguageValue['content'])) {
                    $impactDonationLanguageValue['content'] = json_encode($impactDonationLanguageValue['content']);
                }

                $impactDonationTranslation = $this->missionImpactDonationLanguage->createOrUpdateDonationImpactTranslation(['mission_tab_id' => $missionTabId,
                                'language_id' => $language->language_id], $impactDonationArray);
                unset($impactDonationArray);
            }
        }
    }
}
