<template>

    <div class="dashboard-timesheet inner-pages">
        <header>
        <ThePrimaryHeader v-if="isShownComponent"></ThePrimaryHeader>
        </header>
        <main>
            
            <DashboardBreadcrumb />
            <div class="dashboard-tab-content">
                <b-container>
                <div class="heading-section">
                    <h1>
                        {{langauageData.label.volunteering_timesheet}}  
                    </h1>
                </div>
                <div class="inner-content-wrap" v-if="isAllVisible">
                    <div class="dashboard-table">
                        <div class="table-outer">
                            <div class="table-inner">
                                <h3>{{langauageData.label.volunteering_hours}}</h3>
                                <VolunteeringTimesheetTableHeader
                                    @updateCall="changeVolunteeringHours"
                                />
                                <div class="table-wrapper-outer">
                                    <div v-bind:class="{ 'content-loader-wrap': true, 'loader-active': tableLoaderActive}">
                                        <div class="content-loader"></div>
                                    </div>
                                    <b-table-simple
                                    small
                                    responsive
                                    bordered
                                    class="timesheet-table timesheethours-table"
                                    >
                                        <b-thead>
                                            <b-tr>
                                                <b-th class="mission-col">{{langauageData.label.mission}}</b-th>
                                                <b-th v-for="(item,key) in volunteeringHoursWeeks">
                                                    {{key+1}}<span>{{item}}</span> 
                                                </b-th>  
                                                <b-th class="total-col">{{langauageData.label.total}}</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody v-if="timeMissionData.length > 0">
                                            <b-tr v-for="(timeItem,key) in timeMissionData">
                                                <b-td class="mission-col">{{timeItem.title}}</b-td>
                                                <b-td 
                                                :mission-id="timeItem.mission_id"
                                                :date="key+1"
                                                v-on:click="getRelatedTimeData(key+1,timeItem,'time')"
                                                 v-bind:class="[getTimeSheetHourClass(key+1,timeItem,'time')]"
                                               
                                                v-for="(item,key) in volunteeringHoursWeeks">
                                                    {{getTime(key,timeItem.timesheet,'time')}} 
                                                </b-td> 
                                                <b-td class="total-col">{{getRawHourTotal(timeItem.timesheet,'time')}}</b-td>
                                            </b-tr>
                                            <b-tr class="total-row">
                                                <b-td class="mission-col">{{langauageData.label.total}}:</b-td>
                                                <b-td v-for="(item,key) in volunteeringHoursWeeks">
                                                    {{getColumnHourTotal(key+1,'time')}}
                                                </b-td> 
                                                <b-td>
                                                    {{getTotalHours('time')}}
                                                </b-td>
                                            </b-tr>
                                        </b-tbody>

                                    </b-table-simple>
                                </div>
                            </div>
                            <div class="btn-block">
                                <b-button class="btn-bordersecondary ml-auto"
                                v-bind:class="{
                                    disabled : enableSubmitTimeTimeSheet    
                                }"
                                @click="submitVolunteerTimeSheet('time')">{{langauageData.label.submit}}</b-button>
                            </div>
                        </div>
                        <ul class="meta-data-list">
                            <li class="approve-indication">{{langauageData.label.approved}}</li>
                            <li class="decline-indication">{{langauageData.label.declined}}</li>
                            <li class="approval-indication">
                                {{langauageData.label.submit_for_approval}}
                            </li>
                        </ul>
                    <!--    {{goalMissionData}} -->
                        <div class="table-outer timesheet-table-outer">
                            <div class="table-inner">
                                <h3>{{langauageData.label.volunteering_goals}}</h3>
                                <VolunteeringTimesheetTableHeader
                                    @updateCall="changeVolunteeringGoals"
                                />
                                <div class="table-wrapper-outer">
                                    <div v-bind:class="{ 'content-loader-wrap': true, 'loader-active': goalTableLoaderActive}">
                                        <div class="content-loader"></div>
                                    </div>
                                    <b-table-simple
                                        small
                                        responsive
                                        bordered
                                        class="timesheet-table timesheetgoals-table"
                                        >
                                        <b-thead>
                                            <b-tr>
                                                <b-th class="mission-col">{{langauageData.label.mission}}</b-th>
                                                <b-th v-for="(item,key) in volunteeringGoalWeeks">
                                                    {{key+1}}<span>{{item}}</span> 
                                                </b-th>  
                                                <b-th class="total-col">{{langauageData.label.total}}</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody v-if="goalMissionData.length > 0">
                                            <b-tr v-for="(timeItem,key) in goalMissionData">
                                                <b-td class="mission-col">{{timeItem.title}}</b-td>
                                                <b-td 
                                                    :mission-id="timeItem.mission_id"
                                                    :date="key+1"
                                                    v-on:click="getRelatedTimeData(key+1,timeItem,'goal')"
                                                     v-bind:class="[getTimeSheetHourClass(key+1,timeItem,'goal')]"
                                                   
                                            
                                                v-for="(item,key) in volunteeringGoalWeeks">
                                                {{getTime(key,timeItem.timesheet,'goal')}} 
                                                </b-td> 
                                                <b-td class="total-col">{{getRawHourTotal(timeItem.timesheet,'goal')}}</b-td>
                                            </b-tr>
                                            <b-tr class="total-row">
                                                <b-td class="mission-col">{{langauageData.label.total}}:</b-td>
                                                <b-td v-for="(item,key) in volunteeringGoalWeeks">
                                                {{getColumnHourTotal(key+1,'goal')}}
                                                </b-td> 
                                                <b-td>
                                                {{getTotalHours('goal')}}
                                                </b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                            </div>
                            <div class="btn-block">
                                <b-button class="btn-bordersecondary ml-auto"
                                    @click="submitVolunteerTimeSheet('goal')"
                                    v-bind:class="{
                                        disabled : enableSubmitGoalTimeSheet    
                                    }"
                                >{{langauageData.label.submit}}</b-button>
                            </div>
                        </div> 
                            <ul class="meta-data-list">
                                    <li class="approve-indication">{{langauageData.label.approved}}</li>
                                    <li class="decline-indication">{{langauageData.label.declined}}</li>
                                    <li class="approval-indication">
                                        {{langauageData.label.submit_for_approval}}
                                    </li>
                            </ul>
                            <VolunteeringRequest
                                    :headerField="timesheetRequestFields"
                                    :items="timesheetRequestItems"
                                    :headerLable="timeRequestLabel"
                                    :currentPage="hourRequestCurrentPage"
                                    :totalRow="hourRequestTotalRow"
                                    @updateCall = "getTimeRequest"
                                    exportUrl = "app/timesheet/time-requests/export"
                                    :perPage = "hourRequestPerPage"
                                    :nextUrl = "hourRequestNextUrl"
                                    :fileName="langauageData.export_timesheet_file_names.PENDING_TIME_MISSION_ENTRIES_XLSX"
                                />
                        
                            <VolunteeringRequest
                                :headerField="goalRequestFields"
                                :items="goalRequestItems"
                                :headerLable="goalRequestLabel"
                                :currentPage="goalRequestCurrentPage"
                                :totalRow="goalRequestTotalRow"
                                @updateCall = "getGoalRequest"
                                :perPage = "goalRequestPerPage"    
                                :nextUrl = "goalRequestNextUrl"
                                exportUrl = "app/timesheet/goal-requests/export"
                                :fileName="langauageData.export_timesheet_file_names.PENTIND_GOAL_MISSION_ENTRIES_XLSX"
                            />
                    </div>

                    <AddVolunteeringHours
                        ref="timeModal"
                        :defaultWorkday="defaultWorkday"
                        :defaultHours="defaultHours"
                        :defaultMinutes="defaultMinutes"
                        :files="files"
                        :timeEntryDefaultData="currentTimeData"
                        :disableDates="volunteerHourDisableDates"
                        @getTimeSheetData = "getVolunteerHoursData"
                        @resetModal ="hideModal"
                        :workDayList="workDayList"
                        @updateCall="updateDefaultValue" 
                        @changeDocument="changeTimeDocument"
                    />
                    <AddVolunteeringAction
                        ref="goalModal"
                        :defaultWorkday="defaultWorkday"
                        :defaultHours="defaultHours"
                        :defaultMinutes="defaultMinutes"
                        :files="files"
                        :timeEntryDefaultData="currentTimeData"
                        :disableDates="volunteerHourDisableDates"
                        @getTimeSheetData = "getVolunteerHoursData"
                        @resetModal ="hideModal"
                        :workDayList="workDayList"
                        @updateCall="updateDefaultValue" 
                        @changeDocument="changeGoalDocument"
                    />
                </div>
                <div v-else>
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
import AppCustomDropdown from "../components/CustomFieldDropdown";
import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
import VolunteeringTimesheetTableHeader from "../components/VolunteeringTimesheetTableHeader"
import AddVolunteeringHours from "../components/AddVolunteeringHours";
import AddVolunteeringAction from "../components/AddVolunteeringAction";
import VolunteeringRequest from "../components/VolunteeringRequest";
import SimpleBar from "simplebar";
import constants from '../constant';
import axios from "axios";
import store from '../store';
import DatePicker from "vue2-datepicker";
import FileUpload from 'vue-upload-component';
import {volunteerTimesheetHours,fetchTimeSheetDocuments,submitVolunteerHourTimeSheet,timeRequest,goalRequest} from '../services/service';
import moment from 'moment'
import { setTimeout } from 'timers';

export default {
    components: {
        ThePrimaryHeader,
        TheSecondaryFooter,
        AppCustomDropdown,
        SimpleBar,
        DashboardBreadcrumb,
        VolunteeringTimesheetTableHeader,
        AddVolunteeringAction,
        DatePicker,
        FileUpload,
        AddVolunteeringHours,
        VolunteeringRequest
    },
    name: "DashboardTimesheet",
    data() {
        return {
            files: [],
            tableLoaderActive : true,
            goalTableLoaderActive : true,
            langauageData : [],
            VolunteeringRequest : [],
            timesheetRequestFields: [],
            timesheetRequestItems: [],
            hourRequestCurrentPage : 1,
            goalRequestCurrentPage : 1,
            hourRequestTotalRow : 0,
            goalRequestTotalRow : 0,
            goalRequestFields: [],
            goalRequestItems: [],
            defaultWorkday: "",
            workDayList: [
                ["WORKDAY","workday"],
                ["WEEKEND","weekend"],
                ["HOLIDAY","holiday"],
            ],
            rows: 25,
            perPage: 2,
            currentPage: 1,
            volunteeringHoursCurrentMonth : '',
            volunteeringHoursCurrentYear : '',
            volunteeringHoursWeeks : [],
            volunteeringGoalCurrentMonth : '',
            volunteeringGoalCurrentYear : '',
            volunteeringGoalWeeks : [],
            timeMissionData : [],
            goalMissionData : [],
            timeRequestLabel :"",
            goalRequestLabel : "",
            currentTimeData: 
                {
                    missionId : '',
                    hours : '',
                    minutes : '',
                    dateVolunteered : '',
                    workDay : '',
                    notes : '',
                    day: '',
                    timeSheetId : '',
                    documents : [],
                    disabledPastDates : '',
                    disabledFutureDates : '',
                    missionName : '',
                    action : ''
                }
            ,
            timeEntryDefaultData : null,
            isTimeEntryModelShown : false,
            volunteerHourDisableDates : [],
            defaultHours : '',
            defaultMinutes : '',
            futureDates: new Date(),
            enableSubmitGoalTimeSheet : true,
            enableSubmitTimeTimeSheet : true,
            isShownComponent : false,
            hourRequestPerPage : 5,
            goalRequestPerPage : 5,
            hourRequestNextUrl : null,
            goalRequestNextUrl : null,
            isAllVisible : true,
            isCurrentDate : false,
            todaysDate :  moment().format("D"),
            currentYear :  moment().format("YYYY"),
            currentMonth :  moment().format("M")
        };
    },
    updated() {
    
    },
    methods: {
        changeTimeDocument(date) {
            var _this = this;
            let year = moment(date).format("YYYY");
            let month = moment(date).format("M");
            let dates = moment(date).format("D");
            let timeSheetId = '';
            this.timeMissionData.filter(function(timeArray,timeIndex) {
                if(timeArray.mission_id == _this.currentTimeData.missionId && timeArray.timesheet) {
                    timeArray.timesheet.filter(function(timeSheetArray,timeSheetIndex) {
                        if(timeSheetArray.month == month && timeSheetArray.year == year && timeSheetArray.date == dates) {
                            timeSheetId = timeSheetArray.timesheet_id;
                            _this.workDayList.filter(function (workList, workIndex) {
                                if(workList[0] == timeSheetArray.day_volunteered) {
                                    _this.defaultWorkday = _this.langauageData.label[workList[1]];
                                }
                            });
                        } 
                    })
                }
            })

            if(timeSheetId != '') {
                _this.fetchTimeSheetRealtedData(timeSheetId);
            } else {
                _this.currentTimeData.documents = []
                _this.defaultWorkday = _this.langauageData.placeholder.workday 
                _this.defaultHours = _this.langauageData.placeholder.spent_hours
                _this.defaultMinutes = _this.langauageData.placeholder.spent_minutes
                _this.currentTimeData.hours = '';
                _this.currentTimeData.minutes = '';
                _this.currentTimeData.workDay = '';
                _this.currentTimeData.notes = '';
                _this.currentTimeData.day= '';
                _this.currentTimeData.timeSheetId = '';
                _this.currentTimeData.action = '';
            }
           
        },
        changeGoalDocument(date) {
            var _this = this;
            let year = moment(date).format("YYYY");
            let month = moment(date).format("M");
            let dates = moment(date).format("D");
            let timeSheetId = '';
            this.goalMissionData.filter(function(timeArray,timeIndex) {
                if(timeArray.mission_id == _this.currentTimeData.missionId && timeArray.timesheet) {
                    timeArray.timesheet.filter(function(timeSheetArray,timeSheetIndex) {
                        if(timeSheetArray.month == month && timeSheetArray.year == year && timeSheetArray.date == dates) {
                            timeSheetId = timeSheetArray.timesheet_id;
                            _this.workDayList.filter(function (workList, workIndex) {
                                if(workList[0] == timeSheetArray.day_volunteered) {
                                    _this.defaultWorkday = _this.langauageData.label[workList[1]];
                                }
                            });
                        } 
                    })
                }
            })

            if(timeSheetId != '') {
                _this.fetchTimeSheetRealtedData(timeSheetId);
            } else {
                _this.currentTimeData.documents = []
                _this.defaultWorkday = _this.langauageData.placeholder.workday 
                _this.defaultHours = _this.langauageData.placeholder.spent_hours
                _this.defaultMinutes = _this.langauageData.placeholder.spent_minutes
                _this.currentTimeData.hours = '';
                _this.currentTimeData.minutes = '';
                _this.currentTimeData.workDay = '';
                _this.currentTimeData.notes = '';
                _this.currentTimeData.day= '';
                _this.currentTimeData.timeSheetId = '';
                _this.currentTimeData.action = '';
            }
        },
        getRelatedTimeData(date,timeArray,timeSheetType) {
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

            var _this = this;
            if(timeArray.timesheet) { 
                let timeSheetArray =  timeArray.timesheet;
                _this.currentTimeData.missionName = timeArray.title
                timeSheetArray.filter(function (timeSheetItem, timeSheetIndex) {
                    if(timeSheetItem.timesheet_status.status == "APPROVED" || 
                        timeSheetItem.timesheet_status.status == "AUTOMATICALLY_APPROVED"
                    ) {
                        _this.volunteerHourDisableDates.push(timeSheetItem.date_volunteered)
                    }

                           
                    _this.currentTimeData.dateVolunteered = _this.volunteeringHoursCurrentYear+'-'+_this.volunteeringHoursCurrentMonth+'-'+date
                     
                    let currentArrayDate = timeSheetItem.date
                    let currentArrayYear = timeSheetItem.year
                    let currentArrayMonth = timeSheetItem.month
                    let currentTimeSheetYear = '';
                    let currentTimeSheetMonth = '';
                    if(timeSheetType == 'time') {
                        currentTimeSheetYear = _this.volunteeringHoursCurrentYear;
                        currentTimeSheetMonth = _this.volunteeringHoursCurrentMonth;
                    } else {
                        currentTimeSheetYear = _this.volunteeringGoalCurrentYear;
                        currentTimeSheetMonth = _this.volunteeringGoalCurrentMonth;
                    }
                    if(currentTimeSheetYear == currentArrayYear) {
                        if(currentTimeSheetMonth == currentArrayMonth) {
                            if(date == currentArrayDate) {
                                let timeSheetId = timeSheetItem.timesheet_id
                                _this.currentTimeData.timeSheetId = timeSheetId;
                               
                                _this.fetchTimeSheetRealtedData(timeSheetId);

                                _this.workDayList.filter(function (workList, workIndex) {
                                  if(workList[0] == timeSheetItem.day_volunteered) {
                                    _this.defaultWorkday = _this.langauageData.label[workList[1]];
                                   
                                  }
                                });
                            }
                        }

                    }
                  
                });
            }
            if(timeSheetType == 'time') {
                this.$refs.timeModal.$refs.timeHoursModal.show();    
            } else {
                this.$refs.goalModal.$refs.goalActionModal.show();     
            }
           
        },
        fetchTimeSheetRealtedData(timeSheetId) {
            var _this = this
             fetchTimeSheetDocuments(timeSheetId).then( response => {
                                    if(response) {

                                        _this.currentTimeData.dateVolunteered = response.date_volunteered
                                        _this.currentTimeData.workDay = response.day_volunteered
                                        _this.currentTimeData.notes = response.notes
                                        _this.currentTimeData.documents = response.timesheet_document
                                        if(response.hours) {
                                            _this.currentTimeData.hours = ("0" +response.hours.toString()).slice(-2)
                                            _this.defaultHours = ("0" +response.hours.toString()).slice(-2)
                                        } else {
                                            _this.currentTimeData.hours = '00'
                                            _this.defaultHours = '00'
                                        }
                                        if(response.minutes) {
                                            _this.currentTimeData.minutes = ("0" +response.minutes.toString()).slice(-2)
                                            _this.defaultMinutes = ("0" +response.minutes.toString()).slice(-2)
                                        } else {
                                            _this.currentTimeData.minutes = '00'
                                            _this.defaultMinutes = '00'
                                        }
                                       
                                        _this.currentTimeData.action = response.action
                                    }
                                });
        },
        getTimeSheetHourClass(date,timeSheetArray,timeSheetType) {
            let _this = this
            let returnData = [];
            this.isCurrentDate = false;
            let missionEndDate = timeSheetArray.end_date         
            let disabledPastDates = moment(timeSheetArray.start_date).format("YYYY-MM-DD")                                  
            let disablefuterDate = moment(this.futureDates).format("YYYY-MM-DD");
            let endDate = moment(missionEndDate).format("YYYY-MM-DD");
            let disableEndDate = '';

            if (endDate > disablefuterDate) {
                disableEndDate = disablefuterDate
            } else {
                disableEndDate = endDate
            }
          
            let currentDate = moment(_this.volunteeringHoursCurrentYear+'-'+_this.volunteeringHoursCurrentMonth+'-'+date).format("YYYY-MM-DD");
                
           
            let timeArray = timeSheetArray.timesheet;
            let currentTimeSheetYear = '';
            let currentTimeSheetMonth = '';
            if(timeSheetType == 'time') {
                currentTimeSheetYear = _this.volunteeringHoursCurrentYear;
                currentTimeSheetMonth = _this.volunteeringHoursCurrentMonth;
                      
                } else {
                currentTimeSheetYear = _this.volunteeringGoalCurrentYear;
                currentTimeSheetMonth = _this.volunteeringGoalCurrentMonth;
                        
            }
                timeArray.filter(function (timeSheetItem, timeSheetIndex) {
                    let currentArrayDate = timeSheetItem.date
                    let currentArrayYear = timeSheetItem.year
                    let currentArrayMonth = timeSheetItem.month             
                   
                    if(currentTimeSheetYear == currentArrayYear) {
                        if(currentTimeSheetMonth == currentArrayMonth) {
                            if(date == currentArrayDate) {
                                if(timeSheetItem.timesheet_status.status == "APPROVED" || timeSheetItem.timesheet_status.status == "AUTOMATICALLY_APPROVED") {
                                    returnData.push("approved")
                                } 
                                else if(timeSheetItem.timesheet_status.status == "DECLINED") {
                                    returnData.push("declined")
                                    // returnData = "declined"
                                }
                                else if(timeSheetItem.timesheet_status.status == "SUBMIT_FOR_APPROVAL") {
                                    returnData.push("approval")
                                    // returnData = "submit_for_approval"
                                }
                                else {
                                    returnData.push("default-time")
                                    // returnData = "default-time"
                                }
                            }

                        } 
                    }
                    // if((_this.todaysDate == date) && (currentTimeSheetYear ==  _this.currentYear) && (currentTimeSheetMonth == _this.currentMonth)) {
                    //             returnData = "currentdate-col";
                    // }
                });
                
                if((_this.todaysDate == date) && (currentTimeSheetYear ==  _this.currentYear) && (currentTimeSheetMonth == _this.currentMonth)) {
                                
                                returnData.push("currentdate-col");
                                
                }
                if (currentDate < disabledPastDates) {
                    returnData.push("disabled")
                    // returnData ="disabled"
                } 

                if(currentDate > disableEndDate) {
                    returnData.push("disabled")
                    //  returnData = "disabled"
                }
               
            return returnData;
        },

        changeVolunteeringHours(data) {
            this.enableSubmitTimeTimeSheet = true;
            var _this =this;
            this.tableLoaderActive = true
            this.volunteeringHoursCurrentMonth = data.month
            this.volunteeringHoursCurrentYear = data.year
            data.weekdays.shift();
            this.volunteeringHoursWeeks = data.weekdays;
            setTimeout(function(){
                _this.tableLoaderActive = false
            })
        },

        changeVolunteeringGoals(data) {
            this.enableSubmitGoalTimeSheet = true;
            var _this =this;
            this.goalTableLoaderActive = true
            this.volunteeringGoalCurrentMonth = data.month
            this.volunteeringGoalCurrentYear = data.year
            data.weekdays.shift();
            this.volunteeringGoalWeeks = data.weekdays
            setTimeout(function(){
                _this.goalTableLoaderActive = false
            })
        },

        updateWorkday(value) {
          this.defaultWorkday = value.selectedVal;
        },

        updateDefaultValue(value) {
            if(value.fieldId == "workday") {
                this.defaultWorkday = value.selectedVal;
            }
            if(value.fieldId == "hours") {
                this.defaultHours = value.selectedVal
            }
            if(value.fieldId == "minutes") {
                this.defaultMinutes = value.selectedVal
            }
        },

        updateMission(value) {
            this.defaultMission = value;
        },

        async getVolunteerHoursData() {
            this.tableLoaderActive = true
            var _this =this;
            await volunteerTimesheetHours().then( response => { 
                if(response) {
                    this.timeMissionData = response['TIME']
                    this.goalMissionData = response['GOAL']
                    if(response['TIME'].length > 0 ||  this.goalMissionData.length > 0) {
                        this.isAllVisible = true
                    } else {
                        this.isAllVisible = false
                    }
                }
                _this.tableLoaderActive = false 
           })
        },

        getTime(date,timeArray,timeSheetType) {
            let _this = this
            let returnData = '';
            var dates = date+1;
            timeArray.filter(function (timeSheetItem, timeSheetIndex) {

                let currentArrayDate = timeSheetItem.date
                let currentArrayYear = timeSheetItem.year
                let currentArrayMonth = timeSheetItem.month
                if(timeSheetType == "time") {
                    if(_this.volunteeringHoursCurrentYear == currentArrayYear) {
                        if(_this.volunteeringHoursCurrentMonth == currentArrayMonth) {
                            if(dates == currentArrayDate) {
                                    returnData = timeSheetItem.time                  
                            }
                        }
                    }
                } else {
                    if(_this.volunteeringGoalCurrentYear == currentArrayYear) {
                        if(_this.volunteeringGoalCurrentMonth == currentArrayMonth) {
                            if(dates == currentArrayDate) {
                                returnData = timeSheetItem.action    
                            }
                        }
                    }         
                    
                }
              
            });
            return returnData
        },
        
        getRawHourTotal(timeArray,timeSheetType) {
            let _this = this
            var time1 = "00:00";
            var hour=0
            var minute=0
            var action = 0
            if(timeArray) {
                timeArray.filter(function (timeSheetItem, timeSheetIndex) {
                    let currentArrayDate = timeSheetItem.date
                    let currentArrayYear = timeSheetItem.year
                    let currentArrayMonth = timeSheetItem.month
                    var time2 = timeSheetItem.time;
                    if(timeSheetType == "time") {
                        if(_this.volunteeringHoursCurrentYear == currentArrayYear) {
                            if(_this.volunteeringHoursCurrentMonth == currentArrayMonth) {    
                                    var splitTime1= time1.split(':');
                                    var splitTime2= time2.split(':');

                                    hour = parseInt(splitTime1[0])+parseInt(splitTime2[0]);
                                    minute = parseInt(splitTime1[1])+parseInt(splitTime2[1]);
                                    hour = hour + minute/60;
                                    minute = minute%60;
                                    time1= ("0" + Math.floor(hour)).slice(-2)+':'+("0" + Math.floor(minute)).slice(-2);
                            }
                        }
                    }  else {
                        if(_this.volunteeringGoalCurrentYear == currentArrayYear) {
                            if(_this.volunteeringGoalCurrentMonth == currentArrayMonth) {    
                                action = action+ timeSheetItem.action
                            }
                        }
                           
                    }
                });
            }
           if(timeSheetType == "time") {
                    if(time1 != "00:00") {
                        return time1;
                    }
                    
            } 
            if(timeSheetType == "goal") {
                if(action != 0) {
                    return action;
                }
                
            }  
        },

        getColumnHourTotal(day,timeSheetType) {

            let _this = this
            var time1 = "00:00";
            var hour=0;
            var minute=0;
            var action = 0;
            let timeArray = []
            if(timeSheetType == "time") {
                timeArray = this.timeMissionData;
            } else {
                timeArray = this.goalMissionData;
            }
            if(timeArray.length > 0) {
                timeArray.filter(function (timeSheetItem, timeSheetIndex) {
                    let timeEntryData = timeSheetItem.timesheet;
                    timeEntryData.filter(function (timeEntry, timeEntryIndex) {
                       
                            let currentArrayDate = timeEntry.date
                            let currentArrayYear = timeEntry.year
                            let currentArrayMonth = timeEntry.month
                            var time2 = timeEntry.time;
                            if(timeSheetType == "time") {
                                if(_this.volunteeringHoursCurrentYear == currentArrayYear) {
                                    if(_this.volunteeringHoursCurrentMonth == currentArrayMonth) {
                                        if(day == currentArrayDate) {
                                            
                                                var splitTime1= time1.split(':');
                                                var splitTime2= time2.split(':');
                                                hour = parseInt(splitTime1[0])+parseInt(splitTime2[0]);
                                                minute = parseInt(splitTime1[1])+parseInt(splitTime2[1]);
                                                hour = hour + minute/60;
                                                minute = minute%60;

                                                time1= ("0" + Math.floor(hour)).slice(-2)+':'+("0" + Math.floor(minute)).slice(-2);
                                           
                                        }
                                    }
                                } 
                            } else {
                                if(_this.volunteeringGoalCurrentYear == currentArrayYear) {
                                    if(_this.volunteeringGoalCurrentMonth == currentArrayMonth) {
                                        if(day == currentArrayDate) {
                                            action = action+ timeEntry.action
                                        }
                                    }
                                } 
                            }
                        
                    });
                    
                });
            }
            if(timeSheetType == "time") {
                    if(time1 != "00:00") {

                        return time1;
                    }
                    
            } 
            if(timeSheetType == "goal") {
                if(action != 0) {
                    return action;
                }
                
            }          
        },
        
        getTotalHours(timeSheetType) {
            let _this = this
            var time1 = "00:00";
            var timeApproved = "00:00";
            var action = 0;
            var actionApproved = 0;
            var hour=0;
            var minute=0;
            var hourApproved=0;
            var minuteApproved=0;

            let timeArray = []
            if(timeSheetType == "time") {
                timeArray = this.timeMissionData;
            } else {
                timeArray = this.goalMissionData;
            }
                timeArray.filter(function (timeSheetItem, timeSheetIndex) {
                    let timeEntryData = timeSheetItem.timesheet;
                    timeEntryData.filter(function (timeEntry, timeEntryIndex) {
                        
                            let currentArrayDate = timeEntry.date
                            let currentArrayYear = timeEntry.year
                            let currentArrayMonth = timeEntry.month
                            var time2 = timeEntry.time;
                            if(timeSheetType == "time") {
                                if(_this.volunteeringHoursCurrentYear == currentArrayYear) {
                                    if(_this.volunteeringHoursCurrentMonth == currentArrayMonth) {
                                        var splitTime1= time1.split(':');
                                        var splitTime2= time2.split(':');
                                        hour = parseInt(splitTime1[0])+parseInt(splitTime2[0]);
                                        minute = parseInt(splitTime1[1])+parseInt(splitTime2[1]);
                                        hour = hour + minute/60;
                                        minute = minute%60;
                                        time1= ("0" + Math.floor(hour)).slice(-2)+':'+("0" + Math.floor(minute)).slice(-2);
                                        if(timeEntry.timesheet_status.status != "APPROVED" && timeEntry.timesheet_status.status != "AUTOMATICALLY_APPROVED") {
                                             //for submit request
                                            var splitTimeApproved= timeApproved.split(':');
                                            hourApproved = parseInt(splitTimeApproved[0])+parseInt(splitTime2[0]);
                                            minuteApproved = parseInt(splitTimeApproved[1])+parseInt(splitTime2[1]);
                                            hourApproved = hourApproved + minuteApproved/60;
                                            minuteApproved = minuteApproved%60;
                                            timeApproved= ("0" + Math.floor(hourApproved)).slice(-2)+':'+("0" + Math.floor(minuteApproved)).slice(-2);
                                        }
                                       
                                    }
                                }
                            }   else {
                                if(_this.volunteeringGoalCurrentYear == currentArrayYear) {
                                    if(_this.volunteeringGoalCurrentMonth == currentArrayMonth) {
                                        
                                       action = action+ timeEntry.action
                                      if(timeEntry.timesheet_status.status != "APPROVED" && timeEntry.timesheet_status.status != "AUTOMATICALLY_APPROVED") {
                                          actionApproved = actionApproved +  timeEntry.action
                                      }
                                    }
                                }
                             }
                    });
                    
                });
            
                if(timeSheetType == "time") {
                    if(time1 != "00:00") {
                        if(timeApproved != "00:00") {
                            this.enableSubmitTimeTimeSheet = false;
                        } 
                        return time1;
                    }    
                } 
                if(timeSheetType == "goal") {
                    if(action != 0) {
                        if(actionApproved != 0) {
                            this.enableSubmitGoalTimeSheet = false;
                        } 
                        return action;
                    }
                   
                }    
            } ,

        
        updateMinutes(value) {
            this.defaultMinutes = value.selectedVal
        },

        hideModal(){
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
            this.defaultHours = this.langauageData.placeholder.spent_hours
            this.defaultMinutes = this.langauageData.placeholder.spent_minutes
            this.defaultWorkday = this.langauageData.placeholder.workday 
           
        },

        submitVolunteerTimeSheet(timeSheetType) {
                var _this = this;
                let timeSheetId = {
                    'timesheet_entries' : []
                }
                let timeSheetEntry = []
                let timeArray = [];
                let currentYear = ''
                let currentMonth = ''
                if(timeSheetType == "time") {
                    timeArray = this.timeMissionData;
                    currentYear = _this.volunteeringHoursCurrentYear
                    currentMonth = _this.volunteeringHoursCurrentMonth
                } else {
                    timeArray = this.goalMissionData;
                    currentYear = _this.volunteeringGoalCurrentYear
                    currentMonth = _this.volunteeringGoalCurrentMonth
                }
                if(timeArray) {
                    timeArray.filter(function (timeMission, timeMissionIndex) {
                        let timeSheetArray = timeMission.timesheet;
                        if(timeSheetArray) {
                            timeSheetArray.filter(function (timeSheet, timeSheetIndex) {
                                let currentArrayYear = timeSheet.year
                                let currentArrayMonth = timeSheet.month
                                if(currentYear == currentArrayYear) {
                                    if(currentMonth == currentArrayMonth) {
                                        if(timeSheet.timesheet_status.status != "APPROVED" && timeSheet.timesheet_status.status != "AUTOMATICALLY_APPROVED"){
                                                timeSheetId.timesheet_entries.push({'timesheet_id':timeSheet.timesheet_id})  
                                        }
                                    }
                                }
                            });
                        }
                    });
                }
                submitVolunteerHourTimeSheet(timeSheetId).then(response => {
                    if(response.error == true){
                        this.makeToast("danger",response.message);
                    } else {
                        this.getVolunteerHoursData()
                        this.makeToast("success",response.message);
                    }  
                })
                 
        },

        makeToast(variant = null,message) {
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
            var _this = this;
            let currentData = [];
            timeRequest(currentPage).then( response => {
                if(response.data) {
                    let data = response.data;
                    let mission = this.langauageData.label.mission;
                    let time = this.langauageData.label.time;
                    let hours = this.langauageData.label.hours;
                    let organisation = this.langauageData.label.organisation;

                    if(response.pagination) {
                        this.hourRequestTotalRow = response.pagination.total;
                        this.hourRequestCurrentPage = response.pagination.current_page
                        this.hourRequestPerPage = response.pagination.per_page;
                        this.hourRequestNextUrl = response.pagination.next_url
                    }
                    
                    data.filter(function(item,index){
                        currentData.push(
                            {
                                [mission] : item.title,
                                [time] : item.time,
                                [hours] : item.hours,
                                [organisation] : item.organisation_name,
                            }
                        )
                        _this.timesheetRequestItems = currentData;
                    })
                }
            })   
        },
        getGoalRequest(currentPage) {
            this.getGoalRequestData(currentPage);

        },
        getGoalRequestData(currentPage) {
            var _this = this;
            setTimeout(function() {
                if(store.state.missionId != '' && store.state.missionId != null) {
                    let missionId = store.state.missionId;
                    let timeSheetType = store.state.missionType.toLowerCase()
                    let timeArray = []
                    let timeSheetArray = []
                    let date =  moment().format('D')
                    if(timeSheetType == "time") {
                        timeArray = _this.timeMissionData
                    } else {
                        timeArray =  _this.goalMissionData
                    }
                    timeArray.filter(function(timeArray,timeIndex){
                        if(timeArray.mission_id == missionId) {
                            timeSheetArray = timeArray;
                        }
                    })
                    setTimeout(function(){
                        if(timeSheetArray) { 
                            _this.getRelatedTimeData(date,timeSheetArray,timeSheetType)
                        }
                    },500)   
                } 
            },200)
           
            let currentData = [];

            goalRequest(currentPage).then( response => {
                if(response.data) {
                    let data = response.data;
                    let mission = this.langauageData.label.mission;
                    let action = this.langauageData.label.actions;
                    let organisation = this.langauageData.label.organisation;
                     if(response.pagination) {
                        this.goalRequestTotalRow = response.pagination.total;
                        this.goalRequestCurrentPage = response.pagination.current_page
                        this.goalRequestPerPage = response.pagination.per_page;
                        this.goalRequestNextUrl = response.pagination.next_url
                    }
                    
                    data.filter(function(item,index) {
                        currentData.push(
                            {
                                [mission] : item.title,
                                [action] : item.action,
                                [organisation] : item.organisation_name,
                            }
                        )
                        _this.goalRequestItems = currentData
                    }) 
                }
            })   
        }
    },
    created() {
        var globalThis = this;
        this.langauageData = JSON.parse(store.state.languageLabel);
        this.defaultWorkday = this.langauageData.placeholder.workday 
        this.defaultHours = this.langauageData.placeholder.spent_hours
        this.defaultMinutes = this.langauageData.placeholder.spent_minutes
        this.timeRequestLabel = this.langauageData.label.hours_requests 
        this.goalRequestLabel = this.langauageData.label.goals_requests 
        this.getVolunteerHoursData();
        this.isShownComponent = true;
        setTimeout(function(){
            globalThis.getTimeRequestData(globalThis.hourRequestCurrentPage);
        },80)
        setTimeout(function() {
            globalThis.getGoalRequestData(globalThis.goalRequestCurrentPage);
        },100)
        
        let timeRequestFieldArray = [
            this.langauageData.label.mission,
            this.langauageData.label.time,
            this.langauageData.label.hours,
            this.langauageData.label.organisation,
        ]

        let goalRequestFieldArray = [
            this.langauageData.label.mission,
            this.langauageData.label.actions,
            this.langauageData.label.organisation,
        ]

        goalRequestFieldArray.filter(function(data,index){
            globalThis.goalRequestFields.push({
                "key" : data
            })
        });

        timeRequestFieldArray.filter(function(data,index){
            globalThis.timesheetRequestFields.push({
                "key" : data
            })
        });
        
    }
};
</script>