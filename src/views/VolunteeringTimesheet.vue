<template>
    <div class="dashboard-timesheet inner-pages">
        <header>
            <ThePrimaryHeader v-if="isShownComponent"></ThePrimaryHeader>
        </header>
        <main>
            <DashboardBreadcrumb />
            <div class="dashboard-tab-content" v-if="isComponentLoaded">
                <b-container v-if="isAllVisible">
                    <div class="heading-section">
                        <h1>
                            {{languageData.label.volunteering_timesheet}}
                        </h1>
                    </div>
                  
                    <div class="inner-content-wrap">
                        <div class="dashboard-table">
                            <div class="table-outer">
                                <div class="table-inner">
                                    <h3>{{languageData.label.volunteering_hours}}</h3>
                                    <VolunteeringTimesheetTableHeader 
                                    :currentWeek="timeSheetHourCurrentDate"
                                    @updateCall="changeVolunteeringHours" />
                                    <div class="table-wrapper-outer">
                                        <div
                                            v-bind:class="{ 'content-loader-wrap': true, 'loader-active': tableLoaderActive}">
                                            <div class="content-loader"></div>
                                        </div>
                                        <b-table-simple small responsive bordered
                                            class="timesheet-table timesheethours-table"
                                            >
                                            <b-thead>
                                                <b-tr>
                                                    <b-th class="mission-col">{{languageData.label.mission}}</b-th>
                                                    <b-th v-for="(key,item) in volunteeringHoursWeeks"
                                                        v-bind:key="key"
                                                        v-bind:class="{'currentdate-col' : highLightCurrentDate(key+1,'time')}">
                                                        {{key+1}}<span>{{getTimeSheetWeekName(item,'time')}}</span>
                                                    </b-th>
                                                    <b-th class="total-col">{{languageData.label.total}}</b-th>
                                                </b-tr>
                                            </b-thead>
                                            <b-tbody v-if="timeMissionData.length > 0">
                                                <b-tr v-for="(timeItem,key) in timeMissionData" v-bind:key="key">
                                                    <b-td class="mission-col">
                                                        <a target="_blank"
                                                            :href="`mission-detail/${timeItem.mission_id}`">{{timeItem.title}}</a>
                                                    </b-td>
                                                    <b-td :mission-id="timeItem.mission_id" :date="key+1"
                                                        v-on:click="getRelatedTimeData(key+1,timeItem,'time')"
                                                        v-bind:class="[getTimeSheetHourClass(key+1,timeItem,'time')]"
                                                        v-for="(key,item) in volunteeringHoursWeeks" v-if="item != null" v-bind:key="key">
                                                        {{getTime(key,timeItem.timesheet,'time')}}
                                                    </b-td>
                                                    <b-td class="total-col">
                                                        {{getRawHourTotal(timeItem.timesheet,'time')}}</b-td>
                                                </b-tr>
                                                <b-tr class="total-row">
                                                    <b-td class="mission-col">{{languageData.label.total}}:</b-td>
                                                    <b-td v-for="(key,item) in volunteeringHoursWeeks" v-bind:key="key">
                                                        {{getColumnHourTotal(key+1,'time')}}
                                                    </b-td>
                                                    <b-td>
                                                        {{getTotalHours('time')}}
                                                    </b-td>
                                                </b-tr>
                                            </b-tbody>
                                            <b-tbody v-else>
                                                <b-tr>
                                                    <b-td colspan="9" class="disabled">
                                                        {{languageData.label.volunteering_hours | firstLetterCapital}} {{languageData.label.not_found}}
                                                    </b-td>
                                                </b-tr>
                                            </b-tbody>

                                        </b-table-simple>
                                    </div>
                                </div>
                                <div class="btn-block">
                                    <b-button class="btn-bordersecondary ml-auto" v-bind:class="{
                                    disabled : enableSubmitTimeTimeSheet    
                                }" @click="submitVolunteerTimeSheet('time')">{{languageData.label.submit}}</b-button>
                                </div>
                            </div>
                            <ul class="meta-data-list">
                                <li class="approve-indication">{{languageData.label.approved}}</li>
                                <li class="decline-indication">{{languageData.label.declined}}</li>
                                <li class="approval-indication">
                                    {{languageData.label.submit_for_approval}}
                                </li>
                            </ul>
                            <!--    {{goalMissionData}} -->
                            <div class="table-outer timesheet-table-outer">
                                <div class="table-inner">
                                    <h3>{{languageData.label.volunteering_goals}}</h3>
                                    <VolunteeringTimesheetTableHeader 
                                    :currentWeek="timeSheetGoalCurrentDate"
                                    @updateCall="changeVolunteeringGoals" />
                                    <div class="table-wrapper-outer">
                                        <div
                                            v-bind:class="{ 'content-loader-wrap': true, 'loader-active': goalTableLoaderActive}">
                                            <div class="content-loader"></div>
                                        </div>
                                        <b-table-simple small responsive bordered
                                            class="timesheet-table timesheetgoals-table">
                                            <b-thead>
                                                <b-tr>
                                                    <b-th class="mission-col">{{languageData.label.mission}}</b-th>
                                                    <b-th
                                                        v-bind:class="{'currentdate-col' : highLightCurrentDate(key+1,'goal')}"
                                                        v-for="(key,item) in volunteeringGoalWeeks" v-bind:key="key">
                                                        {{key+1}}<span>{{getTimeSheetWeekName(item,'goal')}}</span>
                                                    </b-th>
                                                    <b-th class="total-col">{{languageData.label.total}}</b-th>
                                                </b-tr>
                                            </b-thead>
                                            <b-tbody v-if="goalMissionData.length > 0">
                                                <b-tr v-for="(timeItem,key) in goalMissionData" v-bind:key="key">
                                                    <b-td class="mission-col">
                                                        <a target="_blank"
                                                            :href="`mission-detail/${timeItem.mission_id}`">{{timeItem.title}}</a>
                                                    </b-td>
                                                    <b-td :mission-id="timeItem.mission_id" :date="key+1"
                                                        v-on:click="getRelatedTimeData(key+1,timeItem,'goal')"
                                                        v-bind:class="[getTimeSheetHourClass(key+1,timeItem,'goal')]"
                                                        v-for="(key,item) in volunteeringGoalWeeks"
                                                        v-bind:key="item"
                                                        >
                                                        {{getTime(key,timeItem.timesheet,'goal')}}
                                                    </b-td>
                                                    <b-td class="total-col">
                                                        {{getRawHourTotal(timeItem.timesheet,'goal')}}</b-td>
                                                </b-tr>
                                                <b-tr class="total-row">
                                                    <b-td class="mission-col">{{languageData.label.total}}:</b-td>
                                                    <b-td v-for="(key,item) in volunteeringGoalWeeks" v-bind:key="item">
                                                        {{getColumnHourTotal(key+1,'goal')}}
                                                    </b-td>
                                                    <b-td>
                                                        {{getTotalHours('goal')}}
                                                    </b-td>
                                                </b-tr>
                                            </b-tbody>
                                            <b-tbody v-else>
                                                <b-tr>
                                                    <b-td colspan="9" class="disabled">
                                                        {{languageData.label.volunteering_goals | firstLetterCapital}} {{languageData.label.not_found}}
                                                    </b-td>
                                                </b-tr>
                                            </b-tbody>
                                        </b-table-simple>
                                    </div>
                                </div>
                                <div class="btn-block">
                                    <b-button class="btn-bordersecondary ml-auto"
                                        @click="submitVolunteerTimeSheet('goal')" v-bind:class="{
                                        disabled : enableSubmitGoalTimeSheet    
                                    }">{{languageData.label.submit}}</b-button>
                                </div>
                            </div>
                            <ul class="meta-data-list">
                                <li class="approve-indication">{{languageData.label.approved}}</li>
                                <li class="decline-indication">{{languageData.label.declined}}</li>
                                <li class="approval-indication">
                                    {{languageData.label.submit_for_approval}}
                                </li>
                            </ul>
                            <VolunteeringRequest :headerField="timesheetRequestFields" requestType="time"
                                :items="timesheetRequestItems" :headerLable="timeRequestLabel"
                                :currentPage="hourRequestCurrentPage" :totalRow="hourRequestTotalRow"
                                @updateCall="getTimeRequest" exportUrl="app/timesheet/time-requests/export"
                                :perPage="hourRequestPerPage" :nextUrl="hourRequestNextUrl"
                                :fileName="languageData.export_timesheet_file_names.PENDING_TIME_MISSION_ENTRIES_XLSX"
                                :totalPages="timeMissionTotalPage" />

                            <VolunteeringRequest :headerField="goalRequestFields" requestType="goal"
                                :items="goalRequestItems" :headerLable="goalRequestLabel"
                                :currentPage="goalRequestCurrentPage" :totalRow="goalRequestTotalRow"
                                @updateCall="getGoalRequest" :perPage="goalRequestPerPage" :nextUrl="goalRequestNextUrl"
                                exportUrl="app/timesheet/goal-requests/export"
                                :fileName="languageData.export_timesheet_file_names.PENTIND_GOAL_MISSION_ENTRIES_XLSX"
                                :totalPages="goalMissionTotalPage" />
                        </div>
                        <AddVolunteeringHours 
                            ref="timeModal" 
                            :defaultWorkday="defaultWorkday"
                            :defaultHours="defaultHours" 
                            :defaultMinutes="defaultMinutes" :files="files"
                            :timeEntryDefaultData="currentTimeData" 
                            :disableDates="volunteerHourDisableDates"
                            @getTimeSheetData="getVolunteerHoursData" @resetModal="hideModal" :workDayList="workDayList"
                            @changeTimeSheetView="changeTimeSheetHourView"
                            @updateCall="updateDefaultValue" @changeDocument="changeTimeDocument" />
                        <AddVolunteeringAction 
                            ref="goalModal" 
                            :defaultWorkday="defaultWorkday"
                            :defaultHours="defaultHours" :defaultMinutes="defaultMinutes" :files="files"
                            :timeEntryDefaultData="currentTimeData" :disableDates="volunteerHourDisableDates"
                            @changeTimeSheetView="changeTimeSheetGoalView"
                            @getTimeSheetData="getVolunteerHoursData" @resetModal="hideModal" :workDayList="workDayList"
                            @updateCall="updateDefaultValue" @changeDocument="changeGoalDocument" />
                    </div>
                </b-container>
                <b-container v-else>
                    <div class="no-history-data">
                        <p>{{languageData.label.volunteering_timesheet_not_found}}</p>
                        <div class="btn-row">
                            <b-button :title="languageData.label.start_volunteering" class="btn-bordersecondary"
                                @click="$router.push({ name: 'home' })">{{languageData.label.start_volunteering}}
                            </b-button>
                        </div>
                    </div>
                </b-container>
            </div>
        </main>
        <footer>
            <TheSecondaryFooter v-if="isShownComponent"></TheSecondaryFooter>
        </footer>
    </div>
</template>

<script>
    import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
    import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
    import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
    import VolunteeringTimesheetTableHeader from "../components/VolunteeringTimesheetTableHeader"
    import AddVolunteeringHours from "../components/AddVolunteeringHours";
    import AddVolunteeringAction from "../components/AddVolunteeringAction";
    import VolunteeringRequest from "../components/VolunteeringRequest";
    import store from '../store';

    import {
        volunteerTimesheetHours,
        fetchTimeSheetDocuments,
        submitVolunteerHourTimeSheet,
        timeRequest,
        goalRequest
    } from '../services/service';
    import moment from 'moment'
    import {
        setTimeout
    } from 'timers';

    export default {
        components: {
            ThePrimaryHeader,
            TheSecondaryFooter,
            DashboardBreadcrumb,
            VolunteeringTimesheetTableHeader,
            AddVolunteeringAction,
            AddVolunteeringHours,
            VolunteeringRequest
        },
        name: "DashboardTimesheet",
        data() {
            return {
                files: [],
                tableLoaderActive: true,
                goalTableLoaderActive: true,
                languageData: [],
                VolunteeringRequest: [],
                timesheetRequestFields: [],
                timesheetRequestItems: [],
                hourRequestCurrentPage: 1,
                goalRequestCurrentPage: 1,
                hourRequestTotalRow: 0,
                goalRequestTotalRow: 0,
                goalRequestFields: [],
                goalRequestItems: [],
                timeMissionTotalPage: null,
                goalMissionTotalPage: null,
                defaultWorkday: "",
                workDayList: [
                    ["WORKDAY", "workday"],
                    ["WEEKEND", "weekend"],
                    ["HOLIDAY", "holiday"],
                ],
                rows: 25,
                perPage: 2,
                currentPage: 1,
                volunteeringHoursCurrentMonth: '',
                volunteeringHoursCurrentYear: '',
                volunteeringHoursWeeks: [],
                volunteeringHoursWeekName: [],
                volunteeringHoursMonthArray: [],
                volunteeringHoursYearArray: [],
                volunteeringGoalMonthArray: [],
                volunteeringGoalYearArray: [],
                volunteeringGoalCurrentMonth: '',
                volunteeringGoalCurrentYear: '',
                volunteeringGoalWeeks: [],
                volunteeringGoalWeekName: [],
                timeMissionData: [],
                goalMissionData: [],
                timeRequestLabel: "",
                goalRequestLabel: "",
                currentTimeData: {
                    missionId: '',
                    hours: '',
                    minutes: '',
                    dateVolunteered: '',
                    workDay: '',
                    notes: '',
                    day: '',
                    timeSheetId: '',
                    documents: [],
                    disabledPastDates: '',
                    disabledFutureDates: '',
                    missionName: '',
                    action: ''
                },
                timeEntryDefaultData: null,
                isTimeEntryModelShown: false,
                volunteerHourDisableDates: [],
                defaultHours: '',
                defaultMinutes: '',
                futureDates: new Date(),
                enableSubmitGoalTimeSheet: true,
                enableSubmitTimeTimeSheet: true,
                isShownComponent: false,
                hourRequestPerPage: 5,
                goalRequestPerPage: 5,
                hourRequestNextUrl: null,
                goalRequestNextUrl: null,
                isAllVisible: false,
                isCurrentDate: false,
                todaysDate: moment().format("D"),
                currentYear: moment().format("YYYY"),
                currentMonth: moment().format("M"),
                isComponentLoaded: false,
                timeSheetHourCurrentDate :  moment().week(),
                timeSheetGoalCurrentDate :  moment().week(),
                timeSheetStartDate : '',
                timeSheetEndDate : '',
                userTimezone : ''
            };
        },
        updated() {

        },
        methods: {
            getTimeSheetWeekName(item,timeSheetType){
                let weekArray = [];
                if(timeSheetType == "time") {
                    weekArray = this.volunteeringHoursWeekName
                } else {
                    weekArray = this.volunteeringGoalWeekName   
                }
                return weekArray[item]
              
            },
            changeTimeSheetGoalView(data) {
                this.timeSheetGoalCurrentDate =  moment(data).week();
            },
            changeTimeSheetHourView(data){
                this.timeSheetHourCurrentDate =  moment(data).week();
            },
            changeTimeDocument(date) {
                let year = moment(date).format("YYYY");
                let month = moment(date).format("M");
                let dates = moment(date).format("D");
                let timeSheetId = '';
                this.timeMissionData.filter( (timeArray) =>{
                    if (timeArray.mission_id == this.currentTimeData.missionId && timeArray.timesheet) {
                        timeArray.timesheet.filter((timeSheetArray) => {
                            if (timeSheetArray.month == month && timeSheetArray.year == year &&
                                timeSheetArray.date == dates) {
                                timeSheetId = timeSheetArray.timesheet_id;
                                this.workDayList.filter( (workList) => {
                                    if (workList[0] == timeSheetArray.day_volunteered) {
                                        this.defaultWorkday = this.languageData.label[
                                            workList[1]];
                                    }
                                });
                            }
                        })
                    }
                })

                if (timeSheetId != '') {
                    this.fetchTimeSheetRealtedData(timeSheetId);
                } else {
                    this.currentTimeData.documents = []
                    this.defaultWorkday = this.languageData.placeholder.workday
                    this.defaultHours = this.languageData.placeholder.spent_hours
                    this.defaultMinutes = this.languageData.placeholder.spent_minutes
                    this.currentTimeData.hours = '';
                    this.currentTimeData.minutes = '';
                    this.currentTimeData.workDay = '';
                    this.currentTimeData.notes = '';
                    this.currentTimeData.day = '';
                    this.currentTimeData.timeSheetId = '';
                    this.currentTimeData.action = '';
                }

            },
            changeGoalDocument(date) {
                let year = moment(date).format("YYYY");
                let month = moment(date).format("M");
                let dates = moment(date).format("D");
                let timeSheetId = '';
                this.goalMissionData.filter( (timeArray)=>  {
                    if (timeArray.mission_id == this.currentTimeData.missionId && timeArray.timesheet) {
                        timeArray.timesheet.filter( (timeSheetArray) => {
                            if (timeSheetArray.month == month && timeSheetArray.year == year &&
                                timeSheetArray.date == dates) {
                                timeSheetId = timeSheetArray.timesheet_id;
                                this.workDayList.filter( (workList) => {
                                    if (workList[0] == timeSheetArray.day_volunteered) {
                                        this.defaultWorkday = this.languageData.label[
                                            workList[1]];
                                    }
                                });
                            }
                        })
                    }
                })

                if (timeSheetId != '') {
                    this.fetchTimeSheetRealtedData(timeSheetId);
                } else {
                    this.currentTimeData.documents = []
                    this.defaultWorkday = this.languageData.placeholder.workday
                    this.defaultHours = this.languageData.placeholder.spent_hours
                    this.defaultMinutes = this.languageData.placeholder.spent_minutes
                    this.currentTimeData.hours = '';
                    this.currentTimeData.minutes = '';
                    this.currentTimeData.workDay = '';
                    this.currentTimeData.notes = '';
                    this.currentTimeData.day = '';
                    this.currentTimeData.timeSheetId = '';
                    this.currentTimeData.action = '';
                }
            },
            getRelatedTimeData(date, timeArray, timeSheetType) {
                this.currentTimeData.missionId = timeArray.mission_id
                this.currentTimeData.day = date
                let missionEndDate = timeArray.end_date
                this.currentTimeData.disabledPastDates = timeArray.start_date
                let futerDate = moment(this.futureDates).format("YYYY-MM-DD");
                let endDate = moment(missionEndDate).format("YYYY-MM-DD");

                if (endDate > futerDate) {
                    this.currentTimeData.disabledFutureDates = futerDate
                } else {
                    this.currentTimeData.disabledFutureDates = endDate
                }

                if (timeArray.timesheet) {
                    let timeSheetArray = timeArray.timesheet;
                    this.currentTimeData.missionName = timeArray.title
                    let months = '';
                    let dates = '';
                    if(timeSheetType == "time") {
                        if(Math.floor(this.volunteeringHoursCurrentMonth) < 10) {
                            months = ("0" + Math.floor(this.volunteeringHoursCurrentMonth)).slice(-2);
                            dates = ("0" + Math.floor(date)).slice(-2);
                        } else {
                            months = Math.floor(this.volunteeringHoursCurrentMonth)
                            dates = Math.floor(date)
                        }
                    } else {
                        if(Math.floor(this.volunteeringGoalCurrentMonth) < 10) {
                            months = ("0" + Math.floor(this.volunteeringGoalCurrentMonth)).slice(-2);
                            dates = ("0" + Math.floor(date)).slice(-2);
                        } else {
                            months = Math.floor(this.volunteeringGoalCurrentMonth)
                            dates = Math.floor(date)
                        }
                    }

                    this.currentTimeData.dateVolunteered = this.volunteeringHoursCurrentYear + '-' + months + '-' + dates

                    timeSheetArray.filter( (timeSheetItem) => {

                        if (timeSheetItem.timesheet_status.status == "APPROVED" ||
                            timeSheetItem.timesheet_status.status == "AUTOMATICALLY_APPROVED"
                        ) {
                            this.volunteerHourDisableDates.push(timeSheetItem.date_volunteered)
                        }

                        let currentArrayDate = timeSheetItem.date
                        let currentArrayYear = timeSheetItem.year
                        let currentArrayMonth = timeSheetItem.month

                        let currentTimeSheetYear = '';
                        let currentTimeSheetMonth = '';
                        if (timeSheetType == 'time') {
                            currentTimeSheetYear = this.volunteeringHoursCurrentYear;
                            currentTimeSheetMonth = this.volunteeringHoursCurrentMonth;
                        } else {
                            currentTimeSheetYear = this.volunteeringGoalCurrentYear;
                            currentTimeSheetMonth = this.volunteeringGoalCurrentMonth;
                        }
                        if (currentTimeSheetYear == currentArrayYear) {
                            if (currentTimeSheetMonth == currentArrayMonth) {
                                if (date == currentArrayDate) {
                                    let timeSheetId = timeSheetItem.timesheet_id
                                    this.currentTimeData.timeSheetId = timeSheetId;

                                    this.fetchTimeSheetRealtedData(timeSheetId);

                                    this.workDayList.filter((workList) => {
                                        if (workList[0] == timeSheetItem.day_volunteered) {
                                            this.defaultWorkday = this.languageData.label[workList[
                                                1]];

                                        }
                                    });
                                }
                            }

                        }

                    });
                }
                if (timeSheetType == 'time') {
                    this.$refs.timeModal.$refs.timeHoursModal.show();
                } else {
                    this.$refs.goalModal.$refs.goalActionModal.show();
                }

            },
            fetchTimeSheetRealtedData(timeSheetId) {
                fetchTimeSheetDocuments(timeSheetId).then(response => {
                    if (response) {
                        let momentObj = moment(response.date_volunteered, 'MM-DD-YYYY');
                        let dateVolunteered = momentObj.format('YYYY-MM-DD');
                        this.currentTimeData.dateVolunteered = dateVolunteered;
                        this.currentTimeData.workDay = response.day_volunteered
                        this.currentTimeData.notes = response.notes
                        this.currentTimeData.documents = response.timesheet_document
                        if (response.hours) {
                            this.currentTimeData.hours = ("0" + response.hours.toString()).slice(-2)
                            this.defaultHours = ("0" + response.hours.toString()).slice(-2)
                        } else {
                            this.currentTimeData.hours = '00'
                            this.defaultHours = '00'
                        }
                        if (response.minutes) {
                            this.currentTimeData.minutes = ("0" + response.minutes.toString()).slice(-2)
                            this.defaultMinutes = ("0" + response.minutes.toString()).slice(-2)
                        } else {
                            this.currentTimeData.minutes = '00'
                            this.defaultMinutes = '00'
                        }

                        this.currentTimeData.action = response.action
                    }
                });
            },
            getTimeSheetHourClass(date, timeSheetArray, timeSheetType) {
                let returnData = [];
                this.isCurrentDate = false;
                let missionEndDate = timeSheetArray.end_date
                let disabledPastDates = moment(timeSheetArray.start_date).format("YYYY-MM-DD HH::mm::ss")
                let disablefuterDate = moment(this.futureDates).format("YYYY-MM-DD HH:mm:ss");
                let endDate = moment(missionEndDate).format("YYYY-MM-DD HH:mm:ss");
               
                let disableEndDate = '';
                if (endDate > disablefuterDate) {
                    disableEndDate = disablefuterDate
                } else {
                    disableEndDate = endDate
                }

                let timeArray = timeSheetArray.timesheet;
                let currentTimeSheetYear = '';
                let currentTimeSheetMonth = '';
                let currentDate = ''
                let currentDataArray = []
                let now = moment().format("YYYY-MM-DD")
                if (timeSheetType == 'time') {
                    currentDataArray = this.volunteeringHoursWeeks

                    currentDataArray.filter((data,index) => {
                        if(data+1 == date) {
                            currentTimeSheetMonth = parseInt(this.volunteeringHoursMonthArray[index])
                            currentTimeSheetYear = parseInt(this.volunteeringHoursYearArray[index])
                        }
                    })
                   
                    let timeMonth = '';
                    let timeDate = '';
                    if(Math.floor(this.volunteeringHoursCurrentMonth) < 10) {
                        timeMonth = ("0" + Math.floor(this.volunteeringHoursCurrentMonth)).slice(-2);
                        timeDate = ("0" + Math.floor(date)).slice(-2);
                    } else {
                        timeMonth = Math.floor(this.volunteeringHoursCurrentMonth)
                        timeDate = Math.floor(date)
                    }
                    
                    currentDate = moment(this.volunteeringHoursCurrentYear + '-' + timeMonth + '-' + timeDate).format("YYYY-MM-DD");
                    if(now == currentDate) {
                        currentDate = moment().tz(this.userTimezone).format("YYYY-MM-DD HH:mm:ss")
                    } else {
                        currentDate = moment(this.volunteeringHoursCurrentYear + '-' + timeMonth + '-' + timeDate).format("YYYY-MM-DD HH::mm:ss");
                    }
                } else {
                    currentDataArray = this.volunteeringGoalWeeks

                    currentDataArray.filter((data,index) => {
                        if(data+1 == date) { 
                            currentTimeSheetMonth = parseInt(this.volunteeringGoalMonthArray[index])
                            currentTimeSheetYear = parseInt(this.volunteeringGoalYearArray[index])
                        }
                    })
                    let goalMonth = '';
                    let goalDate = '';
                    if(Math.floor(this.volunteeringGoalCurrentMonth) < 10) {
                        goalMonth = ("0" + Math.floor(this.volunteeringGoalCurrentMonth)).slice(-2);
                        goalDate = ("0" + Math.floor(date)).slice(-2);
                    } else {
                        goalMonth = Math.floor(this.volunteeringGoalCurrentMonth)
                        goalDate = Math.floor(date)
                    }

                    currentDate = moment(this.volunteeringGoalCurrentYear + '-' + goalMonth + '-' + goalDate).format("YYYY-MM-DD");
                    if(now == currentDate) {
                        currentDate = moment().tz(this.userTimezone).format("YYYY-MM-DD HH:mm:ss")
                    } else {
                        currentDate = moment(this.volunteeringGoalCurrentYear + '-' + goalMonth + '-' + goalDate).format("YYYY-MM-DD HH::mm:ss");
                    }
                }

                timeArray.filter((timeSheetItem) => {
                    let currentArrayDate = timeSheetItem.date
                    let currentArrayYear = timeSheetItem.year
                    let currentArrayMonth = timeSheetItem.month

                    if (currentTimeSheetYear == currentArrayYear) {
                        if (currentTimeSheetMonth == currentArrayMonth) {
                            if (date == currentArrayDate) {
                                if (timeSheetItem.timesheet_status.status == "APPROVED" || timeSheetItem
                                    .timesheet_status.status == "AUTOMATICALLY_APPROVED") {
                                    returnData.push("approved")
                                } else if (timeSheetItem.timesheet_status.status == "DECLINED") {
                                    returnData.push("declined")
                                } else if (timeSheetItem.timesheet_status.status == "SUBMIT_FOR_APPROVAL") {
                                    returnData.push("approval")
                                } else {
                                    returnData.push("default-time")
                                }
                            }
                        }
                    }
                });
                if (currentDate < disabledPastDates && timeSheetArray.start_date != null) {
                    returnData.push("disabled")
                }
                if (currentDate > disableEndDate) {
                    returnData.push("disabled")
                }

                return returnData;
            },
            highLightCurrentDate(date, timeSheetType) {
                let currentTimeSheetYear = ''
                let currentTimeSheetMonth = ''
                if (timeSheetType == 'time') {
                    currentTimeSheetYear = this.volunteeringHoursCurrentYear;
                    currentTimeSheetMonth = this.volunteeringHoursCurrentMonth;

                } else {
                    currentTimeSheetYear = this.volunteeringGoalCurrentYear;
                    currentTimeSheetMonth = this.volunteeringGoalCurrentMonth;

                }
                if ((this.todaysDate == date) && (currentTimeSheetYear == this.currentYear) && (currentTimeSheetMonth ==
                        this.currentMonth)) {
                    return true
                }
            },
            changeVolunteeringHours(data) {
                this.enableSubmitTimeTimeSheet = true;
                this.tableLoaderActive = true
                this.volunteeringHoursCurrentMonth = data.month
                this.volunteeringHoursCurrentYear = data.year
                data.weekdays.shift();
                this.volunteeringHoursWeeks = data.days;
                this.volunteeringHoursWeekName = data.weekdays
                this.volunteeringHoursMonthArray = data.monthArray,
                this.volunteeringHoursYearArray = data.yearArray,
                setTimeout( () => {
                    this.tableLoaderActive = false
                })
            },
            changeVolunteeringGoals(data) {
                this.enableSubmitGoalTimeSheet = true;
                this.goalTableLoaderActive = true
                this.volunteeringGoalCurrentMonth = data.month
                this.volunteeringGoalCurrentYear = data.year
                data.weekdays.shift();
                this.volunteeringGoalWeeks = data.days
                this.volunteeringGoalWeekName = data.weekdays
                this.volunteeringGoalMonthArray = data.monthArray,
                this.volunteeringGoalYearArray = data.yearArray,
                setTimeout(() => {
                    this.goalTableLoaderActive = false
                })
            },
            updateWorkday(value) {
                this.defaultWorkday = value.selectedVal;
            },
            updateDefaultValue(value) {
                if (value.fieldId == "workday") {
                    this.defaultWorkday = value.selectedVal;
                }
                if (value.fieldId == "hours") {
                    this.defaultHours = value.selectedVal
                }
                if (value.fieldId == "minutes") {
                    this.defaultMinutes = value.selectedVal
                }
            },
            updateMission(value) {
                this.defaultMission = value;
            },
            async getVolunteerHoursData() {
                this.tableLoaderActive = true
                await volunteerTimesheetHours().then(response => {
                    if (response) {
                        this.timeMissionData = response['TIME']
                        this.goalMissionData = response['GOAL']
                        if (response['TIME'].length > 0 || this.goalMissionData.length > 0) {
                            this.isAllVisible = true
                        } else {
                            this.isAllVisible = false
                        }
                    }
                    this.isComponentLoaded = true
                    this.tableLoaderActive = false
                })
            },
            getTime(date, timeArray, timeSheetType) {
                let returnData = '';
                let dates = date + 1;
                let currentValueYear = ''
                let currentValueMonth = ''
                let currentDataArray = []
                if (timeSheetType == "time") {
                    currentDataArray = this.volunteeringHoursWeeks
                    currentDataArray.filter((data,index) => {
                        if(data == date) {
                            currentValueMonth = parseInt(this.volunteeringHoursMonthArray[index])
                            currentValueYear = parseInt(this.volunteeringHoursYearArray[index])
                        }
                    })
                } else {
                    currentDataArray = this.volunteeringGoalWeeks
                    currentDataArray.filter((data,index) => {
                        if(data == date) {
                            currentValueMonth = parseInt(this.volunteeringGoalMonthArray[index])
                            currentValueYear = parseInt(this.volunteeringGoalYearArray[index])
                        }
                    })
                }    

                timeArray.filter( (timeSheetItem) => {
                    let currentArrayDate = parseInt(timeSheetItem.date)
                    let currentArrayYear = parseInt(timeSheetItem.year)
                    let currentArrayMonth = parseInt(timeSheetItem.month)
                    if (timeSheetType == "time") {
                        if (currentValueYear == currentArrayYear) {
                            if (currentValueMonth == currentArrayMonth) {
                                if (dates == currentArrayDate) {
                                    returnData = timeSheetItem.time
                                }
                            }
                        }
                    } else {
                        if (currentValueYear == currentArrayYear) {
                            if (currentValueMonth == currentArrayMonth) {
                                if (dates == currentArrayDate) {
                                    returnData = timeSheetItem.action
                                }
                            }
                        }
                    }
                });
                return returnData
            },
            getRawHourTotal(timeArray, timeSheetType) {
                let time1 = "00:00";
                let hour = 0
                let minute = 0
                let action = 0
                
                if (timeArray) {
                    timeArray.filter( (timeSheetItem) => {
                        let currentArrayYear = timeSheetItem.year
                        let currentArrayMonth = timeSheetItem.month
                        let time2 = timeSheetItem.time;
                        if (timeSheetType == "time") {
                            if (this.volunteeringHoursCurrentYear == currentArrayYear) {
                                if (this.volunteeringHoursCurrentMonth == currentArrayMonth) {
                                    let splitTime1 = time1.split(':');
                                    let splitTime2 = time2.split(':');

                                    hour = parseInt(splitTime1[0]) + parseInt(splitTime2[0]);
                                    minute = parseInt(splitTime1[1]) + parseInt(splitTime2[1]);
                                    hour = hour + minute / 60;
                                    minute = minute % 60;
                                    if(Math.floor(hour) < 10) {
                                        time1 = ("0" + Math.floor(hour)).slice(-2) + ':' + ("0" + Math.floor(
                                        minute)).slice(-2);
                                    } else {
                                        time1 = Math.floor(hour) + ':' + ("0" + Math.floor(
                                        minute)).slice(-2);
                                    }
                                    
                                }
                            }
                        } else {
                            if (this.volunteeringGoalCurrentYear == currentArrayYear) {
                                if (this.volunteeringGoalCurrentMonth == currentArrayMonth) {
                                    action = action + timeSheetItem.action
                                }
                            }

                        }
                    });
                }
                if (timeSheetType == "time") {
                    if (time1 != "00:00") {
                        return time1;
                    }

                }
                if (timeSheetType == "goal") {
                    if (action != 0) {
                        return action;
                    }

                }
            },
            getColumnHourTotal(day, timeSheetType) {
                let time1 = "00:00";
                let hour = 0;
                let minute = 0;
                let action = 0;
                let timeArray = []

                let currentValueYear = ''
                let currentValueMonth = ''
                let currentDataArray = []
                
                if (timeSheetType == "time") {
                    timeArray = this.timeMissionData;
                    currentDataArray = this.volunteeringHoursWeeks
                    currentDataArray.filter((data,index) => {
                        if(data+1 == day) {
                            currentValueMonth = parseInt(this.volunteeringHoursMonthArray[index])
                            currentValueYear = parseInt(this.volunteeringHoursYearArray[index])
                        }
                    })
                } else {
                    timeArray = this.goalMissionData;
                    currentDataArray = this.volunteeringGoalWeeks
                    currentDataArray.filter((data,index) => {
                        if(data+1 == day) {
                            currentValueMonth = parseInt(this.volunteeringGoalMonthArray[index])
                            currentValueYear = parseInt(this.volunteeringGoalYearArray[index])
                        }
                    })
                }  

                if (timeArray.length > 0) {
                    timeArray.filter( (timeSheetItem) => {
                        let timeEntryData = timeSheetItem.timesheet;
                        timeEntryData.filter( (timeEntry) => {

                            let currentArrayDate = timeEntry.date
                            let currentArrayYear = timeEntry.year
                            let currentArrayMonth = timeEntry.month
                            let time2 = timeEntry.time;
                            if (timeSheetType == "time") {
                                if (currentValueYear == currentArrayYear) {
                                    if (currentValueMonth == currentArrayMonth) {
                                        if (day == currentArrayDate) {

                                            let splitTime1 = time1.split(':');
                                            let splitTime2 = time2.split(':');
                                            hour = parseInt(splitTime1[0]) + parseInt(splitTime2[0]);
                                            minute = parseInt(splitTime1[1]) + parseInt(splitTime2[1]);
                                            hour = hour + minute / 60;
                                            minute = minute % 60;
                                            if(Math.floor(hour) < 10) { 
                                                time1 = ("0" + Math.floor(hour)).slice(-2) + ':' + ("0" +
                                                Math.floor(minute)).slice(-2);
                                            } else {
                                                 time1 = Math.floor(hour) + ':' + ("0" +
                                                Math.floor(minute)).slice(-2);
                                            }

                                        }
                                    }
                                }
                            } else {
                                if (currentValueYear == currentArrayYear) {
                                    if (currentValueMonth == currentArrayMonth) {
                                        if (day == currentArrayDate) {
                                            action = action + timeEntry.action
                                        }
                                    }
                                }
                            }

                        });

                    });
                }
                if (timeSheetType == "time") {
                    if (time1 != "00:00") {

                        return time1;
                    }

                }
                if (timeSheetType == "goal") {
                    if (action != 0) {
                        return action;
                    }

                }
            },
            getTotalHours(timeSheetType) {
                let time1 = "00:00";
                let timeApproved = "00:00";
                let action = 0;
                let actionApproved = 0;
                let hour = 0;
                let minute = 0;
                let hourApproved = 0;
                let minuteApproved = 0;

                let timeArray = []
                if (timeSheetType == "time") {
                    timeArray = this.timeMissionData;
                } else {
                    timeArray = this.goalMissionData;
                }
               
                timeArray.filter((timeSheetItem) => {
                    let timeEntryData = timeSheetItem.timesheet;
                    timeEntryData.filter((timeEntry) => {
                        let currentArrayYear = timeEntry.year
                        let currentArrayMonth = timeEntry.month
                        let time2 = timeEntry.time;
                        if (timeSheetType == "time") {
                            if (this.volunteeringHoursCurrentYear == currentArrayYear) {
                                if (this.volunteeringHoursCurrentMonth == currentArrayMonth) {
                                    let splitTime1 = time1.split(':');
                                    let splitTime2 = time2.split(':');
                                    hour = parseInt(splitTime1[0]) + parseInt(splitTime2[0]);
                                    minute = parseInt(splitTime1[1]) + parseInt(splitTime2[1]);
                                    hour = hour + minute / 60;
                                    minute = minute % 60;
                                    time1 = Math.floor(hour) + ':' + ("0" + Math
                                        .floor(minute)).slice(-2);
                                    if (timeEntry.timesheet_status.status != "APPROVED" && timeEntry
                                        .timesheet_status.status != "AUTOMATICALLY_APPROVED") {
                                        //for submit request
                                        let splitTimeApproved = timeApproved.split(':');
                                        hourApproved = parseInt(splitTimeApproved[0]) + parseInt(
                                            splitTime2[0]);
                                        minuteApproved = parseInt(splitTimeApproved[1]) + parseInt(
                                            splitTime2[1]);
                                        hourApproved = hourApproved + minuteApproved / 60;
                                        minuteApproved = minuteApproved % 60;
                                       
                                        if(Math.floor(hourApproved) < 10) { 
                                            timeApproved = ("0" + Math.floor(hourApproved)).slice(-2) +
                                            ':' + ("0" + Math.floor(minuteApproved)).slice(-2);
                                        } else {
                                            timeApproved =  Math.floor(hourApproved) +
                                            ':' + ("0" + Math.floor(minuteApproved)).slice(-2);
                                        }
                                    }

                                }
                            }
                             
                        } else {
                            if (this.volunteeringGoalCurrentYear == currentArrayYear) {
                                if (this.volunteeringGoalCurrentMonth == currentArrayMonth) {

                                    action = action + timeEntry.action
                                    if (timeEntry.timesheet_status.status != "APPROVED" && timeEntry
                                        .timesheet_status.status != "AUTOMATICALLY_APPROVED") {
                                        actionApproved = actionApproved + timeEntry.action
                                    }
                                }
                            }
                        }
                    });

                });

                if (timeSheetType == "time") {
                    if (time1 != "00:00") {
                        if (timeApproved != "00:00") {
                            this.enableSubmitTimeTimeSheet = false;
                        }
                        return time1;
                    }
                }
                if (timeSheetType == "goal") {
                    if (action != 0) {
                        if (actionApproved != 0) {
                            this.enableSubmitGoalTimeSheet = false;
                        }
                        return action;
                    }

                }
            },
            updateMinutes(value) {
                this.defaultMinutes = value.selectedVal
            },
            hideModal() {
                store.commit('removeTimeSheetDetail');
                this.currentTimeData.missionId = '';
                this.currentTimeData.hours = '';
                this.currentTimeData.minutes = '';
                this.currentTimeData.dateVolunteered = '';
                this.currentTimeData.workDay = '';
                this.currentTimeData.notes = '';
                this.currentTimeData.day = '';
                this.currentTimeData.timeSheetId = '';
                this.currentTimeData.documents = [];
                this.volunteerHourDisableDates = [],
                this.currentTimeData.disabledPastDates = '',
                this.currentTimeData.disabledFutureDates = '',
                this.currentTimeData.missionName = ''
                this.currentTimeData.action = ''
                this.files = [];
                this.defaultHours = this.languageData.placeholder.spent_hours
                this.defaultMinutes = this.languageData.placeholder.spent_minutes
                this.defaultWorkday = this.languageData.placeholder.workday
            },
            submitVolunteerTimeSheet(timeSheetType) {
                let timeSheetId = {
                    'timesheet_entries': []
                }

                let timeArray = [];
                let currentYear = ''
                let currentMonth = ''
                if (timeSheetType == "time") {
                    timeArray = this.timeMissionData;
                    currentYear = this.volunteeringHoursCurrentYear
                    currentMonth = this.volunteeringHoursCurrentMonth
                } else {
                    timeArray = this.goalMissionData;
                    currentYear = this.volunteeringGoalCurrentYear
                    currentMonth = this.volunteeringGoalCurrentMonth
                }
                if (timeArray) {
                    timeArray.filter( (timeMission) => {
                        let timeSheetArray = timeMission.timesheet;
                        if (timeSheetArray) {
                            timeSheetArray.filter( (timeSheet) => {
                                let currentArrayYear = timeSheet.year
                                let currentArrayMonth = timeSheet.month
                                if (currentYear == currentArrayYear) {
                                    if (currentMonth == currentArrayMonth) {
                                        if (timeSheet.timesheet_status.status != "APPROVED" && timeSheet
                                            .timesheet_status.status != "AUTOMATICALLY_APPROVED") {
                                            timeSheetId.timesheet_entries.push({
                                                'timesheet_id': timeSheet.timesheet_id
                                            })
                                        }
                                    }
                                }
                            });
                        }
                    });
                }
                submitVolunteerHourTimeSheet(timeSheetId).then(response => {
                    if (response.error == true) {
                        this.makeToast("danger", response.message);
                    } else {
                        this.getVolunteerHoursData()
                        if (timeSheetType == "time") {
                            this.getTimeRequestData(this.currentPage);
                        } else {
                            this.getGoalRequestData(this.currentPage);
                        }
                        this.makeToast("success", response.message);
                    }
                })

            },
            makeToast(variant = null, message) {
                this.$bvToast.toast(message, {
                    variant: variant,
                    solid: true,
                    autoHideDelay: 1000
                })
            },
            getTimeRequest(currentPage) {
                this.getTimeRequestData(currentPage);
            },
            getTimeRequestData(currentPage) {
                let currentData = [];
                timeRequest(currentPage).then(response => {
                    if (response.data) {
                        let data = response.data;
                        let mission = this.languageData.label.mission;
                        let time = this.languageData.label.time;
                        let hours = this.languageData.label.hours;
                        let organisation = this.languageData.label.organisation;

                        if (response.pagination) {
                            this.hourRequestTotalRow = response.pagination.total;
                            this.hourRequestCurrentPage = response.pagination.current_page
                            this.hourRequestPerPage = response.pagination.per_page;
                            this.hourRequestNextUrl = response.pagination.next_url
                            this.timeMissionTotalPage = response.pagination.total_pages;
                        }

                        data.filter( (item) => {
                            currentData.push({
                                [mission]: item.title,
                                [time]: item.time,
                                [hours]: item.hours,
                                [organisation]: item.organisation_name,
                                ['mission_id']: item.mission_id
                            })
                            this.timesheetRequestItems = currentData;
                        })
                    }
                })
            },
            getGoalRequest(currentPage) {
                this.getGoalRequestData(currentPage);
            },
            getGoalRequestData(currentPage) {
                setTimeout( () => {
                    if (store.state.missionId != '' && store.state.missionId != null) {
                        let missionId = store.state.missionId;
                        let timeSheetType = store.state.missionType.toLowerCase()
                        let timeArray = []
                        let timeSheetArray = []
                        let date = moment().format('D')
                        if (timeSheetType == "time") {
                            timeArray = this.timeMissionData
                        } else {
                            timeArray = this.goalMissionData
                        }
                        timeArray.filter( (timeArray) => {
                            if (timeArray.mission_id == missionId) {
                                timeSheetArray = timeArray;
                            }
                        })
                        setTimeout(() => {
                            if (timeSheetArray) {
                                this.getRelatedTimeData(date, timeSheetArray, timeSheetType)
                            }
                        }, 500)
                    }
                }, 200)
                let currentData = [];
                goalRequest(currentPage).then(response => {
                    if (response.data) {
                        let data = response.data;
                        let mission = this.languageData.label.mission;
                        let action = this.languageData.label.actions;
                        let organisation = this.languageData.label.organisation;
                        if (response.pagination) {
                            this.goalRequestTotalRow = response.pagination.total;
                            this.goalRequestCurrentPage = response.pagination.current_page
                            this.goalRequestPerPage = response.pagination.per_page;
                            this.goalRequestNextUrl = response.pagination.next_url
                            this.goalMissionTotalPage = response.pagination.total_pages;
                        }

                        data.filter( (item) => {
                            currentData.push({
                                [mission]: item.title,
                                [action]: item.action,
                                [organisation]: item.organisation_name,
                                ['mission_id']: item.mission_id
                            })
                            this.goalRequestItems = currentData
                        })
                    }
                })
            }
        },
        created() {
            this.languageData = JSON.parse(store.state.languageLabel);
            this.defaultWorkday = this.languageData.placeholder.workday
            this.defaultHours = this.languageData.placeholder.spent_hours
            this.defaultMinutes = this.languageData.placeholder.spent_minutes
            this.timeRequestLabel = this.languageData.label.hours_requests
            this.goalRequestLabel = this.languageData.label.goals_requests
            this.userTimezone = store.state.userTimezone
            this.getVolunteerHoursData();
            this.isShownComponent = true;
            setTimeout(() => {
                this.getTimeRequestData(this.hourRequestCurrentPage);
            }, 80)
            setTimeout(() => {
                this.getGoalRequestData(this.goalRequestCurrentPage);
            }, 100)

            let timeRequestFieldArray = [
                this.languageData.label.mission,
                this.languageData.label.time,
                this.languageData.label.hours,
                this.languageData.label.organisation,
            ]

            let goalRequestFieldArray = [
                this.languageData.label.mission,
                this.languageData.label.actions,
                this.languageData.label.organisation,
            ]

            goalRequestFieldArray.filter( (data) => {
                this.goalRequestFields.push({
                    "key": data
                })
            });

            timeRequestFieldArray.filter( (data) => {
                this.timesheetRequestFields.push({
                    "key": data
                })
            });
        }
    };
</script>