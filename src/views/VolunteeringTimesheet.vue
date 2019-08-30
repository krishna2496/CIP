<template>
    <div class="dashboard-timesheet inner-pages">
        <header>
        <ThePrimaryHeader></ThePrimaryHeader>
        </header>
        <main>
            <DashboardBreadcrumb />
        <div class="dashboard-tab-content">
            <b-container>
                <div class="heading-section">
                    <h1>{{langauageData.label.volunteering_timesheet}}</h1>
                </div>
                <div class="dashboard-table">
                    <div class="table-outer">
                        <div class="table-inner">
                            {{volunteeringHoursWeeks}}
                            <h3>{{langauageData.label.volunteering_hours}}</h3>
                            <VolunteeringTimesheetTableHeader
                                @updateCall="changeVolunteeringHours"
                            />
                           
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
                                        <b-td v-for="(item,key) in volunteeringHoursWeeks">
                                          {{getTime(key,timeItem.timesheet)}} 
                                        </b-td> 
                                        <b-td class="total-col">7:00</b-td>
                                    </b-tr>
                                    <b-tr class="total-row">
                                      <b-td class="mission-col">Total:</b-td>
                                      <b-td>5</b-td>
                                      
                                      <b-td class="total-col">13</b-td>
                                    </b-tr>
                                </b-tbody>

                            </b-table-simple>
                        </div>
                        <div class="btn-block">
                            <b-button class="btn-bordersecondary ml-auto" title="Submit">{{langauageData.label.submit}}</b-button>
                        </div>
                    </div>
                    <ul class="meta-data-list">
                        <li class="approve-indication">{{langauageData.label.approved}}</li>
                        <li class="decline-indication">{{langauageData.label.declined}}</li>
                    </ul>
                    {{goalMissionData}}
                    <div class="table-outer timesheet-table-outer">
                        <div class="table-inner">
                            <h3>{{langauageData.label.volunteering_goals}}</h3>
                            <VolunteeringTimesheetTableHeader
                                @updateCall="changeVolunteeringGoals"
                            />
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
                      <b-tbody>
                        <b-tr>
                          <b-td class="mission-col">Help Old People</b-td>
                          <b-td>5</b-td>
                          <b-td></b-td>
                          <b-td class="approved">2</b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td class="total-col">7</b-td>
                        </b-tr>
                        <b-tr>
                          <b-td class="mission-col">Help Young Kids</b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td class="declined">6</b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td class="total-col">6</b-td>
                        </b-tr>
                        <b-tr class="total-row">
                          <b-td class="mission-col">Total:</b-td>
                          <b-td>5</b-td>
                          <b-td></b-td>
                          <b-td>2</b-td>
                          <b-td></b-td>
                          <b-td>6</b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td></b-td>
                          <b-td class="total-col">13</b-td>
                        </b-tr>
                      </b-tbody>
                </b-table-simple>
                        </div>
                    <div class="btn-block">
                        <b-button class="btn-bordersecondary ml-auto">{{langauageData.label.submit}}</b-button>
                    </div>
                    </div>
                    <div class="table-outer timesheet-table-outer">
                        <div class="table-inner">
                            <h3>{{langauageData.label.hours_requests}}</h3>
                            <b-table
                            :items="timesheetRequestItems"
                            responsive
                            :fields="timesheetRequestFields"
                            class="volunteery-table"
                            ></b-table>
                        </div>
                        <div class="btn-block">
                            <b-button class="btn-bordersecondary ml-auto" title="Export">{{langauageData.label.export}}</b-button>
                        </div>  
                    </div>
                    <div class="pagination-block">
                        <b-pagination
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        align="right"
                        aria-controls=""
                        ></b-pagination>
                    </div>
                    <div class="table-outer timesheet-table-outer">
                        <div class="table-inner">
                            <h3>{{langauageData.label.goals_requests}}</h3>
                            <b-table
                            :items="timesheetRequestItems"
                            responsive
                            :fields="timesheetRequestFields"
                            class="volunteery-table"
                            ></b-table>
                        </div>
                        <div class="btn-block">
                            <b-button class="btn-bordersecondary ml-auto" title="Export">{{langauageData.label.export}}</b-button>
                        </div>
                    </div>
                    <div class="pagination-block">
                        <b-pagination
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        align="right"
                        aria-controls=""
                        ></b-pagination>
                    </div>
                </div>

                <b-modal ref="timeHoursModal" :modal-class="'time-hours-modal table-modal'" hide-footer>
                    <template slot="modal-header" slot-scope="{ close }">
                        <i class="close" @click="close()" v-b-tooltip.hover :title="langauageData.label.close"></i>
                        <h5 class="modal-title">{{langauageData.label.hour_entry_modal_title}}</h5>
                    </template>
                    <form action class="form-wrap">
                        <!-- <b-form-group>
                        <b-row>
                        <b-col cols="12">
                        <b-form-group>
                        <label for>Select mission</label>
                        <AppCustomDropdown
                        :optionList="missionList"
                        :default_text="defaultMission"
                        @updateCall="updateMission"
                        />
                        </b-form-group>
                        </b-col>
                        </b-row>
                        </b-form-group> -->
                        <b-form-group>
                            <b-row>
                                <b-col cols="6" class="time-col-left">
                                    <b-form-group>
                                        <label for>{{langauageData.label.hours}}</label>
                                        <b-form-input id type="text" placeholder="Enter spent hours"></b-form-input>
                                    </b-form-group>
                                </b-col>
                                <b-col cols="6" class="time-col-right">
                                    <b-form-group>
                                        <label for>{{langauageData.label.minutes}}</label>
                                        <b-form-input id type="text"></b-form-input>
                                    </b-form-group>
                                </b-col>
                            </b-row>
                        </b-form-group>
                        <b-form-group>
                            <b-row>
                                <b-col sm="6" class="date-col">
                                    <b-form-group>
                                        <label for>{{langauageData.label.date_volunteered}}</label>
                                            <date-picker
                                                v-model="time1"
                                                valuetype="format"
                                                :first-day-of-week="1"
                                                :lang="lang"
                                            ></date-picker>
                                    </b-form-group>
                                </b-col>
                                <b-col sm="6" class="date-col">
                                    <b-form-group>
                                        <label for>{{langauageData.label.workday}}</label>
                                        <b-row>
                                            <b-col sm="6">
                                                <AppCustomDropdown
                                                    :optionList="workDayList"
                                                    :defaultText="defaultWorkday"
                                                    @updateCall="updateWorkday"
                                                    translationEnable= "false"
                                                />
                                            </b-col>
                                        </b-row>
                                    </b-form-group>
                                </b-col>
                            </b-row>
                        </b-form-group>

                        <b-form-group>
                            <label for>{{langauageData.label.notes}}:</label>
                            <b-form-textarea id size="lg" no-resize rows="5"></b-form-textarea>
                        </b-form-group>
                        <b-form-group>
                            <label for>{{langauageData.label.file_upload}}</label>
                            <div class="file-upload-wrap">
                             <file-upload
                                class="btn"
                                post-action="/upload/post"
                                extensions="gif,jpg,jpeg,png,webp"
                                accept="image/png,image/gif,image/jpeg,image/webp"
                                :multiple="true"
                                :drop="true"
                                :drop-directory="true"
                                :size="1024 * 1024 * 10"
                                v-model="files"
                                ref="upload">
                               Browse
                                </file-upload>
                                <div class="uploaded-file-details" v-for="(file, index) in files" :key="file.id">
                                    <p class="filename">{{file.name}}</p>
                                    <span>{{file.size}}</span>
                                    <div class="status">
                                        <span v-if="file.error">{{file.error}}</span>
                                        <span v-else-if="file.success">File uploaded successfully.</span>
                                    </div>
                                    <a class="remove-item" href="#" @click.prevent="$refs.upload.remove(file)">
                                    <img :src="$store.state.imagePath+'/assets/images/delete-ic.svg'" alt="delete-ic"/>
                                    </a>
                                </div>
                            </div>
                        </b-form-group>
                    </form>
                    <div class="btn-wrap">
                        <b-button
                            class="btn-borderprimary"
                            @click="$refs.timeHoursModal.hide()"
                            title="Cancel"
                        >{{langauageData.label.cancel}}</b-button>
                        <b-button class="btn-bordersecondary" @click="save()" title="Submit">{{langauageData.label.submit}}</b-button>
                    </div>
                </b-modal>

                    <b-modal ref="goalModal" :modal-class="'goal-modal table-modal'" hide-footer>

                    <template slot="modal-header" slot-scope="{ close }">
                        <i class="close" @click="close()" v-b-tooltip.hover :title="langauageData.label.close"></i>
                        <h5 class="modal-title"></h5>
                    </template>
                <form action class="form-wrap">
                    <!--  <b-form-group>
                    <b-row>
                    <b-col cols="12">
                    <b-form-group>
                    <label for>Select mission</label>
                    <AppCustomDropdown
                    :optionList="missionList"
                    :default_text="defaultMission"
                    @updateCall="updateMission"
                    />
                    </b-form-group>
                    </b-col>
                    </b-row>
                    </b-form-group> -->
                    <b-form-group>
                        <b-row>
                        <b-col cols="12">
                            <b-form-group>
                                <label for>{{langauageData.label.actions}}</label>
                                <b-form-input id type="text" placeholder="Enter actions"></b-form-input>
                            </b-form-group>
                        </b-col>
                        </b-row>
                    </b-form-group>
                    <b-form-group>
                    <b-row>
                        <b-col sm="6" class="date-col">
                            <b-form-group>
                                <label for>{{langauageData.label.date_volunteered}}</label>
                                <date-picker
                                    v-model="time1"
                                    valuetype="format"
                                    :first-day-of-week="1"
                                    :lang="lang"
                            ></date-picker>
                            </b-form-group>
                        </b-col>
                        <b-col sm="6" class="date-col">
                            <b-form-group>
                                <label for>{{langauageData.label.workday}}</label>
                                <b-row>
                                    <b-col sm="6">
                                        <AppCustomDropdown
                                            :optionList="workDayList"
                                            :defaultText="defaultWorkday"
                                            @updateCall="updateWorkday"
                                            translationEnable= "false"
                                        />
                                    </b-col>
                                </b-row>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    </b-form-group>
                    <b-form-group>
                        <label for>{{langauageData.label.notes}}</label>
                        <b-form-textarea id size="lg" no-resize rows="5"></b-form-textarea>
                    </b-form-group>
                    <b-form-group>
                        <label for>{{langauageData.label.file_upload}}</label>
                        <div class="file-upload-wrap">
                             <file-upload
                                class="btn"
                                post-action="/upload/post"
                                extensions="gif,jpg,jpeg,png,webp"
                                accept="image/png,image/gif,image/jpeg,image/webp"
                                :multiple="true"
                                :drop="true"
                                :drop-directory="true"
                                :size="1024 * 1024 * 10"
                                v-model="files"
                                ref="upload2">
                               Browse
                                </file-upload>
                                <div class="uploaded-file-details" v-for="(file, index) in files" :key="file.id">
                                    <p class="filename">{{file.name}}</p>
                                    <span>{{file.size}}</span>
                                    <div class="status">
                                        <span v-if="file.error">{{file.error}}</span>
                                        <span v-else-if="file.success">File uploaded successfully.</span>
                                    </div>
                                    <a class="remove-item" href="#" @click.prevent="$refs.upload.remove(file)">
                                    <img :src="$store.state.imagePath+'/assets/images/delete-ic.svg'" alt="delete-ic"/>
                                    </a>
                                </div>
                            </div>
                    </b-form-group>
                </form>
                <div class="btn-wrap">
                    <b-button
                        class="btn-borderprimary"
                        @click="$refs.goalModal.hide()"
                    >{{langauageData.label.cancel}}</b-button>
                    <b-button class="btn-bordersecondary" @click="save()">{{langauageData.label.submit}}</b-button>
                </div>
                </b-modal>
                </b-container>
            </div>
        </main>
        <footer>
            <TheSecondaryFooter></TheSecondaryFooter>
        </footer>
    </div>
</template>

<script>
import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
import AppCustomDropdown from "../components/CustomFieldDropdown";
import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
import VolunteeringTimesheetTableHeader from "../components/VolunteeringTimesheetTableHeader"
import SimpleBar from "simplebar";
import constants from '../constant';
import axios from "axios";
import store from '../store';
import DatePicker from "vue2-datepicker";
import FileUpload from 'vue-upload-component';
import {volunteerTimesheetHours} from '../services/service';

export default {
    components: {
        ThePrimaryHeader,
        TheSecondaryFooter,
        AppCustomDropdown,
        SimpleBar,
        DashboardBreadcrumb,
        VolunteeringTimesheetTableHeader,
        DatePicker,
        FileUpload
    },

    name: "dashboardtimesheet",

    data() {
        return {
            files: [],
            time1: "",
            value2: "",
            lang: {
            days: [" Sun ", " Mon ", " Tue ", " Wed ", " You ", " Fri ", " Sat "],
            months: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec"
            ],
            pickers: [
            "next 7 days",
            "next 30 days",
            "previous 7 days",
            "previous 30 days"
            ],
            placeholder: {
            date: "mm/dd/yy",
            dateRange: "Select Date Range"
            }
            },
            missionList: [
            "Help Old People",
            "Help Young Kids",
            "Plant house",
            "The place"
            ],
            defaultMission: "Help Old People",
            langauageData : [],
            timesheetRequestFields: [
        {
          key: "Mission",
          class: "mission-col"
        },
        {
          key: "Time"
        },
        {
          key: "Hours",
          class: "hours-col"
        },
        {
          key: "Organisation",
          class: "organisation-col"
        }
      ],
      timesheetRequestItems: [
        {
          Mission: "Help old people",
          Time: "1h30",
          Hours: 5.5,
          Organisation: "Red Cross"
        },
        {
          Mission: "Help young kids",
          Time: "0h20",
          Hours: 0.33,
          Organisation: "Red Cross"
        },
        {
          Mission: "Plant house",
          Time: "2h50",
          Hours: 2.83,
          Organisation: "Green House"
        },
        {
          Mission: "The place",
          Time: "0h15",
          Hours: 0.25,
          Organisation: "Blue Cross"
        }
      ],
      goalRequestFields: [
        {
          key: "Mission",
          class: "mission-col"
        },
        {
          key: "Action"
        },
        {
          key: "Organisation",
          class: "organisation-col"
        }
      ],
      goalRequestItems: [
        {
          Mission: "Help old people",
          Action: "100",
          Organisation: "Red Cross"
        },
        {
          Mission: "Help young kids",
          Action: "200",
          Organisation: "Red Cross"
        },
        {
          Mission: "Plant house",
          Action: "300",
          Organisation: "Green House"
        },
        {
          Mission: "The place",
          Action: "400",
          Organisation: "Blue Cross"
        }
      ],
            defaultWorkday: "",
            workDayList: [
            ["workday","workday"],
            ["weekend","weekend"],
            ["holiday","holiday"],
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
            goalMissionData : []
            };
        },
        methods: {
                changeVolunteeringHours(data) {
                    console.log(data);
                    this.volunteeringHoursCurrentMonth = data.month
                    this.volunteeringHoursCurrentYear = data.year
                    data.weekdays.shift();
                    this.volunteeringHoursWeeks = data.weekdays;
                },
                changeVolunteeringGoals(data) {
                    this.volunteeringGoalCurrentMonth = data.month
                    this.volunteeringGoalCurrentYear = data.year
                    data.weekdays.shift();
                    this.volunteeringGoalWeeks = data.weekdays
                },
                updateWorkday(value) {
                    this.defaultWorkday = value.selectedVal;
                },
                updateMission(value) {
                    this.defaultMission = value;
                },
                async getVolunteerHoursData() {
                   await volunteerTimesheetHours().then( response => {
                        if(response) {
                            this.timeMissionData = response['TIME']
                            this.goalMissionData = response['GOAL']
                        }
                   })
                },
                getTime(date,timeArray) {
                    let _this = this
                    timeArray.filter(function (timeSheetItem, timeSheetIndex) {
                        let currentArrayDate = timeSheetItem.date
                        let currentArrayYear = timeSheetItem.year
                        let currentArrayMonth = timeSheetItem.month

                       
                        if(_this.volunteeringHoursCurrentYear == currentArrayYear) {
                            if(_this.volunteeringHoursCurrentMonth == currentArrayMonth) {
                                if(date == currentArrayDate) {
                                    console.log(timeSheetItem.time)
                                }
                            }
                        }
                        // console.log(currentArrayDate+','+currentArrayYear+','+currentArrayMonth);
                    });
                   
                    var dates = date+1;
                    return dates
                }
            },
            created() {
                var globalThis = this;
                this.langauageData = JSON.parse(store.state.languageLabel);
                this.defaultWorkday = this.langauageData.label.workday 
                this.getVolunteerHoursData();
                setTimeout(() => {
                    var hourTableCell = document.querySelectorAll(
                        ".timesheethours-table td:not(.mission-col):not(.approved):not(.total-col)"
                    );

                    var goalTableCell = document.querySelectorAll(
                        ".timesheetgoals-table td:not(.mission-col):not(.approved):not(.total-col)"
                    );
                    hourTableCell.forEach(function(currentEvent) {
                        currentEvent.addEventListener("click", function() {
                            globalThis.$refs.timeHoursModal.show();
                        });
                    });
                    goalTableCell.forEach(function(currentEvent) {
                        currentEvent.addEventListener("click", function() {
                            globalThis.$refs.goalModal.show();
                        });
                    });
                });
            }
};
</script>