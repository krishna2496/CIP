<?php
    
namespace Tests\Unit\Repositories\Timesheet;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use App\Models\Mission;
use App\Models\MissionLanguage;
use App\Models\Timesheet;
use App\Models\TimesheetDocument;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Repositories\Timesheet\TimesheetRepository;
use App\Repositories\User\UserRepository;
use Bschmitt\Amqp\Amqp;
use TestCase;

class TimeSheetRepositoryTest extends TestCase
{
    /**
    * @testdox Get sum of all users approved time
    */
    public function testGetSumOfUsersTotalMinutes() {

        $timeSheetModel = new Timesheet;
        $instance = $this->getIntance(['timesheet' => $timeSheetModel]);
        $originalTotalMinutes = $instance->getSumOfUsersTotalMinutes();

        $connection = 'tenant';
        $missionFactory = factory(Mission::class)->make();
        $missionFactory->setConnection($connection);
        $missionFactory->save();
        $missionId = $missionFactory->mission_id;
        $timeSheets = [
            [
                'user_id' => 1,
                'mission_id' => $missionId,
                'time' => '01:00:00',
                'action' => null,
                'date_volunteered' => '2020-06-06',
                'day_volunteered' => 'WORKDAY',
                'notes' => 'Some Sample Notes',
                'status' => 'APPROVED'
            ],
            [
                'user_id' => 1,
                'mission_id' => $missionId,
                'time' => '02:00:00',
                'action' => null,
                'date_volunteered' => '2020-06-06',
                'day_volunteered' => 'WORKDAY',
                'notes' => 'Some Sample Notes',
                'status' => 'APPROVED'
            ]
        ];

        foreach ($timeSheets as $timeSheet) {
            $timeSheetFactory = factory(Timesheet::class)->make($timeSheet);
            $timeSheetFactory->setConnection($connection);
            $timeSheetFactory->save();
        }

        $newTotalMinutes = $instance->getSumOfUsersTotalMinutes();
        // 180 total of new minutes record added
        $expectedTotalMinutes = $originalTotalMinutes + 180;

        $this->assertEquals($newTotalMinutes, $expectedTotalMinutes);
    }

    private function getIntance($defaults = []) {
        $timesheet = array_key_exists('timesheet', $defaults) ?
            $defaults['timesheet'] : $this->getTimeSheetMock();

        return new TimesheetRepository(
            $timesheet,
            $this->getMissionMock(),
            $this->getMissionLanguageMock(),
            $this->getTimesheetDocumentMock(),
            $this->getHelpersMock(),
            $this->getLanguageHelperMock(),
            $this->getS3HelperMock(),
            $this->getTenantOptionRepositoryMock(),
            $this->getUserRepositoryMock(),
            $this->getAmqpMock()
        );
    }

    private function getTimeSheetMock() {
        return $this->getMockBuilder(Timesheet::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getMissionMock() {
        return $this->getMockBuilder(Mission::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getMissionLanguageMock() {
        return $this->getMockBuilder(MissionLanguage::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getTimesheetDocumentMock() {
        return $this->getMockBuilder(TimesheetDocument::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getHelpersMock() {
        return $this->getMockBuilder(Helpers::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getLanguageHelperMock() {
        return $this->getMockBuilder(LanguageHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getS3HelperMock() {
        return $this->getMockBuilder(S3Helper::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getTenantOptionRepositoryMock() {
        return $this->getMockBuilder(TenantOptionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getUserRepositoryMock() {
        return $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getAmqpMock() {
        return $this->getMockBuilder(Amqp::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

}