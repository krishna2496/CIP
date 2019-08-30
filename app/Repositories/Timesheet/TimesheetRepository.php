<?php
namespace App\Repositories\Timesheet;

use App\Repositories\Timesheet\TimesheetInterface;
use Illuminate\Http\Request;
use App\Models\Timesheet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\TimesheetDocument;
use App\Helpers\S3Helper;
use App\Helpers\Helpers;

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
     * Create a new Timezone repository instance.
     *
     * @param  App\Models\Timesheet $timesheet
     * @param  App\Models\TimesheetDocument $timesheetDocument
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        Timesheet $timesheet,
        TimesheetDocument $timesheetDocument,
        Helpers $helpers,
        S3Helper $s3helper
    ) {
        $this->timesheet = $timesheet;
        $this->timesheetDocument = $timesheetDocument;
        $this->helpers = $helpers;
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
    * @return bool
    */
    public function delete(int $id): bool
    {
        return $this->timesheetDocument->deleteTimesheetDocument($id);
    }
}
