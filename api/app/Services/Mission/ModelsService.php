<?php
namespace App\Services\Mission;

use App\Models\Mission;
use App\Models\MissionLanguage;
use App\Models\MissionDocument;
use App\Models\FavouriteMission;
use App\Models\MissionSkill;
use App\Models\TimeMission;
use App\Models\MissionRating;
use App\Models\MissionApplication;
use App\Models\City;
use App\Models\Organization;
use App\Models\MissionImpactDonation;
use App\Models\MissionImpact;
use App\Models\MissionTab;
use App\Models\MissionTabLanguage;


class ModelsService
{
    /**
     * @var App\Models\Mission
     */
    public $mission;

    /**
     * @var App\Models\TimeMission
     */
    public $timeMission;

    /**
     * @var App\Models\FavouriteMission
     */
    public $favouriteMission;

    /**
     * @var App\models\MissionSkill
     */
    public $missionSkill;

    /**
     * @var App\Models\MissionLanguage
     */
    public $missionLanguage;

    /**
     * @var App\models\MissionDocument
     */
    public $missionDocument;

    /**
    * @var App\Models\MissionRating
    */
    public $missionRating;

    /**
    * @var App\Models\MissionApplication
    */
    public $missionApplication;

    /**
     * @var App\Models\City
     */
    public $city;

    /*
     * @var App\Models\Organization
     */
    public $organization;
    
    /** 
     * @var App\Models\MissionImpactDonation
     */
    public $missionImpactDonation;

    /**
     * @var App\Models\MissionImpact
     */
    public $missionImpact;

    /**
     * @var App\Models\MissionTab
     */
    public $missionTab;

    /**
     * @var App\Models\MissionTabLanguage
     */
    public $missionTabLanguage;

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
     * @param  App\Models\Organization $organization
     * @param  App\Models\MissionImpactDonation $missionImpactDonation
     * @param  App\Models\MissionImpact $missionImpact
     * @param  App\Models\MissionTab $missionTab
     * @param  App\Models\MissionTabLanguage $missionTabLanguage
     * @return void
     */
    public function __construct(
        Mission $mission,
        TimeMission $timeMission,
        MissionLanguage $missionLanguage,
        MissionDocument $missionDocument,
        FavouriteMission $favouriteMission,
        MissionSkill $missionSkill,
        MissionRating $missionRating,
        MissionApplication $missionApplication,
        City $city,
        Organization $organization,
        MissionImpactDonation $missionImpactDonation,
        MissionImpact $missionImpact,
        MissionTab $missionTab,
        MissionTabLanguage $missionTabLanguage
    ) {
        $this->mission = $mission;
        $this->timeMission = $timeMission;
        $this->missionLanguage = $missionLanguage;
        $this->missionDocument = $missionDocument;
        $this->favouriteMission = $favouriteMission;
        $this->missionSkill = $missionSkill;
        $this->missionRating = $missionRating;
        $this->missionApplication = $missionApplication;
        $this->city = $city;
        $this->organization = $organization;
        $this->missionImpactDonation = $missionImpactDonation;
        $this->missionImpact = $missionImpact;
        $this->missionTab = $missionTab;
        $this->missionTabLanguage = $missionTabLanguage;
    }
}
