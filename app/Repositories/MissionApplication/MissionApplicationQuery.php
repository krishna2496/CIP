<?php

namespace App\Repositories\MissionApplication;

use App\Models\DataObjects\VolunteerApplication;
use App\Models\MissionApplication;
use App\Repositories\Core\QueryableInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MissionApplicationQuery implements QueryableInterface
{
    const FILTER_APPLICATION_IDS    = 'applicationIds';
    const FILTER_APPLICATION_DATE   = 'applicationDate';
    const FILTER_APPLICANT_SKILLS   = 'applicationSkills';
    const FILTER_MISSION_SKILLS     = 'missionSkills';
    const FILTER_MISSION_THEMES     = 'missionThemes';
    const FILTER_MISSION_TYPES      = 'missionTypes';

    const ALLOWED_SORTABLE_FIELDS = [
        'applicationId' => 'ma.mission_application_id',
        'applicantFirstName' => 'u.first_name',
        'applicantLastName' => 'u.last_name',
        'applicantEmail' => 'u.email',
        'missionName' => 'ml.title',
        'country' => 'c.name',
        'city' => 'ci.name',
        'missionTypes' => 'm.mission_type',
        'applicationDate' => 'ma.applied_at',
    ];

    const ALLOWED_SORTING_DIR = ['ASC', 'DESC'];

    const SEARCHABLE_COLUMNS = [
        'u.first_name',
        'u.last_name',
        'u.email',
        'ml.title',
        'c.name',
        'ci.name'
    ];

    const IN_COLUMN_MAPPINGS = [
        'missionSkills' => 'ms.skill_id',
        'applicantSkills' => 'us.skill_id',
        'missionThemes' => 'm.theme_id',
        'applicationIds' => 'ma.mission_application_id',
        'missionTypes' => 'm.mission_type',
    ];

    const RANGE_COLUMN_MAPPINGS = [
        'applicationDate' => 'ma.applied_at'
    ];

    /**
     * @param array $parameters
     * @return LengthAwarePaginator
     */
    public function run($parameters = [])
    {
        $filters = $parameters['filters'];
        $search = $parameters['search'];
        $order = $parameters['order'];
        $limit = $this->getLimit($parameters['limit']);

        $order = $this->getOrder($order);

        $inClauses = [];
        $whereClauses = [];

        foreach ($filters as $filterKey => $values) {
            if (array_key_exists($filterKey, self::IN_COLUMN_MAPPINGS) && !empty($values)) {
                $inClauses[] = [self::IN_COLUMN_MAPPINGS[$filterKey], $values];
            } elseif (array_key_exists($filterKey, self::RANGE_COLUMN_MAPPINGS)) {
                if (isset($values['from']) && !is_null($values['from'])) {
                    $whereClauses[] = [self::RANGE_COLUMN_MAPPINGS[$filterKey], '>=', $values['from']];
                }

                if (isset($values['to']) && !is_null($values['to'])) {
                    $whereClauses[] = [self::RANGE_COLUMN_MAPPINGS[$filterKey], '<=', $values['to']];
                }
            }
        }

        $query = MissionApplication::query();
        $applications = $query
            ->with([
                'user:user_id,first_name,last_name,avatar,email',
                'user.skills',
                'mission',
                'mission.missionLanguage',
                'mission.missionSkill',
                'mission.country',
            ])
            ->when(isset($filters[self::FILTER_APPLICATION_IDS]), function($query) use ($filters) {
                $query->whereIn('mission_application_id', $filters[self::FILTER_APPLICATION_IDS]);
            })
            ->when(isset($filters[self::FILTER_APPLICATION_DATE]['from']), function($query) use ($filters) {
                $query->where('applied_at', '>=', $filters[self::FILTER_APPLICATION_DATE]['from']);
            })
            ->when(isset($filters[self::FILTER_APPLICATION_DATE]['to']), function($query) use ($filters) {
                $query->where('applied_at', '<=', $filters[self::FILTER_APPLICATION_DATE]['to']);
            })
            ->whereHas('user.skills', function($query) use ($filters) {
                $query->when(isset($filters[self::FILTER_APPLICANT_SKILLS]), function($query) use ($filters) {
                    $query->whereIn('user_skill_id', $filters[self::FILTER_APPLICANT_SKILLS]);
                });
            })
            ->whereHas('mission', function($query) use ($filters) {
                $query->when(isset($filters[self::FILTER_MISSION_THEMES]), function($query) use ($filters) {
                    $query->whereIn('theme_id', $filters[self::FILTER_MISSION_THEMES]);
                });
                $query->when(isset($filters[self::FILTER_MISSION_TYPES]), function($query) use ($filters) {
                    $query->whereIn('mission_type', $filters[self::FILTER_MISSION_TYPES]);
                });
            })
            ->whereHas('mission.missionSkill', function($query) use ($filters) {
                $query->when(isset($filters[self::FILTER_MISSION_SKILLS]), function($query) use ($filters) {
                    $query->whereIn('mission_skill_id', $filters[self::FILTER_MISSION_SKILLS]);
                });
            })
            ->when($limit, function ($query) use ($limit) {
                $query->offset($limit['offset']);
                $query->limit($limit['limit']);
            })
            ->get();

        $total = $applications->count() > 0 ? $applications->first()->total : 0;
        return new LengthAwarePaginator($applications, $total, $limit['limit'], $limit['offset']);

        $applications = DB::table('mission_application AS ma')
            ->select([
                DB::raw('count(distinct mission_application_id) AS total'),
                'ma.mission_application_id as id',
                'ma.applied_at as applicationDate',
                'ma.motivation as applicantMotivation',
                'ma.approval_status as applicationStatus',
                'ml.mission_id as missionId',
                'ml.title as missionName',
                'm.mission_type as missionType',
                'm.theme_id as missionThemeId',
                'c.name AS countryName',
                'c.iso AS countryCode',
                'ci.name AS city',
                DB::raw('group_concat(distinct ms.skill_id) as missionSkills'),
                'u.user_id AS applicantId',
                'u.first_name AS applicantFirstName',
                'u.last_name AS applicantLastName',
                'u.email AS applicantEmail',
                'u.avatar AS applicantAvatar',
                DB::raw('group_concat(distinct us.skill_id) as applicantSkills'),
            ])
            ->join('mission AS m', 'ma.mission_id', '=', 'm.mission_id')
            ->join('mission_language AS ml', 'm.mission_id', '=', 'ml.mission_id')
            ->join('country AS c', 'm.country_id', '=', 'c.country_id')
            ->join('city AS ci', 'm.city_id', '=', 'ci.city_id')
            ->leftJoin('mission_skill AS ms', 'm.mission_id', '=', 'ms.mission_id')
            ->join('user AS u', 'u.user_id', '=', 'ma.user_id')
            ->leftJoin('user_skill AS us', 'us.user_id', '=', 'u.user_id')
            ->when($inClauses, function (Builder $query) use ($inClauses) {
                foreach ($inClauses as $inClause) {
                    $query
                        ->whereIn($inClause[0], $inClause[1]);
                }
            })
            ->when($whereClauses, function (Builder $query) use ($whereClauses) {
                foreach ($whereClauses as $whereClause) {
                    $query
                        ->where($whereClause[0], $whereClause[1], $whereClause[2]);
                }
            })
            ->when($search, function (Builder $query) use ($search) {
                foreach (self::SEARCHABLE_COLUMNS as $column) {
                    $query->orWhere($column, 'like', '%'. $search .'%');
                }
            })
            ->groupBy('m.mission_id', 'u.user_id')
            ->when($limit, function (Builder $query) use ($limit) {
                $query->offset($limit['offset']);
                $query->limit($limit['limit']);
            })
            ->when($order, function (Builder $query) use ($order) {
                $query->orderBy($order['orderBy'], $order['orderDir']);
            })
            ->get();

        $total = $applications->count() > 0 ? $applications->first()->total : 0;

        $applications = $applications->map(function ($application) {
            return $this->makeEntity($application);
        });

        return new LengthAwarePaginator($applications, $total, $limit['limit'], $limit['offset']);
    }

    /**
     * @param $values
     * @return VolunteerApplication
     */
    private function makeEntity($values)
    {
        $application = new VolunteerApplication($values->applicantId, $values->applicationDate, $values->applicationStatus, $values->missionId);

        foreach ($values as $property => $value) {
            $setter = 'set' . ucfirst($property);

            if ($property === 'missionSkills' || $property === 'applicantSkills') {
                if (is_null($value)) {
                    $value = [];
                } else {
                    $value = explode(',', $value);
                }
            }

            if (method_exists($application, $setter)) {
                $application->$setter($value);
            }
        }

        return $application;
    }

    /**
     * @param $order
     * @return mixed
     */
    private function getOrder($order)
    {
        if (array_key_exists('orderBy', $order)) {
            if (array_key_exists($order['orderBy'], self::ALLOWED_SORTABLE_FIELDS)) {
                $order['orderBy'] = self::ALLOWED_SORTABLE_FIELDS[$order['orderBy']];
            } else {
                // Default to application date
                $order['orderBy'] = self::ALLOWED_SORTABLE_FIELDS['applicationDate'];
            }

            if (array_key_exists('orderDir', $order)) {
                if (!in_array($order['orderDir'], self::ALLOWED_SORTING_DIR)) {
                    // Default to ASC
                    $order['orderDir'] = self::ALLOWED_SORTING_DIR[0];
                }
            } else {

                // Default to ASC
                $order['orderDir'] = self::ALLOWED_SORTING_DIR[0];
            }
        }
        return $order;
    }

    /**
     * @param array $limit
     * @return array
     */
    private function getLimit(array $limit): array
    {
        if (!array_key_exists('limit', $limit)) {
            $limit['limit'] = 25;
        }

        if (!array_key_exists('offset', $limit)) {
            $limit['offset'] = 0;
        }

        return $limit;
    }

}
