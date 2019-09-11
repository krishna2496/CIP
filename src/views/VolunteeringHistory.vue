<template>
  <div class="dashboard-history inner-pages">
    <header>
      <TopHeader></TopHeader>
    </header>
    <main>
      <DashboardBreadcrumb />
      <div class="dashboard-tab-content">
        <b-container>
          <div class="heading-section">
            <h1>{{langauageData.label.volunteering_history}}</h1>
          </div>
          <div class="inner-content-wrap">
            <b-row class="chart-block">
              <b-col lg="6" class="chart-col">
                <div class="inner-chart-col">
                  <div class="chart-title">
                    <h5>{{langauageData.label.hours_per_theme}}</h5>
                    <AppCustomDropdown
                      :optionList="themeYearList"
                      @updateCall="updateThemeYear"
                      :defaultText="ThemeYearText"
                      translationEnable="false"
                    />
                  </div>
                  <div class="line-chart" v-if="perHourApiDataTheme.length">
                    <horizontal-chart :labels="getThemeLabels" :data="getThemeValue"></horizontal-chart>
                  </div>
                  <div v-else class="text-center">
                    <h5>{{langauageData.label.no_record_found}}</h5>
                  </div>
                </div>
              </b-col>
              <b-col lg="6" class="chart-col">
                <div class="inner-chart-col">
                  <div class="chart-title">
                    <h5>{{langauageData.label.hours_per_skill}}</h5>
                    <AppCustomDropdown
                      :optionList="skillYearList"
                      @updateCall="updateSkillYear"
                      :defaultText="skillYearText"
                      translationEnable="false"
                    />
                  </div>
                  <div class="line-chart" v-if="perHourApiDataSkill.length">
                    <horizontal-chart :labels="getSkillLabels" :data="getSkillValue"></horizontal-chart>
                  </div>
				  <div v-else class="text-center">
					  <h5>{{langauageData.label.no_record_found}}</h5>
				  </div>
                </div>
              </b-col>
            </b-row>
            <b-row class="dashboard-table">
				<b-col lg="6" class="table-col">
				<VolunteeringRequest
					:headerField="timesheetRequestFields"
					:items="timesheetRequestItems"
					:headerLable="timeRequestLabel"
					:currentPage="hourRequestCurrentPage"
					:totalRow="hourRequestTotalRow"
					@updateCall = "getVolunteerMissionsHours"
					exportUrl = "app/volunteer/history/time-mission/export"
					:fileName="langauageData.export_timesheet_file_names.PENDING_TIME_MISSION_ENTRIES_XLSX"
				/>
				</b-col>
              <b-col lg="6" class="table-col">
                <div class="table-outer">
                  <div class="table-inner">
                    <h3>{{langauageData.label.volunteering_hours}}</h3>
                    <b-table
                      :items="hoursItems"
                      responsive
                      :fields="hoursFields"
                      class="volunteery-table"
                    ></b-table>
                  </div>
                  <div class="btn-row">
                    <b-button class="btn-bordersecondary ml-auto">{{langauageData.label.export}}</b-button>
                  </div>
                </div>
              </b-col>
              <b-col lg="6" class="table-col">
                <div class="table-outer">
                  <div class="table-inner">
                    <h3>{{langauageData.label.volunteering_goals}}</h3>
                    <b-table
                      :items="goalsItems"
                      responsive
                      :fields="goalsFields"
                      class="volunteery-table"
                    ></b-table>
                  </div>
                  <div class="btn-row">
                    <b-button class="btn-bordersecondary ml-auto">{{langauageData.label.export}}</b-button>
                  </div>
                </div>
              </b-col>
            </b-row>
            <!-- <div class="no-history-data">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
				<div class="btn-row">
					<b-button title="Start Volunteering" class="btn-borderprimary">Start Volunteering</b-button>
				</div>
            </div>-->
          </div>
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
import AppCustomDropdown from "../components/AppCustomDropdown";
import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
import HorizontalChart from "../components/HorizontalChart";
import VolunteerHistoryHours from "../services/VolunteerHistory/VolunteerHistoryHours";
import VolunteerMissionHours from "../services/VolunteerHistory/VolunteerMissionHours";
import VolunteerMissionGoals from "../services/VolunteerHistory/VolunteerMissionGoals";
import VolunteeringRequest from "../components/VolunteeringRequest";
import store from "../store";
import Chart from "chart.js";

export default {
  components: {
    TopHeader,
    PrimaryFooter,
    AppCustomDropdown,
    Chart,
    DashboardBreadcrumb,
	HorizontalChart,
	VolunteeringRequest
  },

  name: "dashboardhistory",

  data() {
    return {
		langauageData: [],
		perHourApiDataTheme: [],
		perHourApiDataSkill: [],

		timeRequestLabel :"",


		timesheetRequestItems: [],
		hourRequestCurrentPage : 1,
		hourRequestTotalRow : 0,

		VolunteeringRequest : [],
		timesheetRequestFields: [],
		goalRequestCurrentPage : 1,
		goalRequestTotalRow : 0,
		goalRequestFields: [],
		goalRequestItems: [],
			

		hoursFields: [
			{
			key: "Mission",
			class: "mission-col"
			},
			{
			key: "Time",
			class: "time-col"
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
		hoursItems: [
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
		goalsFields: [
			{
			key: "Mission",
			class: "mission-col"
			},
			{
			key: "Goal",
			class: "goal-col"
			},
			{
			key: "Result",
			class: "result-col"
			},
			{
			key: "Result%",
			class: "result-col"
			},
			{
			key: "Organisation",
			class: "organisation-col"
			}
		],
		goalsItems: [
			{
			Mission: "Plant trees",
			Goal: "Plant 1000 tree",
			Result: 900,
			"Result%": "90%",
			Organisation: "Red Cross"
			},
			{
			Mission: "Feed Kids",
			Goal: "Provide 5000 meals",
			Result: 4400,
			"Result%": "88%",
			Organisation: "Red Cross"
			},
			{
			Mission: "Feed the homeless",
			Goal: "Provide 2500 meals",
			Result: 400,
			"Result%": "20%",
			Organisation: "Green House"
			}
		],

		ThemeYearText: "Year",
		themeYearList: [
			["2016", "2016"],
			["2017", "2017"],
			["2018", "2018"],
			["2019", "2019"]
		],
		skillYearText: "Year",
		skillYearList: []
    };
  },
  mounted() {
    var currentYear = new Date().getFullYear();
    var yearsList = [];    
    for (var index = currentYear; index > (currentYear - 5); index--) {
      yearsList.push([index, index]);
    }
    this.skillYearList = yearsList;
    this.themeYearList = yearsList;
  },
  methods: {
    updateThemeYear(value) {
      this.ThemeYearText = value.selectedVal;
      this.getVolunteerHistoryHoursOfType("theme", this.ThemeYearText);
    },
    updateSkillYear(value) {
	  this.skillYearText = value.selectedVal;
	  this.getVolunteerHistoryHoursOfType("skill", this.skillYearText);
    },
    getVolunteerHistoryHoursOfType(type = "theme", year = "") {
      VolunteerHistoryHours(type, year).then(response => {
        let typeName =
          "perHourApiData" + type.charAt(0).toUpperCase() + type.slice(1);
        if (typeof response.data !== "undefined") {
          this[typeName] = Object.values(response.data);
        } else {
          this[typeName] = [];
        }
      });
	},
	getVolunteerMissionsHours(currentPage) {
		VolunteerMissionHours(currentPage).then(response => {
			var _this = this;
            _this.timesheetRequestItems = [];
			if(response.data) {
				let data = response.data;
				let mission = this.langauageData.label.mission;
				let time = this.langauageData.label.time;
				let hours = this.langauageData.label.hours;
				let organisation = this.langauageData.label.organisation;
				console.log(response.pagination);
				if(response.pagination) {
					_this.hourRequestTotalRow = response.pagination.total;
					_this.hourRequestCurrentPage = response.pagination.current_page
				}
				
				data.filter(function(item,index){
					_this.timesheetRequestItems.push(
						{
							[mission] : item.title,
							[time] : item.time,
							[hours] : item.hours,
							[organisation] : item.organisation_name,
						}
					)
				})
			}
		})
	},
	getVolunteerMissionsGoals(currentPage) {
		VolunteerMissionGoals(currentPage).then(response => {
			// console.log(response);
		})
	}
  },
  created() {
	this.langauageData = JSON.parse(store.state.languageLabel);
	this.timeRequestLabel = this.langauageData.label.hours_requests
    this.getVolunteerHistoryHoursOfType("theme");
	this.getVolunteerHistoryHoursOfType("skill");
	this.getVolunteerMissionsHours();
	this.getVolunteerMissionsGoals();
  },
  computed: {
    getThemeLabels: {
      get: function() {
        var labelArray = [];
        if (this.perHourApiDataTheme.length > 0) {
          this.perHourApiDataTheme.map(function(data) {
            labelArray.push(data.theme_name);
          });
        } else {
          return labelArray;
        }
        return labelArray;
      }
    },
    getThemeValue: {
      get: function() {
        var valueArray = [];
        if (this.perHourApiDataTheme.length > 0) {
          this.perHourApiDataTheme.map(function(data) {
            valueArray.push((data.total_minutes / 60).toFixed(2));
          });
        } else {
          return valueArray;
        }
        return valueArray;
      }
	},
	getSkillLabels: {
      get: function() {
        var labelArray = [];
        if (this.perHourApiDataSkill.length > 0) {
          this.perHourApiDataSkill.map(function(data) {
            labelArray.push(data.skill_name);
          });
        } else {
          return labelArray;
        }
        return labelArray;
      }
    },
    getSkillValue: {
      get: function() {
        var valueArray = [];
        if (this.perHourApiDataSkill.length > 0) {
          this.perHourApiDataSkill.map(function(data) {
            valueArray.push((data.total_minutes / 60).toFixed(2));
          });
        } else {
          return valueArray;
        }
        return valueArray;
      }
    }
  }
};
</script>