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
        'applicationId' => 'mission_application_id',
        'applicantFirstName' => 'u.first_name',
        'applicantLastName' => 'u.last_name',
        'applicantEmail' => 'u.email',
        'missionName' => 'ml.title',
        'country' => 'c.name',
        'city' => 'ci.name',
        'missionTypes' => 'm.mission_type',
        'applicationDate' => 'applied_at',
    ];

    const ALLOWED_SORTING_DIR = ['ASC', 'DESC'];

    /**
     * @param array $parameters
     * @return LengthAwarePaginator
     */
    public function run($parameters = [])
    {
        $filters = $parameters['filters'];
        $search = $parameters['search'];
        $order = $this->getOrder($parameters['order']);
        $limit = $this->getLimit($parameters['limit']);

        $hasMissionFilters = isset($filters[self::FILTER_MISSION_THEMES]) || isset($filters[self::FILTER_MISSION_TYPES]);

        $query = MissionApplication::query();
        $applications = $query
            ->with([
                'user:user_id,first_name,last_name,avatar,email',
                'user.skills',
                'mission',
                'mission.missionLanguage',
                'mission.missionSkill',
                'mission.country',
                'mission.city',
            ])
            // Filter by application ID
            ->when(isset($filters[self::FILTER_APPLICATION_IDS]), function($query) use ($filters) {
                Log::debug($filters[self::FILTER_APPLICATION_IDS]);
                $query->whereIn('mission_application_id', $filters[self::FILTER_APPLICATION_IDS]);
            })
            // Filter by application start date
            ->when(isset($filters[self::FILTER_APPLICATION_DATE]['from']), function($query) use ($filters) {
                $query->where('applied_at', '>=', $filters[self::FILTER_APPLICATION_DATE]['from']);
            })
            // Filter by application end date
            ->when(isset($filters[self::FILTER_APPLICATION_DATE]['to']), function($query) use ($filters) {
                $query->where('applied_at', '<=', $filters[self::FILTER_APPLICATION_DATE]['to']);
            })
            ->when($hasMissionFilters, function($query) use ($filters) {
                $query->whereHas('mission', function($query) use ($filters) {
                    // Filter by mission theme
                    $query->when(isset($filters[self::FILTER_MISSION_THEMES]), function($query) use ($filters) {
                        $query->whereIn('theme_id', $filters[self::FILTER_MISSION_THEMES]);
                    });
                    // Filter by mission type
                    $query->when(isset($filters[self::FILTER_MISSION_TYPES]), function($query) use ($filters) {
                        $query->whereIn('mission_type', $filters[self::FILTER_MISSION_TYPES]);
                    });
                });
            })
            // Filter by applicant skills
            ->when(isset($filters[self::FILTER_APPLICANT_SKILLS]), function($query) use ($filters) {
                $query->whereHas('user.skills', function($query) use ($filters) {
                    $query->whereIn('user_skill_id', $filters[self::FILTER_APPLICANT_SKILLS]);
                });
            })
            // Filter by mission skill
            ->when(isset($filters[self::FILTER_MISSION_SKILLS]), function($query) use ($filters) {
                $query->whereHas('mission.missionSkill', function($query) use ($filters) {
                    $query->whereIn('mission_skill_id', $filters[self::FILTER_MISSION_SKILLS]);
                });
            })
            // Search
            ->when(!empty($search), function($query) use ($search) {
                $query->where(function ($query)  use ($search) {
                    $query->whereHas('user', function($query) use ($search) {
                        $query
                            ->where('first_name', 'like', "%${search}%")
                            ->orWhere('last_name', 'like', "%${search}%")
                            ->orWhere('email', 'like', "%${search}%");
                    })
                    ->orwhereHas('mission.missionLanguage', function($query) use ($search) {
                        $query
                            ->where('title', 'like', "%${search}%");
                    })
                    ->orwhereHas('mission.city', function($query) use ($search) {
                        $query
                            ->where('name', 'like', "%${search}%");
                    })
                    ->orwhereHas('mission.country', function($query) use ($search) {
                        $query
                            ->where('name', 'like', "%${search}%");
                    });
                });
            })
            // Ordering
            ->when($order, function ($query) use ($order) {
                Log::debug(json_encode($order));
                $query->orderBy($order['orderBy'], $order['orderDir']);
            })
            // Pagination
            ->when($limit, function ($query) use ($limit) {
                $query->offset($limit['offset']);
                $query->limit($limit['limit']);
            })
            ->paginate($limit['limit'], '*', 'page', ceil($limit['offset'] / $limit['limit']));

        return $applications;
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
