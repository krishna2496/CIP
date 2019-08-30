<template>
  <div class="dashboard-timesheet inner-pages">
    <header>
      <TopHeader></TopHeader>
    </header>
    <main>
      <DashboardBreadcrumb />
      <div class="dashboard-tab-content">
        <b-container>
          <div class="heading-section">
            <h1>Volunteering Timesheet</h1>
          </div>
          <div class="dashboard-table">
            <div class="table-outer">
              <div class="table-inner">
                <h3>Volunteering Hours</h3>
                <div class="tab-with-picker">
                  <h2>August 2019</h2>
                  <div class="inner-wrap">
                    <button class="add-entry"  @click="$refs.timeHoursModal.show()">
                        <img src="../assets/images/plus-ic-black.svg" alt="plus-ic"/>
                    </button>
                    <div class="picker-btn-wrap">
                    <button class="prev-btn picker-btn" title="Previous">
                        <img src="../assets/images/back-arrow-black.svg" alt="Back Arrow" />
                    </button>
                    <span>August</span>
                    <button class="next-btn picker-btn" title="Next">
                        <img src="../assets/images/next-arrow-black.svg" alt="Next Arrow" />
                    </button>
                    </div>
                    <div class="select-time-period">
                        <span>Day</span>
                        <span>Week</span>
                        <span class="current">Month</span>
                    </div>
                    <div class="datepicker-block">
                        <img src="../assets/images/datepicker-ic.svg" alt="datepicker-ic"/>
                    </div>
                  </div>
                </div>
                <b-table
                  :items="timesheetHoursItems"
                  responsive
                  bordered
                  :fields="timesheetHoursFields"
                  :tbody-tr-class="timesheetTotal"
                  class="timesheet-table timesheethours-table"
                >
                </b-table>
              </div>
              <div class="btn-block">
                <b-button class="btn-bordersecondary ml-auto" title="Submit">Submit</b-button>
              </div>
            </div>
            <ul class="meta-data-list">
                <li class="approve-indication">Approved</li>
                <li class="decline-indication">Declined</li>
            </ul>
            <div class="table-outer timesheet-table-outer">
              <div class="table-inner">
                <h3>Volunteering Goals</h3>
                <div class="tab-with-picker">
                    <h2>August 2019</h2>
                <div class="inner-wrap">
                    <button class="add-entry"  @click="$refs.timeHoursModal.show()">
                        <img src="../assets/images/plus-ic-black.svg" alt="plus-ic"/>
                    </button>
                    <div class="picker-btn-wrap">
                    <button class="prev-btn picker-btn" title="Previous">
                        <img src="../assets/images/back-arrow-black.svg" alt="Back Arrow" />
                    </button>
                    <span>August</span>
                    <button class="next-btn picker-btn" title="Next">
                        <img src="../assets/images/next-arrow-black.svg" alt="Next Arrow" />
                    </button>
                    </div>
                    <div class="select-time-period">
                        <span>Day</span>
                        <span>Week</span>
                        <span class="current">Month</span>
                    </div>
                    <div class="datepicker-block">
                        <img src="../assets/images/datepicker-ic.svg" alt="datepicker-ic"/>
                    </div>
                  </div>
                </div>
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
                <b-button class="btn-bordersecondary ml-auto" title="Submit">Submit</b-button>
              </div>
            </div>
            <div class="table-outer timesheet-table-outer">
              <div class="table-inner">
                <h3>Hours Requests</h3>
                <b-table
                  :items="timesheetResquetItems"
                  responsive
                  :fields="timesheetResquetFields"
                  class="volunteery-table"
                ></b-table>
              </div>
              <div class="btn-block">
                <b-button class="btn-bordersecondary ml-auto" title="Export">Export</b-button>
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
             <div class="table-outer">
              <div class="table-inner">
                <h3>Goals Requests</h3>
                <b-table
                  :items="timesheetResquetItems"
                  responsive
                  :fields="timesheetResquetFields"
                  class="volunteery-table"
                ></b-table>
              </div>
              <div class="btn-block">
                <b-button class="btn-bordersecondary ml-auto" title="Export">Export</b-button>
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
            <template slot="modal-title">&nbsp;</template>
            <!-- <p>Please input below the time you spent on this project</p> -->
            <form action class="form-wrap">
              <b-row>
                <b-col cols="6" class="time-col-left">
                  <b-form-group>
                    <label for>Hours</label>
                    <b-form-input id type="text"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="6" class="time-col-right">
                  <b-form-group>
                    <label for>Minutes</label>
                    <b-form-input id type="text"></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-form-group>
                <label for>Date Volunteered:</label>
                <b-row>
                  <!-- <b-col sm="4" class="date-col">
                    <CustomDropdown
                      :optionList="monthList"
                      :default_text="default_month"
                      @updateCall="updateMonth"
                    />
                  </b-col>
                  <b-col sm="4" class="date-col">
                    <CustomDropdown
                      :optionList="dateList"
                      :default_text="default_date"
                      @updateCall="updateDate"
                    />
                  </b-col>
                  <b-col sm="4" class="date-col">
                    <CustomDropdown
                      :optionList="yearList"
                      :default_text="default_year"
                      @updateCall="updateYear"
                    />
                  </b-col> -->
                  <b-col sm="12">
                    <b-form-group>
                    <b-form-input id type="date"></b-form-input>
                  </b-form-group>
                  </b-col>
                </b-row>
              </b-form-group>

              <b-form-group>
                <label for>Notes:</label>
                <b-form-textarea id size="lg" no-resize rows="5"></b-form-textarea>
              </b-form-group>
              <b-form-group>
                <label for>Workday</label>
                <b-row>
                 <b-col sm="6">
                <CustomDropdown
                  :optionList="workdayList"
                  :default_text="default_workday"
                  @updateCall="updateWorkday"
                />
                </b-col>
                </b-row>
              </b-form-group>
            </form>
            <div class="btn-wrap">
              <b-button
                class="btn-borderprimary"
                @click="$refs.timeHoursModal.hide()"
                title="Cancel"
              >Cancel</b-button>
              <b-button class="btn-bordersecondary" @click="save()" title="Submit">Submit</b-button>
            </div>
          </b-modal>
          <b-modal ref="goalModal" :modal-class="'goal-modal table-modal'" hide-footer>
            <template slot="modal-title">Goal</template>
            <form action class="form-wrap">
              <b-row>
                <b-col cols="6" class="time-col-left">
                  <b-form-group>
                    <label for>Action</label>
                    <b-form-input id type="text"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="6" class="time-col-right"></b-col>
              </b-row>
              <b-form-group>
                <label for>Date Volunteered:</label>
                <b-row>
                  <b-col sm="4" class="date-col">
                    <CustomDropdown
                      :optionList="monthList"
                      :default_text="default_month"
                      @updateCall="updateMonth"
                    />
                  </b-col>
                  <b-col sm="4" class="date-col">
                    <CustomDropdown
                      :optionList="dateList"
                      :default_text="default_date"
                      @updateCall="updateDate"
                    />
                  </b-col>
                  <b-col sm="4" class="date-col">
                    <CustomDropdown
                      :optionList="yearList"
                      :default_text="default_year"
                      @updateCall="updateYear"
                    />
                  </b-col>
                </b-row>
              </b-form-group>

              <b-form-group>
                <label for>Notes:</label>
                <b-form-textarea id size="lg" no-resize rows="5"></b-form-textarea>
              </b-form-group>
              <b-form-group>
                <label for>Workday</label>
                <CustomDropdown
                  :optionList="workdayList"
                  :default_text="default_workday"
                  @updateCall="updateWorkday"
                />
              </b-form-group>
            </form>
            <div class="btn-wrap">
              <b-button
                class="btn-borderprimary"
                @click="$refs.goalModal.hide()"
                title="Cancel"
              >Cancel</b-button>
              <b-button class="btn-bordersecondary" @click="save()" title="Submit">Submit</b-button>
            </div>
          </b-modal>
        </b-container>
      </div>
    </main>
    <footer>
      <PrimaryFooter></PrimaryFooter>
    </footer>
  </div>
</template>

<script>
import TopHeader from "../components/Layouts/ThePrimaryHeader";
import PrimaryFooter from "../components/Layouts/TheSecondaryFooter";
import CustomDropdown from "../components/CustomFieldDropdown";
import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
import SimpleBar from "simplebar";
export default {
  components: {
    TopHeader,
    PrimaryFooter,
    CustomDropdown,
    SimpleBar,
    DashboardBreadcrumb
  },

  name: "dashboardtimesheet",

  data() {
    return {
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
      timesheetHoursItems: [
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
          Mission: "Total:",
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
      default_workday: "Workdays",
      workdayList: ["Dummy", "Dummy text", "text", "4"],
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
    updateWorkday(value) {
      this.default_workday = value;
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
  },
  created() {
    var globalThis = this;
    setTimeout(() => {
      // var myElement = document.querySelectorAll('.table-responsive');
      //   myElement.forEach(function(tableScroll){
      //       console.log(tableScroll)
      // new SimpleBar(tableScroll, { autoHide: true });
      //   },1000);

      var currentCell = document.querySelectorAll(
        ".timesheethours-table td:not(.mission-col):not(.declined)"
      );
      currentCell.forEach(function(currentEvent) {
        currentEvent.addEventListener("click", function() {
          globalThis.$refs.timeHoursModal.show();
        });
      });
    });
  }
};
</script>