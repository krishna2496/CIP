<template>
	<div class="dashboard-history inner-pages">
		<header>
			<TopHeader></TopHeader>
		</header>
		<main>
			<DashboardBreadcrumb />
			<div class="dashboard-tab-content" v-if="!isLoading">
				<b-container>
					<div class="heading-section" v-if="isAllVisible && !isLoading">
						<h1>{{languageData.label.volunteering_history}}</h1>
					</div>
					<div class="inner-content-wrap" v-if="isAllVisible && !isLoading">
						<b-row class="chart-block">
							<b-col lg="6" class="chart-col">
								<div class="inner-chart-col">
									<div class="chart-title">
										<h5>{{languageData.label.hours_per_theme}}</h5>
										<AppCustomDropdown :optionList="themeYearList" @updateCall="updateThemeYear"
											:defaultText="ThemeYearText" translationEnable="false" />
									</div>
									<div
										v-bind:class="{ 'content-loader-wrap': true, 'loader-active': updatingThemeYear}">
										<div class="content-loader"></div>
									</div>
									<div class="line-chart" v-if="perHourApiDataTheme.length && !updatingThemeYear">
										<horizontal-chart :labels="getThemeLabels" :data="getThemeValue">
										</horizontal-chart>
									</div>
									<div v-if="perHourApiDataTheme.length == 0 && !updatingThemeYear" class="text-center">
										<h5>{{perHourDataNotFoundForTheme}}</h5>
									</div>
								</div>
							</b-col>
							<b-col lg="6" class="chart-col">
								<div class="inner-chart-col">
									<div class="chart-title">
										<h5>{{languageData.label.hours_per_skill}}</h5>
										<AppCustomDropdown :optionList="skillYearList" @updateCall="updateSkillYear"
											:defaultText="skillYearText" translationEnable="false" />
									</div>
									<div
										v-bind:class="{ 'content-loader-wrap': true, 'loader-active': updatingSkillYear}">
										<div class="content-loader"></div>
									</div>
									<div class="line-chart" v-if="perHourApiDataSkill.length && !updatingSkillYear">
										<horizontal-chart :labels="getSkillLabels" :data="getSkillValue">
										</horizontal-chart>
									</div>
									<div v-if="perHourApiDataSkill.length == 0 && !updatingSkillYear" class="text-center">
										<h5>{{perHourDataNotFoundForSkill}}</h5>
									</div>
								</div>
							</b-col>
						</b-row>
						<b-row class="dashboard-table">
							<b-col lg="6" class="table-col">
								<VolunteeringRequest :headerField="timeMissionTimesheetFields"
									:items="timeMissionTimesheetItems" :headerLable="timeMissionTimesheetLabel"
									:currentPage="timeMissionCurrentPage" :totalRow="timeMissionTotalRow"
									@updateCall="getVolunteerMissionsHours"
									exportUrl="app/volunteer/history/time-mission/export" :perPage="hourRequestPerPage"
									:nextUrl="hourRequestNextUrl"
									:fileName="languageData.export_timesheet_file_names.TIME_MISSION_HISTORY_XLSX"
									:totalPages="timeMissionTotalPage" />
							</b-col>
							<b-col lg="6" class="table-col">
								<VolunteeringRequest :headerField="goalMissionTimesheetFields"
									:items="goalMissionTimesheetItems" :headerLable="goalMissionTimesheetLabel"
									:currentPage="goalMissionCurrentPage" :totalRow="goalMissionTotalRow"
									:perPage="goalRequestPerPage" :nextUrl="goalRequestNextUrl"
									@updateCall="getVolunteerMissionsGoals"
									exportUrl="app/volunteer/history/goal-mission/export"
									:fileName="languageData.export_timesheet_file_names.GOAL_MISSION_HISTORY_XLSX"
									:totalPages="goalMissionTotalPage" />
							</b-col>
						</b-row>
					</div>
					<div class="no-history-data" v-else>
						<p>{{languageData.label.empty_volunteer_history_text}}</p>
						<div class="btn-row">
							<b-button :title="languageData.label.start_volunteering" class="btn-bordersecondary"
								@click="$router.push({ name: 'home' })">{{languageData.label.start_volunteering}}
							</b-button>
						</div>
					</div>
				</b-container>
			</div>
			<div v-else
				v-bind:class="{ 'content-loader-wrap': true, 'loader-active': isLoading}">
				<div class="content-loader"></div>
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
				languageData: [],
				perHourApiDataTheme: [],
				perHourApiDataSkill: [],

				timeMissionTimesheetLabel: "",
				timeMissionTimesheetFields: [],
				timeMissionTimesheetItems: [],
				timeMissionCurrentPage: 1,
				timeMissionTotalRow: 0,
				timeMissionTotalPage: null,
				

				goalMissionTimesheetLabel: "",
				goalMissionTimesheetFields: [],
				goalMissionTimesheetItems: [],
				goalMissionCurrentPage: 1,
				goalMissionTotalRow: 0,
				goalMissionTotalPage: null,

				ThemeYearText: "Year",
				skillYearText: "Year",
				skillYearList: [],
				themeYearList: [],
				updatingThemeYear: false,
				updatingSkillYear: false,
				hourRequestPerPage: 5,
				goalRequestPerPage: 5,
				hourRequestNextUrl: null,
				goalRequestNextUrl: null,
				perHourDataNotFoundForTheme: null,
				perHourDataNotFoundForSkill: null,
				isLoading: true
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
				this.updatingThemeYear = true;
				this.getVolunteerHistoryHoursOfType("theme", this.ThemeYearText);
			},
			updateSkillYear(value) {
				this.skillYearText = value.selectedVal;
				this.updatingSkillYear = true;
				this.getVolunteerHistoryHoursOfType("skill", this.skillYearText);
			},
			getVolunteerHistoryHoursOfType(type = "theme", year = "") {
				VolunteerHistoryHours(type, year).then(response => {
					let typeName =
						"perHourApiData" + type.charAt(0).toUpperCase() + type.slice(1);
					let perHourDataNotFoundForType = "perHourDataNotFoundFor" + type.charAt(0).toUpperCase() + type.slice(1);
					if (typeof response.data !== "undefined") {
						this[typeName] = Object.values(response.data);
					} else {
						this[typeName] = [];
						this[perHourDataNotFoundForType] = response.message
					}
					this.updatingThemeYear = false;
					this.updatingSkillYear = false;
				});
			},
			getVolunteerMissionsHours(currentPage) {
				VolunteerMissionHours(currentPage).then(response => {
					var _this = this;
					_this.timeMissionTimesheetItems = [];
					if (response.data) {
						let data = response.data;
						let mission = this.languageData.label.mission;
						let time = this.languageData.label.time;
						let hours = this.languageData.label.hours;
						let organisation = this.languageData.label.organisation;

						if (response.pagination) {
							_this.timeMissionTotalRow = response.pagination.total;
							_this.timeMissionCurrentPage = response.pagination.current_page
							_this.hourRequestPerPage = response.pagination.per_page;
							_this.hourRequestNextUrl = response.pagination.next_url;
							_this.timeMissionTotalPage = response.pagination.total_pages;
						}

						data.filter(function (item, index) {
							_this.timeMissionTimesheetItems.push({
								[mission]: item.title,
								[time]: item.time,
								[hours]: item.hours,
								[organisation]: item.organisation_name,
								['mission_id']: item.mission_id
							})
						})
					}
				})
			},
			getVolunteerMissionsGoals(currentPage) {
				VolunteerMissionGoals(currentPage).then(response => {
					var _this = this;
					_this.goalMissionTimesheetItems = [];
					if (response.data) {
						let data = response.data;
						let mission = this.languageData.label.mission;
						let action = this.languageData.label.actions;
						let organisation = this.languageData.label.organisation;
						if (response.pagination) {
							_this.goalMissionTotalRow = response.pagination.total;
							_this.goalMissionCurrentPage = response.pagination.current_page;
							_this.goalRequestPerPage = response.pagination.per_page;
							_this.goalRequestNextUrl = response.pagination.next_url;
							_this.goalMissionTotalPage = response.pagination.total_pages;
						}

						data.filter(function (item, index) {
							_this.goalMissionTimesheetItems.push({
								[mission]: item.title,
								[action]: item.action,
								[organisation]: item.organisation_name,
								['mission_id']: item.mission_id
							})
						})
					}
					this.isLoading = false;
				})
			}
		},
		created() {
			var _this = this;
			this.languageData = JSON.parse(store.state.languageLabel);
			this.timeMissionTimesheetLabel = this.languageData.label.volunteering_hours
			this.goalMissionTimesheetLabel = this.languageData.label.volunteering_goals
			this.getVolunteerHistoryHoursOfType("theme");
			this.getVolunteerHistoryHoursOfType("skill");
			this.getVolunteerMissionsHours();
			this.getVolunteerMissionsGoals();
			let timeRequestFieldArray = [
				this.languageData.label.mission,
				this.languageData.label.time,
				this.languageData.label.hours,
				this.languageData.label.organisation,
			]

			timeRequestFieldArray.filter(function (data, index) {
				_this.timeMissionTimesheetFields.push({
					"key": data
				})
			});

			let goalRequestFieldArray = [
				this.languageData.label.mission,
				this.languageData.label.actions,
				this.languageData.label.organisation,
			]

			goalRequestFieldArray.filter(function (data, index) {
				_this.goalMissionTimesheetFields.push({
					"key": data
				})
			});
		},
		computed: {
			getThemeLabels: {
				get: function () {
					var labelArray = [];
					if (this.perHourApiDataTheme.length > 0) {
						this.perHourApiDataTheme.map(function (data) {
							labelArray.push(data.theme_name);
						});
					} else {
						return labelArray;
					}
					return labelArray;
				}
			},
			getThemeValue: {
				get: function () {
					var valueArray = [];
					if (this.perHourApiDataTheme.length > 0) {
						this.perHourApiDataTheme.map(function (data) {
							valueArray.push((data.total_minutes / 60).toFixed(2));
						});
					} else {
						return valueArray;
					}
					return valueArray;
				}
			},
			getSkillLabels: {
				get: function () {
					var labelArray = [];
					if (this.perHourApiDataSkill.length > 0) {
						this.perHourApiDataSkill.map(function (data) {
							labelArray.push(data.skill_name);
						});
					} else {
						return labelArray;
					}
					return labelArray;
				}
			},
			getSkillValue: {
				get: function () {
					var valueArray = [];
					if (this.perHourApiDataSkill.length > 0) {
						this.perHourApiDataSkill.map(function (data) {
							valueArray.push((data.total_minutes / 60).toFixed(2));
						});
					} else {
						return valueArray;
					}
					return valueArray;
				}
			},
			isAllVisible: {
				get: function () {
					if (this.perHourApiDataTheme.length == 0 && this.perHourApiDataSkill.length == 0 && this
						.timeMissionTimesheetItems.length == 0 && this.goalMissionTimesheetItems.length == 0) {
						return false;
					}
					return true;
				}
			}
		}
	};
</script>