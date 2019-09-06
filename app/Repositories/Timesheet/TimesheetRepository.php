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

        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        $files = $request->file('documents');
       
        if ($request->hasFile('documents')) {
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
     * @param Request $request
     * @param string $missionType
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllTimesheetEntries(Request $request, string $missionType): Collection
    {
        $languages = $this->languageHelper->getLanguages($request);
        $language = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        $language = $languages->where('code', $language)->first();
        $languageId = $language->language_id;
        
        $timesheet = $this->mission->select('mission.mission_id')
        ->where(['publication_status' => config("constants.publication_status")["APPROVED"],
        'mission_type'=> $missionType])
        ->whereHas('missionApplication', function ($query) use ($request) {
            $query->where('user_id', $request->auth->user_id)
            ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        })
        ->with(['missionLanguage' => function ($query) use ($languageId) {
            $query->select('mission_language_id', 'mission_id', 'title')
            ->where('language_id', $languageId);
        }])
        ->with(['timesheet' => function ($query) use ($missionType, $request) {
            $type = ($missionType == config('constants.mission_type.TIME')) ? 'time' : 'action';
            $query->select('mission_id', 'date_volunteered', 'day_volunteered', 'notes', 'status_id', $type)
            ->where('user_id', $request->auth->user_id)
            ->with('timesheetStatus');
        }]);
        return $timesheet->get();
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
        $languages = $this->languageHelper->getLanguages($request);
        $language = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        $language = $languages->where('code', $language)->first();
        $languageId = $language->language_id;

        return Mission::select('mission.mission_id', 'mission.city_id')
        ->where(['publication_status' => config("constants.publication_status")["APPROVED"]])
        ->whereHas('missionApplication', function ($query) use ($userId) {
            $query->where('user_id', $userId)
            ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        })
        ->with(['missionLanguage' => function ($query) use ($languageId) {
            $query->select('mission_language_id', 'mission_id', 'title')
            ->where('language_id', $languageId);
        }])
        ->with(['timesheet' => function ($query) {
            $query->with('timesheetStatus');
        }])
        ->get();
    }

    /**
     * Display a listing of specified resources.
     *
     * @param array $data
     * @param int $timesheetId
     * @return bool
     */
    public function updateTimesheetField(array $data, int $timesheetId): bool
    {
        return $this->timesheet->where('timesheet_id', $timesheetId)->update($data);
    }

    /** Update timesheet on submitted
    *
    * @param \Illuminate\Http\Request $request
    * @param int $userId
    * @return bool
    */
    public function updateSubmittedTimesheet(Request $request, int $userId): bool
    {
        $status = false;
        if ($request->timesheet_entries) {
            foreach ($request->timesheet_entries as $data) {
                $timesheetData = $this->timesheet->with('timesheetStatus')->where(['user_id' => $userId,
                'timesheet_id' => $data['timesheet_id']])
                ->firstOrFail();
                $timesheetDetails = $timesheetData->toArray();
                if ($timesheetDetails["timesheet_status"]["status"] == config('constants.timesheet_status.PENDING')) {
                    $timesheetData->status_id = "5";
                    $status = $timesheetData->save();
                }
            }
        }
        return $status;
    }

    /**
     * Fetch goal requests list
     *
     * @param Request $request
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function getGoalRequestList(Request $request): LengthAwarePaginator
    {
        $languages = $this->languageHelper->getLanguages($request);
        $language = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        $language = $languages->where('code', $language)->first();
        $languageId = $language->language_id;
        
        $goalRequestQuery = Mission::query()
        ->select('mission.mission_id', 'mission.city_id', 'mission.organisation_name');
        $goalRequestQuery->where(['publication_status' => config("constants.publication_status")["APPROVED"],
        'mission_type'=> config('constants.mission_type.GOAL')])
        ->with(['missionLanguage' => function ($query) use ($languageId) {
            $query->select('mission_language_id', 'mission_id', 'title')
            ->where('language_id', $languageId);
        }])
        ->whereHas('timesheet', function ($query) use ($request) {
            $query->where(['status_id' => 5, 'user_id' => $request->auth->user_id]);
        })
        ->withCount([
        'timesheet AS total_action' => function ($query) use ($request) {
            $query->select(DB::raw("SUM(action) as action"))
            ->where(['status_id' => 5, 'user_id' => $request->auth->user_id]);
        }]);
        return $goalRequestQuery->paginate($request->perPage);
    }

    /**
     * Fetch timesheet details by missionId and date
     *
     * @param int $missionId
     * @param string $date
     * @return null|Illuminate\Support\Collection
     */
    public function getTimesheetDetailByDate(int $missionId, string $date): ? Collection
    {
        return ($this->timesheet->where(['mission_id' => $missionId, 'date_volunteered' => $date])
        ->whereIn('status_id', array(2, 4)))->get();
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
        ->whereIn('status_id', array(2, 4))->sum('action')) ?? 0;
    }
}
