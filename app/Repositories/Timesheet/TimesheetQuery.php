<?php

namespace App\Repositories\Timesheet;

use App\Models\Timesheet;
use App\Repositories\Core\QueryableInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TimesheetQuery implements QueryableInterface
{
    const FILTER_MISSION_THEMES = 'missionThemes';
    const FILTER_APPLICATION_DATE = 'applicationDate';
    const FILTER_MISSION_STATUSES = 'customMissionStatus';
    const FILTER_APPROVAL_STATUS = 'timesheetStatus';
    const FILTER_MISSION_COUNTRIES = 'missionCountries';
    const FILTER_MISSION_CITIES = 'missionCities';
    const FILTER_TIMESHEET_IDS = 'timesheetIds';
    const FILTER_TYPE = 'type';

    const ALLOWED_SORTABLE_FIELDS = [
        'appliedDate' => 'date_volunteered',
        'applicant' => 'user.last_name',
        'reviewedHours' => 'time',
        'note' => 'notes',
        'appliedDay' => 'day_volunteered',
        'applicantEmailAddress' => 'user.email',
        'missionCountryCode' => 'country_language.name',
        'approvalStatus' => 'status',
        'missionCityId' => 'city_language.name',
        'appliedTo' => 'mission_language.title',
        'reviewedObjective' => 'action',
        'notes' => 'notes',
        'applicantFirstName' => 'user.first_name',
        'applicantLastName' => 'user.last_name',
    ];

    const ALLOWED_SORTING_DIR = ['ASC', 'DESC'];

    /**
     * @var string
     */
    private $missionType;

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
        $tenantLanguages = $parameters['tenantLanguages'];

        $hasMissionFilters = isset($filters[self::FILTER_MISSION_THEMES])
            || isset($filters[self::FILTER_MISSION_COUNTRIES])
            || isset($filters[self::FILTER_MISSION_CITIES])
            || isset($filters[self::FILTER_MISSION_STATUSES]);

        $languageId = $this->getFilteringLanguage($filters, $tenantLanguages);
        $query = Timesheet::query();
        $timesheets = $query
            ->select([
                'timesheet.*',
                'mission_language.title',
                'city_language.name',
                'country_language.name',
                $filters[self::FILTER_TYPE] !== 'goal' ? 'timesheet.time' :'goal_mission.goal_objective'
            ])
            ->join('user', 'user.user_id', '=', 'timesheet.user_id')
            ->join('mission', 'mission.mission_id', '=', 'timesheet.mission_id')
            ->when($filters[self::FILTER_TYPE] === 'goal', function ($query) use ($filters) {
                $query->join('goal_mission', 'goal_mission.mission_id', '=', 'timesheet.mission_id');
            })
            ->join('mission_language', function ($join) use ($languageId) {
                $join->on('mission_language.mission_id', '=', 'timesheet.mission_id')
                    ->where('mission_language.language_id', '=', $languageId);
            })
            ->join('city_language', function($join) use ($languageId) {
                $join->on('city_language.city_id', '=', 'mission.city_id')
                    ->where('city_language.language_id', '=', $languageId);
            })
            ->join('country_language', function($join) use ($languageId) {
                $join->on('country_language.country_id', '=', 'mission.country_id')
                    ->where('country_language.language_id', '=', $languageId);
            })
            ->whereNotIn('timesheet.status', ['pending'])
            ->whereHas('mission', function ($query) {
                $query->whereIn(
                    'publication_status', [config("constants.publication_status")["APPROVED"], config("constants.publication_status")["PUBLISHED_FOR_APPLYING"]]
                );
            })
            ->whereHas('mission.missionApplication', function ($query) {
                $query->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
            })
            ->with([
                'user:user_id,first_name,last_name,avatar,email',
                'user.skills.skill:skill_id',
                'mission.missionLanguage' => function ($query) use ($languageId) {
                    $query->select('mission_language_id', 'mission_id', 'language_id', 'title', 'objective')
                        ->where('language_id', $languageId);
                },
                'mission.goalMission',
                'mission.missionSkill',
                'mission.country.languages' => function ($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                },
                'mission.city.languages' => function ($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                },
                'timesheetDocument',
            ])
            // Filter by application start date
            ->when(isset($filters[self::FILTER_APPLICATION_DATE]['from']), function ($query) use ($filters) {
                $query->where('date_volunteered', '>=', $filters[self::FILTER_APPLICATION_DATE]['from']);
            })
            // Filter by timesheet ids
            ->when(isset($filters[self::FILTER_TIMESHEET_IDS]), function ($query) use ($filters) {
                $query->whereIn('timesheet_id', $filters[self::FILTER_TIMESHEET_IDS]);
            })
            // Filter by application end date
            ->when(isset($filters[self::FILTER_APPLICATION_DATE]['to']), function ($query) use ($filters) {
                $query->where('date_volunteered', '<=', $filters[self::FILTER_APPLICATION_DATE]['to']);
            })
            // Filter by timesheet status
            ->when(isset($filters[self::FILTER_APPROVAL_STATUS]), function ($query) use ($filters) {
                $query->whereIn('timesheet.status',
                    collect($filters[self::FILTER_APPROVAL_STATUS])->map(function ($val) {
                        return strtoupper($val);
                    })
                );
            })
            ->when($hasMissionFilters, function ($query) use ($filters) {
                $query->whereHas('mission', function ($query) use ($filters) {
                    // Filter by mission theme
                    $query->when(isset($filters[self::FILTER_MISSION_THEMES]), function ($query) use ($filters) {
                        $query->whereIn('theme_id', $filters[self::FILTER_MISSION_THEMES]);
                    });
                    // Filter by mission country
                    $query->when(isset($filters[self::FILTER_MISSION_COUNTRIES]), function ($query) use ($filters) {
                        $query->whereIn('country_id', $filters[self::FILTER_MISSION_COUNTRIES]);
                    });
                    // Filter by mission city
                    $query->when(isset($filters[self::FILTER_MISSION_CITIES]), function ($query) use ($filters) {
                        $query->whereIn('city_id', $filters[self::FILTER_MISSION_CITIES]);
                    });
                    // Filter by mission Status
                    $query->when(isset($filters[self::FILTER_MISSION_STATUSES]), function ($query) use ($filters) {
                        collect($filters[self::FILTER_MISSION_STATUSES])->map(function ($val) use ($query) {
                            if ($val === 'active') {
                                return $query->whereIn('publication_status', [
                                        config("constants.publication_status")["PUBLISHED_FOR_APPLYING"],
                                        config("constants.publication_status")["APPROVED"]
                                ]);
                            } else {
                                return $query->whereIn('publication_status', [
                                    config("constants.publication_status")["UNPUBLISHED"],
                                    config("constants.publication_status")["DRAFT"]
                                ]);
                            }
                        });
                    });
                });
            })
             ->whereHas('mission', function ($query) use ($filters) {
                 $query->when(isset($filters[self::FILTER_TYPE]), function ($query) use ($filters) {
                     $this->missionType = $filters[self::FILTER_TYPE] === 'time' ? config('constants.mission_type.TIME') : config('constants.mission_type.GOAL');
                     $query->where('mission_type', '=', "$this->missionType");
                 });
             })
            // Search
            ->when(!empty($search), function ($query) use ($search, $filters, $languageId) {
                $searchCallback = function ($query) use ($search, $languageId) {

                    $query
                        ->where('timesheet.status', 'like', "%${search}%")
                        ->orWhere('timesheet.time', 'like', "%${search}%")
                        ->orWhere('timesheet.action', 'like', "%${search}%")
                        ->orWhere('timesheet.notes', 'like', "%${search}%")
                        ->orWhere('timesheet.day_volunteered', 'like', "%${search}%")
                        ->orWhere('timesheet.date_volunteered', 'like', "%${search}%")
                        ->orwhereHas('timesheetDocument', function ($query) use ($search) {
                            $query
                                ->where('document_name', 'like', "%${search}%");
                        })
                        ->orwhereHas('user', function ($query) use ($search) {
                            $query
                                ->where('first_name', 'like', "%${search}%")
                                ->orWhere('last_name', 'like', "%${search}%")
                                ->orWhere('email', 'like', "%${search}%");
                        })
                        ->orwhereHas('mission.missionLanguage', function ($query) use ($search, $languageId) {
                            $query
                                ->where([
                                    ['title', 'like', "%${search}%"],
                                    ['language_id', '=', $languageId]
                                ])
                                ->orWhere('objective', 'like', "%${search}%");
                        })
                        ->orwhereHas('mission.goalMission', function ($query) use ($search) {
                            $query
                                ->where('goal_objective', 'like', "%${search}%");
                        })
                        ->orwhereHas('mission.city.languages', function ($query) use ($search, $languageId) {
                            $query
                                ->where([
                                    ['name', 'like', "%${search}%"],
                                    ['language_id', '=', $languageId],
                                ]);
                        })
                        ->orwhereHas('mission.country.languages', function ($query) use ($search, $languageId) {
                            $query
                                ->where([
                                    ['name', 'like', "%${search}%"],
                                    ['language_id', '=', $languageId],
                                ]);
                        });
                };

                if (isset($filters[self::FILTER_TIMESHEET_IDS])) {
                    $query->orWhere($searchCallback);
                } else {
                    $query->where($searchCallback);
                }

            })
            ->whereHas('mission', function ($query) use ($filters) {
                $query->when(isset($filters[self::FILTER_TYPE]), function ($query) {
                    $query->where('mission_type', '=', "$this->missionType");
                });
            })
            // Ordering
            ->when($order, function ($query) use ($order) {
                $query->orderBy($order['orderBy'], $order['orderDir']);
            })
            // Pagination
            ->paginate($limit['limit'], '*', 'page', 1 + ceil($limit['offset'] / $limit['limit']));

        return $timesheets;
    }


    /**
     * @param array $filters
     * @param Collection $tenantLanguages
     * @return int
     */
    private function getFilteringLanguage(array $filters, Collection $tenantLanguages): int
    {
        $hasLanguageFilter = array_key_exists('language', $filters);
        $defaultLanguageId = $tenantLanguages->filter(function ($language) use ($filters) {
            return $language->default === '1';
        })->first()->language_id;

        if (!$hasLanguageFilter) {
            return $defaultLanguageId;
        }

        $language = $tenantLanguages->filter(function ($language) use ($filters) {
            return $language->code === $filters['language'];
        })->first();

        if (is_null($language)) {
            return $defaultLanguageId;
        }

        return $language->language_id;
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
                $order['orderBy'] = self::ALLOWED_SORTABLE_FIELDS['appliedDate'];
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
