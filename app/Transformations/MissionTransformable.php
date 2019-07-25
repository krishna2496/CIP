<?php
namespace App\Transformations;

use App\Models\Mission;
use App\Helpers\Helpers;

trait MissionTransformable
{
    private $helpers;

    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
    }

    /**
     * Select mission fields
     *
     * @param App\Models\Mission $mission
     * @param string $languageCode
     * @param string $type
     * @return App\Models\Mission
     */
    protected function transformMission(Mission $mission, string $languageCode, string $type): Mission
    {
        if (isset($mission['goalMission'])) {
            $mission['goal_objective']  = $mission['goalMission']['goal_objective'];
        }
        if (isset($mission['timeMission'])) {
            $mission['application_deadline'] = $mission['timeMission']['application_deadline'];
            $mission['application_start_date'] = $mission['timeMission']['application_start_date'];
            $mission['application_end_date'] = $mission['timeMission']['application_end_date'];
            $mission['application_start_time'] = $mission['timeMission']['application_start_time'];
            $mission['application_end_time'] = $mission['timeMission']['application_end_time'];
        }
        unset($mission['goalMission']);
        unset($mission['timeMission']);

        $mission['user_application_status']  = ($mission['missionApplication'][0]['approval_status']) ?? '';
        $mission['rating']  = ($mission['missionRating'][0]['rating']) ?? 0;
                
        $mission['is_favourite']  = (empty($mission['favouriteMission'])) ? 0 : 1;
        unset($mission['favouriteMission']);
        unset($mission['missionApplication']);
        
        // Set seats_left or already_volunteered
        if ($mission['total_seats'] != 0 && $mission['total_seats'] !== null) {
            $mission['seats_left'] = ($mission['total_seats']) - ($mission['mission_application_count']);
        } else {
            $mission['already_volunteered'] = $mission['mission_application_count'];
        }

        // Get defalut media image
        $mission['default_media_type'] = $mission['missionMedia'][0]['media_type'] ?? '';
        $mission['default_media_path'] = $mission['missionMedia'][0]['media_path'] ?? '';
        unset($mission['missionMedia']);
        unset($mission['city']);

        // Set title and description
        $mission['title'] = $mission['missionLanguage'][0]['title'] ?? '';
        $mission['short_description'] = $mission['missionLanguage'][0]['short_description'] ?? '';
        $mission['objective'] = $mission['missionLanguage'][0]['objective'] ?? '';
        unset($mission['missionLanguage']);

        // Check for apply in mission validity
        $mission['set_view_detail'] = 0;
        $today = $this->helpers->getUserTimeZoneDate(date(config("constants.DB_DATE_FORMAT")));
            
        if (($mission['user_application_count'] > 0) ||
                (isset($mission['application_deadline']) && $mission['application_deadline'] < $today) ||
                ($mission['total_seats'] != 0
                && $mission['total_seats'] == $mission['mission_application_count']) ||
                ($mission['end_date'] !== null && $mission['end_date'] < $today)
            ) {
            $mission['set_view_detail'] = 1;
        }

        $mission['mission_rating_count'] = $mission['mission_rating_count'] ?? 0;
              
        if (!empty($mission['missionSkill']) && ($type == config('constants.DETAIL'))) {
            foreach ($mission['missionSkill'] as $key => $value) {
                if ($value['skill']) {
                    $arrayKey = array_search($languageCode, array_column(
                        $value['skill']['translations'],
                        'lang'
                    ));
                    if ($arrayKey  !== '') {
                        $returnData[config('constants.SKILL')][$key]['title'] =
                        $value['skill']['translations'][$arrayKey]['title'];
                        $returnData[config('constants.SKILL')][$key]['id'] =
                        $value['skill']['skill_id'];
                    }
                }
            }
            $mission[config('constants.SKILL')] = $returnData[config('constants.SKILL')];
        }
        if ($type == config('constants.LIST')) {
            $mission['mission_rating']  = ($mission['missionRating']) ?? [];
        }
        unset($mission['missionRating']);
        unset($mission['missionSkill']);
        return $mission;
    }
}
