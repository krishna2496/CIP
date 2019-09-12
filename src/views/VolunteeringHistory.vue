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
          <div class="inner-content-wrap" v-if="isAllVisible">
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
						:headerField="timeMissionTimesheetFields"
						:items="timeMissionTimesheetItems"
						:headerLable="timeMissionTimesheetLabel"
						:currentPage="timeMissionCurrentPage"
						:totalRow="timeMissionTotalRow"
						@updateCall = "getVolunteerMissionsHours"
						exportUrl = "app/volunteer/history/time-mission/export"
            :perPage = "hourRequestPerPage"
            :nextUrl = "hourRequestNextUrl"
						:fileName="langauageData.export_timesheet_file_names.TIME_MISSION_HISTORY_XLSX"
					/>
				</b-col>
              <b-col lg="6" class="table-col">
					<VolunteeringRequest
						:headerField="goalMissionTimesheetFields"
						:items="goalMissionTimesheetItems"
						:headerLable="goalMissionTimesheetLabel"
						:currentPage="goalMissionCurrentPage"
						:totalRow="goalMissionTotalRow"
            :perPage = "goalRequestPerPage"    
            :nextUrl = "goalRequestNextUrl"
						@updateCall = "getVolunteerMissionsGoals"
						exportUrl = "app/volunteer/history/goal-mission/export"
						:fileName="langauageData.export_timesheet_file_names.GOAL_MISSION_HISTORY_XLSX"
					/>
              </b-col>
            </b-row>
            <!-- <div class="no-history-data">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
				<div class="btn-row">
					<b-button title="Start Volunteering" class="btn-borderprimary">Start Volunteering</b-button>
				</div>
            </div>-->
          </div>
		  <div class="inner-content-wrap" v-else>
			  <h3>No data found</h3>
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

		timeMissionTimesheetLabel :"",
		timeMissionTimesheetFields: [],
		timeMissionTimesheetItems: [],
		timeMissionCurrentPage : 1,
		timeMissionTotalRow : 0,

		goalMissionTimesheetLabel :"",
		goalMissionTimesheetFields: [],
		goalMissionTimesheetItems: [],
		goalMissionCurrentPage : 1,
		goalMissionTotalRow : 0,

		ThemeYearText: "Year",
		skillYearText: "Year",
    skillYearList: [],
    hourRequestPerPage : 5,
    goalRequestPerPage : 5,
    hourRequestNextUrl : null,
    goalRequestNextUrl : null
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
            _this.timeMissionTimesheetItems = [];
			if(response.data) {
				let data = response.data;
				let mission = this.langauageData.label.mission;
				let time = this.langauageData.label.time;
				let hours = this.langauageData.label.hours;
				let organisation = this.langauageData.label.organisation;
				
				if(response.pagination) {
					_this.timeMissionTotalRow = response.pagination.total;
          _this.timeMissionCurrentPage = response.pagination.current_page
          _this.hourRequestPerPage = response.pagination.per_page;
          _this.hourRequestNextUrl = response.pagination.next_url
				}
				
				data.filter(function(item,index){
					_this.timeMissionTimesheetItems.push(
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
			var _this = this;
            _this.goalMissionTimesheetItems = [];
			if(response.data) {
				let data = response.data;
				let mission = this.langauageData.label.mission;
				let action = this.langauageData.label.actions;
				let organisation = this.langauageData.label.organisation;
				if(response.pagination) {
					_this.goalMissionTotalRow = response.pagination.total;
          _this.goalMissionCurrentPage = response.pagination.current_page
          _this.goalRequestPerPage = response.pagination.per_page;
          _this.goalRequestNextUrl = response.pagination.next_url
				}
				
				data.filter(function(item,index){
					_this.goalMissionTimesheetItems.push(
						{
							[mission] : item.title,
							[action] : item.action,
							[organisation] : item.organisation_name,
						}
					)
				})
			}
		})
	}
  },
  created() {
	this.langauageData = JSON.parse(store.state.languageLabel);
	this.timeMissionTimesheetLabel = this.langauageData.label.volunteering_hours
	this.goalMissionTimesheetLabel = this.langauageData.label.volunteering_goals
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
	},
	isAllVisible: {
		get: function() {
			if (this.perHourApiDataTheme.length == 0 && this.perHourApiDataSkill.length == 0 && this.timeMissionTimesheetItems.length == 0 && this.goalMissionTimesheetItems.length == 0) {
				return false;
			}
			return true;
		}		
	}
  }
};
</script>