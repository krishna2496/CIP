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
                            <h3>{{langauageData.label.volunteering_hours}}</h3>
                            <VolunteeringTimesheetTableHeader
                                @updateCall="changeVolunteeringHours"
                            />
                            <b-table
                                :items="timeSheetHoursItems"
                                responsive
                                bordered
                                :fields="timesheetHoursFields"
                                :tbody-tr-class="timesheetTotal"
                                class="timesheet-table timesheethours-table"
                            >
                            </b-table>
                        </div>
                        <div class="btn-block">
                            <b-button class="btn-bordersecondary ml-auto" title="Submit">{{langauageData.label.submit}}</b-button>
                        </div>
                    </div>
                    <ul class="meta-data-list">
                        <li class="approve-indication">{{langauageData.label.approved}}</li>
                        <li class="decline-indication">{{langauageData.label.declined}}</li>
                    </ul>
                    <div class="table-outer timesheet-table-outer">
                        <div class="table-inner">
                            <h3>{{langauageData.label.volunteering_goals}}</h3>
                            <VolunteeringTimesheetTableHeader
                                @updateCall="changeVolunteeringGoals"
                            />
                            <b-table
                                :items="timesheetGoalsItems"
                                responsive
                                bordered
                                :fields="timesheetGoalsFields"
                                class="timesheet-table timesheetgoals-table"
                            >
                            </b-table>
                        </div>
                    <div class="btn-block">
                        <b-button class="btn-bordersecondary ml-auto">{{langauageData.label.submit}}</b-button>
                    </div>
                    </div>
                    <div class="table-outer timesheet-table-outer">
                        <div class="table-inner">
                            <h3>{{langauageData.label.hours_requests}}</h3>
                            <b-table
                            :items="timesheetResquetItems"
                            responsive
                            :fields="timesheetResquetFields"
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
                            :items="timesheetResquetItems"
                            responsive
                            :fields="timesheetResquetFields"
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
                            <b-form-input id type="text"></b-form-input>
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
                        <b-form-input id type="text"></b-form-input>
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

export default {
    components: {
        ThePrimaryHeader,
        TheSecondaryFooter,
        AppCustomDropdown,
        SimpleBar,
        DashboardBreadcrumb,
        VolunteeringTimesheetTableHeader,
        DatePicker
    },

    name: "dashboardtimesheet",

    data() {
        return {
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
            timesheetResquetFields: [
            {
            key: "Mission",
            class: "mission-col"
            },
            {
            key: "Time",
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
            timesheetResquetItems: [
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

            timesheetHoursFields: [
            {
            key: "Mission",
            class: "mission-col"
            },
            {
            key: "1",
            class: "approved"
            },
            {
            key: "2",
            },
            {
            key: "3",
            class: "currentdate-col declined",
            },
            {
            key: "4",
            },
            {
            key: "5",
            },
            {
            key: "6",
            },
            {
            key: "7",
            },

            {
            key: "Total",
            class: "total-col"
            },
            ],
            timeSheetHoursItems: [
            {
            Mission: "Help Old People 1",
            "1": "5:00",
            "2": "",
            "3": "2:00",
            "4": "",
            "5": "",
            "6": "",
            "7": "",
            Total: "7:00"
            },
            {
            Mission: "Help Young Kids 1",
            "11": "",
            "22": "",
            "33": "",
            "43": "",
            "55": "6:00",
            "66": "",
            "77": "",
            Total: "6:00",
            },
            {
            status: "total",
            Mission: "Total:",
            "1": "5:00",
            "2": "",
            "3": "2:00",
            "4": "",
            "5": "6:00",
            "6": "",
            "7": "",
            Total: "13:00"
            }
            ],


            timesheetGoalsFields: [
            {
            key: "Mission",
            class: "mission-col"
            },
            {
            key: "1",
            },
            {
            key: "2",
            },
            {
            key: "3",
            class: "currentdate-col declined",
            },
            {
            key: "4",
            },
            {
            key: "5",
            },
            {
            key: "6",
            },
            {
            key: "7",
            },
            {
            key: "8",
            },
            {
            key: "9",
            },
            {
            key: "10",
            },
            {
            key: "11",
            },
            {
            key: "12",
            },
            {
            key: "13",
            },
            {
            key: "14",
            },
            {
            key: "15",
            },
            {
            key: "16",
            },
            {
            key: "17",
            },
            {
            key: "18",
            },
            {
            key: "19",
            },
            {
            key: "20",
            },
            {
            key: "21",
            },
            {
            key: "22",
            },
            {
            key: "23",
            },
            {
            key: "24",
            },
            {
            key: "25",
            },
            {
            key: "26",
            },
            {
            key: "27",
            },
            {
            key: "28",
            },
            {
            key: "29",
            },
            {
            key: "30",
            },
            {
            key: "31",
            },
            {
            key: "Total",
            class: "total-col"
            },
            ],
            timesheetGoalsItems: [
            {
            Mission: "Help Old People",
            "1": "5:00",
            "2": "",
            "3": "2:00",
            "4": "",
            "5": "",
            "6": "",
            "7": "",
            "8": "",
            "9": "",
            "10": "",
            "11": "",
            "12": "",
            "13": "",
            "14": "",
            "15": "",
            "16": "",
            "17": "",
            "18": "",
            "19": "",
            "20": "",
            "21": "",
            "22": "",
            "23": "",
            "24": "",
            "25": "",
            "26": "",
            "27": "",
            "28": "",
            "29": "",
            "30": "",
            "31": "",
            Total: "7:00"
            },
            {
            Mission: "Help Young Kids",
            "11": "",
            "22": "",
            "33": "",
            "43": "",
            "55": "6:00",
            "66": "",
            "77": "",
            "8": "",
            "9": "",
            "10": "",
            "11": "",
            "12": "",
            "13": "",
            "14": "",
            "15": "",
            "16": "",
            "17": "",
            "18": "",
            "19": "",
            "20": "",
            "21": "",
            "22": "",
            "23": "",
            "24": "",
            "25": "",
            "26": "",
            "27": "",
            "28": "",
            "29": "",
            "30": "",
            "31": "",
            Total: "6:00",
            },
            {
            status: "total",
            Mission: "Total hours",
            "1": "5:00",
            "2": "",
            "3": "2:00",
            "4": "",
            "5": "6:00",
            "6": "",
            "7": "",
            "8": "",
            "9": "",
            "10": "",
            "11": "",
            "12": "",
            "13": "",
            "14": "",
            "15": "",
            "16": "",
            "17": "",
            "18": "",
            "19": "",
            "20": "",
            "21": "",
            "22": "",
            "23": "",
            "24": "",
            "25": "",
            "26": "",
            "27": "",
            "28": "",
            "29": "",
            "30": "",
            "31": "",
            Total: "13:00"
            }
            ],
            defaultWorkday: "",
            workDayList: [
            ["workday","workday"],
            ["weekend","weekend"],
            ["holiday","holiday"],
            ],
            default_month: "Mar",
            monthList: ["Jan", "Feb", "Mar", "April"],
            default_date: "27",
            dateList: ["1", "2", "4", "27"],
            default_year: "2018",
            yearList: ["2018", "2017", "2016", "2015"],
            rows: 25,
            perPage: 2,
            currentPage: 1,
            };
        },
        methods: {
            changeVolunteeringHours(data) {
                console.log(data);
            },
            changeVolunteeringGoals(data) {
            console.log(data);  
            },
            updateWorkday(value) {
                this.defaultWorkday = value.selectedVal;
            },
            updateMonth(value) {
                this.default_month = value;
            },
            updateDate(value) {
                this.default_date = value;
            },
            updateYear(value) {
                this.default_year = value;
            },
            timesheetTotal(item){
                if(!item) return
                    if(item.status === 'total') return 'total-row';
                },
            updateMission(value) {
                this.defaultMission = value;
            },
            },
            created() {
                var globalThis = this;
                this.langauageData = JSON.parse(store.state.languageLabel);
                this.defaultWorkday = this.langauageData.label.workday 
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