<?php

namespace App\Repositories\MissionApplication;

use App\Repositories\Core\QueryableInterface;
use Illuminate\Support\Facades\DB;

class MissionApplicationQuery implements QueryableInterface
{
    const ALLOWED_SORTABLE_FIELDS = [
        ''
    ];

    const COLUMN_MAPPINGS = [
        'missionSkills' => 'ms.skill_id',
        'applicantSkills' => 'us.skill_id',
        'missionThemes' => 'm.theme_id',
        'applicationDate' => 'ma.applied_at',
        'applicationIds' => 'ma.mission_application_id',
//        'cities' => 'ci.name',
    ];

    public function run($parameters = [])
    {
        $applications = DB::table('mission_application AS ma')
            ->select([
                'ma.applied_at',
                'ma.motivation',
                'ma.approval_status',
                'ma.availability_id',
                'ml.title',
                'm.mission_type',
                'm.theme_id',
                'c.iso',
                'ci.name',
                DB::raw('group_concat(distinct ms.skill_id) as mission_skills'),
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.avatar',
                DB::raw('group_concat(distinct us.skill_id) as user_skills'),
            ])
            ->join('mission AS m', 'ma.mission_id', '=', 'm.mission_id')
            ->join('mission_language AS ml', 'm.mission_id', '=', 'ml.mission_id')
            ->join('country AS c', 'm.country_id', '=', 'c.country_id')
            ->join('city AS ci', 'm.city_id', '=', 'ci.city_id')
            ->join('mission_skill AS ms', 'm.mission_id', '=', 'ms.mission_id')
            ->join('user AS u', 'u.user_id', '=', 'ma.user_id')
            ->join('user_skill AS us', 'us.user_id', '=', 'u.user_id')
//            ->where([
//                ['ml.language_id', '=', 2]
//            ])
            ->whereIn('u.last_name', ['Fauvarque', 'Delcourt'])
            ->groupBy('ms.mission_id', 'us.user_id')
            ->get();
        return $applications;

    }

}
