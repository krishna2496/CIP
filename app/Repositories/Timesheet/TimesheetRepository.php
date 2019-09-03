<?php

namespace App\Repositories\Timesheet;

use DB;
use App\Models\Mission;
use App\Models\Timezone;
use App\Helpers\Helpers;
use App\Helpers\S3Helper;
use App\Helpers\LanguageHelper;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use App\Models\TimesheetDocument;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Timesheet\TimesheetInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TimesheetRepository implements TimesheetInterface
{
    /**
     * @var App\Models\Timesheet
     */
    public $timesheet;

    /**
     * @var App\Models\TimesheetDocument
     */
    public $timesheetDocument;

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
     * @param  App\Models\TimesheetDocument $timesheetDocument
     * @param  App\Helpers\Helpers $helpers
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param  App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        Timesheet $timesheet,
        TimesheetDocument $timesheetDocument,
        Helpers $helpers,
        LanguageHelper $languageHelper,
        S3Helper $s3helper
    ) {
        $this->timesheet = $timesheet;
        $this->timesheetDocument = $timesheetDocument;
        $this->helpers = $helpers;
        $this->languageHelper = $languageHelper;
        $this->s3helper = $s3helper;
    }
    
    /**
     * Store timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Timesheet
     */
    public function storeTimesheet(Request $request): Timesheet
    {
        $data = $request->toArray();
        $timesheet = $this->timesheet->create($data);
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
     * get added action data count
     *
     * @param int $missionId
     * @return int
     */
    public function getAddedActions(int $missionId): int
    {
        return ($this->timesheet->where('mission_id', $missionId)->sum('action')) ?? 0;
    }

    /**
     * Get timesheet entries
     *
     * @param Request $request
     * @param string $missionType
     * @return array
     */
    public function getAllTimesheetEntries(Request $request, string $missionType)
    {
        $languages = $this->languageHelper->getLanguages($request);
        $language = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        $language = $languages->where('code', $language)->first();
        $languageId = $language->language_id;
        
        $timesheetQuery = Mission::select('mission.mission_id')
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
        ->with(['timesheet' => function ($query) use ($missionType) {
            $type = ($missionType == config('constants.mission_type.TIME')) ? 'time' : 'action';
            $query->select('mission_id', 'date_volunteered', 'day_volunteered', 'notes', 'status_id', $type)
            ->where('user_id', $request->auth->user_id)->with('timesheetStatus');
        }]);
        return $timesheetQuery->get();
    }
    
    /**
     * Update timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @param int $timesheetId
     * @return bool
     */
    public function updateTimesheet(Request $request, int $timesheetId):  bool
    {
        $timesheetData =$this->timesheet->where(['timesheet_id' => $timesheetId,
        'user_id' => $request->auth->user_id])->firstOrFail();

        $data = $request->toArray();
        $timesheet = $timesheetData->update($data);
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        $files = $request->file('documents');
       
        if ($request->hasFile('documents')) {
            foreach ($files as $file) {
                $filePath = $this->s3helper->uploadDocumentOnS3Bucket(
                    $file,
                    $tenantName,
                    $request->auth->user_id,
                    $timesheetId
                );
                $timesheetDocument = array('timesheet_id' => $timesheetId,
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
     * Update timesheet on submitted
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
}
