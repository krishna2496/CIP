<?php

namespace App\Repositories\Timesheet;

use DB;
use App\Models\Mission;
use App\Models\Timesheet;
use Carbon\Carbon;
use App\Models\TimesheetDocument;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Helpers\S3Helper;
use App\Helpers\LanguageHelper;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Timesheet\TimesheetInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TimesheetRepository implements TimesheetInterface
{
    /**
     * @var App\Models\Timesheet
     */
    private $timesheet;

    /**
     * @var App\Models\Mission
     */
    private $mission;

    /**
     * @var App\Models\TimesheetDocument
     */
    private $timesheetDocument;

    /**
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new Timesheet repository instance.
     *
     * @param  App\Models\Timesheet $timesheet
     * @param  App\Models\Mission $mission
     * @param  App\Models\TimesheetDocument $timesheetDocument
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @param  App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        Timesheet $timesheet,
        Mission $mission,
        TimesheetDocument $timesheetDocument,
        Helpers $helpers,
        LanguageHelper $languageHelper,
        S3Helper $s3helper
    ) {
        $this->timesheet = $timesheet;
        $this->mission = $mission;
        $this->timesheetDocument = $timesheetDocument;
        $this->helpers = $helpers;
        $this->languageHelper = $languageHelper;
        $this->s3helper = $s3helper;
    }
    
    /**
     * Store/Update timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Timesheet
     */
    public function storeOrUpdateTimesheet(Request $request): Timesheet
    {
        $data = $request->except('date_volunteered');

        $dateVolunteered = Carbon::createFromFormat('m-d-Y', $request->date_volunteered)
        ->setTimezone(config('constants.TIMEZONE'));
        
        $timesheet = $this->timesheet->updateOrCreate(['user_id' => $request->auth->user_id,
        'mission_id' => $request->mission_id,
        'date_volunteered' => $dateVolunteered->format('Y-m-d')
        ], $data);
       
        if ($request->hasFile('documents')) {
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            $files = $request->file('documents');
            foreach ($files as $file) {
                $filePath = $this->s3helper
                ->uploadDocumentOnS3Bucket(
                    $file,
                    $tenantName,
                    $request->auth->user_id,
                    $timesheet->timesheet_id
                );
                $timesheetDocument = array('timesheet_id' => $timesheet->timesheet_id,
                                        'document_name' => basename($filePath),
                                        'document_type' => pathinfo(basename($filePath), PATHINFO_EXTENSION),
                                        'document_path' => $filePath);
                $this->timesheetDocument->create($timesheetDocument);
                unset($timesheetDocument);
            }
        }
        return $timesheet;
    }

    /**
     * Get timesheet entries
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function getAllTimesheetEntries(Request $request): array
    {
        $timeMissionEntries = $this->getTimesheetEntries($request, config('constants.mission_type.TIME'));
        foreach ($timeMissionEntries as $value) {
            if ($value->missionLanguage) {
                $value->setAttribute('title', $value->missionLanguage[0]->title);
                unset($value->missionLanguage);
            }
            $value->setAppends([]);
        }

        $goalMissionEntries = $this->getTimesheetEntries($request, config('constants.mission_type.GOAL'));
        foreach ($goalMissionEntries as $value) {
            if ($value->missionLanguage) {
                $value->setAttribute('title', $value->missionLanguage[0]->title);
                unset($value->missionLanguage);
            }
            $value->setAppends([]);
        }

        $timesheetEntries[config('constants.mission_type.TIME')] = $timeMissionEntries;
        $timesheetEntries[config('constants.mission_type.GOAL')] = $goalMissionEntries;
        return $timesheetEntries;
    }
    
    /**
     * Fetch timesheet details
     *
     * @param int $timesheetId
     * @return null|Timesheet
     */
    public function find(int $timesheetId): ?Timesheet
    {
        return $this->timesheet->findOrFail($timesheetId);
    }

    /**
     * Fetch timesheet details
     *
     * @param int $timesheetId
     * @param int $userId
     * @return Timesheet
     */
    public function getTimesheetData(int $timesheetId, int $userId): Timesheet
    {
        return $this->timesheet->findTimesheet($timesheetId, $userId);
    }
    
    /**
    * Remove the timesheet document.
    *
    * @param  int  $id
    * @param  int  $timesheetId
    * @return bool
    */
    public function delete(int $id, int $timesheetId): bool
    {
        return $this->timesheetDocument->deleteTimesheetDocument($id, $timesheetId);
    }

    /**
     * Display a listing of specified resources.
     *
     * @param int $userId
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getUserTimesheet(int $userId, Request $request): Collection
    {
        $languageId = $this->languageHelper->getLanguageId($request);

        return $this->mission->select('mission.mission_id')
        ->where(['publication_status' => config("constants.publication_status")["APPROVED"]])
        ->whereHas('missionApplication', function ($query) use ($userId) {
            $query->where('user_id', $userId)
            ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        })
        ->with(['missionLanguage' => function ($query) use ($languageId) {
            $query->select('mission_language_id', 'mission_id', 'title')
            ->where('language_id', $languageId);
        }])
        ->with(['timesheet' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
            $query->with('timesheetStatus');
        }])
        ->get();
    }

    /**
     * Update timesheet field value, based on timesheet_id condition
     *
     * @param int $statusId
     * @param int $timesheetId
     * @return bool
     */
    public function updateTimesheetStatus(int $statusId, int $timesheetId): bool
    {
        return $this->timesheet->where('timesheet_id', $timesheetId)
        ->update(['status_id' => $statusId]);
    }

    /** Update timesheet status on submit
    *
    * @param \Illuminate\Http\Request $request
    * @param int $userId
    * @return bool
    */
    public function submitTimesheet(Request $request, int $userId): bool
    {
        $status = false;
        if ($request->timesheet_entries) {
            foreach ($request->timesheet_entries as $data) {
                $timesheetData = $this->timesheet->with('timesheetStatus')->where(['user_id' => $userId,
                'timesheet_id' => $data['timesheet_id']])
                ->firstOrFail();
                $timesheetDetails = $timesheetData->toArray();
                if ($timesheetDetails["timesheet_status"]["status"] == config('constants.timesheet_status.PENDING')) {
                    $timesheetData->status_id = config('constants.timesheet_status_id.SUBMIT_FOR_APPROVAL');
                    $status = $timesheetData->update();
                }
            }
        }
        return $status;
    }

    /**
     * Get time request details.
     *
     *
     * @param \Illuminate\Http\Request $request
     * @param array $statusArray
     * @param bool $withPagination
     * @return Object
     */
    public function timeRequestList(Request $request, array $statusArray, bool $withPagination = true) : Object
    {
        $languageId = $this->languageHelper->getLanguageId($request);
        
        $timeRequests = $this->mission->query()
        ->select('mission.mission_id', 'mission.organisation_name');
        $timeRequests->where(['publication_status' => config("constants.publication_status")["APPROVED"],
        'mission_type'=> config('constants.mission_type.TIME')])
        ->with(['missionLanguage' => function ($query) use ($languageId) {
            $query->select('mission_language_id', 'mission_id', 'title')
            ->where('language_id', $languageId);
        }])
        ->whereHas('timesheet', function ($query) use ($request, $statusArray) {
            $query->whereIn('status_id', $statusArray);
            $query->where('user_id', $request->auth->user_id);
        })
        ->withCount([
        'timesheet AS total_hours' => function ($query) use ($request, $statusArray) {
            $query->select(DB::raw("sum(((hour(time) * 60) + minute(time))) as 'total_minutes'"));
            $query->where('user_id', $request->auth->user_id);
            $query->whereIn('status_id', $statusArray);
        }]);
        if ($withPagination) {
            $timeRequestsList = $timeRequests->paginate($request->perPage);
        } else {
            $timeRequestsList = $timeRequests->get();
        }

        foreach ($timeRequestsList as $value) {
            if ($value->missionLanguage) {
                $value->setAttribute('title', $value->missionLanguage[0]->title);
                unset($value->missionLanguage);
            }
            $value->time = $this->helpers->convertInReportTimeFormat($value->total_hours);
            $value->hours = $this->helpers->convertInReportHoursFormat($value->total_hours);
            
            unset($value->total_hours);
            $value->setAppends([]);
        }
        return $timeRequestsList;
    }

    /**
      * Fetch goal time details.
     *
     * @param Illuminate\Http\Request $request
     * @param array $statusArray
     * @param bool $withPagination
     * @return Object
     */
    public function goalRequestList(Request $request, array $statusArray, bool $withPagination = true): Object
    {
        $languageId = $this->languageHelper->getLanguageId($request);
       
        $goalRequests = $this->mission->query()
        ->select('mission.mission_id', 'mission.organisation_name');
        $goalRequests->where(['publication_status' => config("constants.publication_status")["APPROVED"],
        'mission_type'=> config('constants.mission_type.GOAL')])
        ->with(['missionLanguage' => function ($query) use ($languageId) {
            $query->select('mission_language_id', 'mission_id', 'title')
            ->where('language_id', $languageId);
        }])
        ->whereHas('timesheet', function ($query) use ($request, $statusArray) {
            $query->where('user_id', $request->auth->user_id);
            $query->whereIn('status_id', $statusArray);
        })
        ->withCount([
        'timesheet AS action' => function ($query) use ($request, $statusArray) {
            $query->select(DB::raw("SUM(action) as action"));
            $query->where('user_id', $request->auth->user_id);
            $query->whereIn('status_id', $statusArray);
        }]);
        if ($withPagination) {
            $goalRequestList = $goalRequests->paginate($request->perPage);
        } else {
            $goalRequestList = $goalRequests->get();
        }
        foreach ($goalRequestList as $value) {
            if ($value->missionLanguage) {
                $value->setAttribute('title', $value->missionLanguage[0]->title);
                unset($value->missionLanguage);
            }
            $value->setAppends([]);
        }
        return $goalRequestList;
    }

    /**
     * Fetch timesheet details by mission and date
     *
     * @param int $missionId
     * @param string $date
     * @return null|Illuminate\Support\Collection
     */
    public function getTimesheetDetailByDate(int $missionId, string $date): ? Collection
    {
        return ($this->timesheet->where(['mission_id' => $missionId, 'date_volunteered' => $date])
        ->whereIn('status_id', array(config('constants.timesheet_status_id.APPROVED'),
        config('constants.timesheet_status_id.AUTOMATICALLY_APPROVED'))))->get();
    }

    /**
     * Get timesheet entries
     *
     * @param Illuminate\Http\Request $request
     * @param string $missionType
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getTimesheetEntries(Request $request, string $missionType): Collection
    {
        $languageId = $this->languageHelper->getLanguageId($request);
        $userId = $request->auth->user_id;

        $timesheet = $this->mission->select('mission.mission_id', 'mission.start_date', 'mission.end_date')
        ->where(['publication_status' => config("constants.publication_status")["APPROVED"],
        'mission_type'=> $missionType])
        ->whereHas('missionApplication', function ($query) use ($userId) {
            $query->where('user_id', $userId)
            ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        })
        ->with(['missionLanguage' => function ($query) use ($languageId) {
            $query->select('mission_language_id', 'mission_id', 'title')
            ->where('language_id', $languageId);
        }])
        ->with(['timesheet' => function ($query) use ($missionType, $userId) {
            $type = ($missionType == config('constants.mission_type.TIME')) ? 'time' : 'action';
            $query->select(
                'timesheet_id',
                'mission_id',
                'date_volunteered',
                'day_volunteered',
                'notes',
                'status_id',
                $type
            )
            ->where('user_id', $userId)
            ->with('timesheetStatus');
        }]);
        return $timesheet->get();
    }

    /**
     * Fetch timesheet details
     *
     * @param int $missionId
     * @param int $userId
     * @param string $date
     * @return null|Illuminate\Support\Collection
     */
    public function getTimesheetDetails(int $missionId, int $userId, string $date): ?Collection
    {
        $date = Carbon::createFromFormat('m-d-Y', $date)
        ->setTimezone(config('constants.TIMEZONE'));
        $date = $date->format('Y-m-d');
        return $this->timesheet->with('timesheetDocument', 'timesheetStatus')->where(['mission_id' => $missionId,
        'user_id' => $userId, 'date_volunteered' => $date])->get();
    }

    /**
     * get added action data count
     *
     * @param int $missionId
     * @return int
     */
    public function getAddedActions(int $missionId): int
    {
        return ($this->timesheet->where('mission_id', $missionId)
        ->whereIn('status_id', array(config('constants.timesheet_status_id.APPROVED'),
        config('constants.timesheet_status_id.AUTOMATICALLY_APPROVED')))
        ->sum('action')) ?? 0;
    }
}
