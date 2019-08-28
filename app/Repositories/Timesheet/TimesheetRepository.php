<?php
namespace App\Repositories\Timesheet;

use App\Repositories\Timesheet\TimesheetInterface;
use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\Mission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use DB;
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

        // Add timesheet documents
        if (isset($request->documents) && count($request->documents) > 0) {
            if (!empty($request->documents)) {
                foreach ($request->documents as $value) {
                    $filePath = $this->s3helper->uploadFileOnS3Bucket($value['document_path'], $tenantName);
                    $timesheetDocument = array('timesheet_id' => $timesheet->timesheet_id,
                                            'document_name' => basename($filePath),
                                            'document_type' => pathinfo(basename($filePath), PATHINFO_EXTENSION),
                                            'document_path' => $filePath);
                    $this->timesheetDocument->create($timesheetDocument);
                    unset($timesheetDocument);
                }
            }
        }
        return $timesheet;
    }

    /**
     * get added action data count
     *
     * @param int $missionId
     * @return int|null
     */
    public function getAddedActions(int $missionId): ?int
    {
        return ($this->timesheet->where('mission_id', $missionId)->sum('action')) ?? 0;
    }

    /**
     * Get timesheet entries
     *
     * @param Request $request
     * @return array
     */
    public function getAllTimesheetEntries(Request $request)
    {
        // $timesheetQuery =
        $timesheetQuery = Mission::select(
            'mission.mission_id',
            'mission.theme_id',
            'mission.city_id',
            'mission.country_id',
            'mission.start_date',
            'mission.end_date',
            'mission.mission_type',
            'mission.publication_status'
        )
        ->where('publication_status', config("constants.publication_status")["APPROVED"])
        ->with(['timesheet', 'missionLanguage', 'goalMission', 'timeMission'])
        ->with(['missionApplication' => function ($query) use ($request) {
            $query->where('user_id', $request->auth->user_id)
            ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        }])
        ->get();
        return $timesheetQuery;
    }
}
