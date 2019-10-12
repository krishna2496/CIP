<template>
	<div class="user-dashboard inner-pages">
		<header>
			<ThePrimaryHeader v-if="isShownComponent"></ThePrimaryHeader>
		</header>
		<main>
			<DashboardBreadcrumb />
			<div class="dashboard-tab-content">
				<b-container v-if="isPageLoaded">
					<div class="heading-section">
						<h1>{{languageData.label.dashboard}}</h1>
						<div class="date-filter">
							<AppCustomDropdown 
								:optionList="yearList" 
								@updateCall="updateYear" 
								:defaultText="defaultYear" 
								translationEnable="false"/>
							<AppCustomDropdown 
								:optionList="monthList" 
								@updateCall="updateMonth" 
								:defaultText="defaultMonth" 
								translationEnable="true"
								class="month-dropdown" />
						</div>
					</div>
					<div class="inner-content-wrap">
						<b-list-group class="status-bar">
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/clock-ic.svg'" alt />
									</i>
									<p>
										<span>{{stats.totalHours}}</span>{{languageData.label.hours}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/certified-ic.svg'" alt />
									</i>
									<p>
										<span>{{languageData.label.top}} {{stats.volunteeringRank}}%</span>{{languageData.label.volunteering_rank}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/request-ic.svg'" alt />
									</i>
									<p>
										<span>{{stats.openVolunteeringRequests}}</span>{{languageData.label.open_volunteering_requests}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/target-ic.svg'" alt />
									</i>
									<p>
										<span>{{stats.missionCount}}</span>{{languageData.label.mission}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/vote-ic.svg'" alt />
									</i>
									<p>
										<span>{{stats.votedMissions}}</span>{{languageData.label.voted_missions}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/group-ic.svg'" alt />
									</i>
									<p>
										<span>{{stats.organizationCount}}</span>{{languageData.label.organisation}}
									</p>
								</div>
							</b-list-group-item>
						</b-list-group>
						<b-row class="chart-block">
							<b-col lg="6" class="chart-col">
								<div class="inner-chart-col">
									<div class="chart-title">
										<h5>{{languageData.label.hours_tracked_this_year}}</h5>
									</div>
									<div class="progress-chart">
										<b-progress :max="totalGoalHours">
											<b-progress-bar :value="max">
												{{languageData.label.completed}}:
												<span>{{ completedGoalHours }} {{languageData.label.hours}}</span>
											</b-progress-bar>
										</b-progress>
										<ul class="progress-axis">
											<li v-for="xvalue in xvalues" :key="xvalue">{{xvalue}}</li>
										</ul>
										<p class="progress-label">{{languageData.label.goal}}: {{ totalGoalHours }} {{languageData.label.hours}}</p>
									</div>
								</div>
							</b-col>
							<b-col lg="6" class="chart-col">
								<div class="inner-chart-col">
									<div class="chart-title">
										<h5>{{languageData.label.hours_per_month}}</h5>
										<AppCustomDropdown 
											:optionList="missionTitle" 
											@updateCall="updateMissionTitle"
											translationEnable="false"
											:defaultText="defaultMissionTitle" 
											class="month-dropdown"/>
									</div>
									<div class="line-chart">
										<canvas ref="lineChartRefs"></canvas>
									</div>
								</div>
							</b-col>
						</b-row>
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
	import AppCustomDropdown from "../components/AppCustomDropdown";
	import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
	import store from '../store';
	import Chart from "chart.js";
	import {
		storyMissionListing,
		myDashboard
	} from "../services/service";
	import moment from 'moment'
	export default {
		components: {
			ThePrimaryHeader,
			AppCustomDropdown,
			TheSecondaryFooter,
			DashboardBreadcrumb
		},

		name: "Dashboard",

		data() {
			return {
				isShownComponent : true,
				isPageLoaded : true,
				max: 0,
				xvalues: [0],
				defaultYear: "",
				defaultMonth: "",
				defaultMissionTitle: "",
				yearList: [],
				missionTitle: [],
				monthList: [
					["01","january"],
					["02","february"],
					["03","march"],
					["04","april"],
					["05","may"],
					["06","june"],
					["07","july"],
					["08","august"],
					["09","september"],
					["10","october"],
					["11","november"],
					["12","december"]
				],
				chartdata: {
					labels: [
						"Jan 18",
						"Feb 18",
						"Apr 18",
						"Mar 18",
						"May 18",
						"Jun 18",
						"Jul 18",
						"Aug 18"
					]
				},
				currentpage: "dashboard",
				languageData : [],
				filterData : {
					year : '',
					month : '',
					mission_id : ''
				},
				stats :{
					'totalHours' : 0,
					'volunteeringRank' : 0,
					'openVolunteeringRequests' : 0,
					'missionCount' : 0,
					'votedMissions' : 0,
					'organizationCount' : 0
				},
				totalGoalHours : 0,
				completedGoalHours : 0
			};
		},
		mounted() {
			var lineChartRefs = this.$refs.lineChartRefs;
			var lineContent = lineChartRefs.getContext("2d");
			lineChartRefs.height = 350;
			new Chart(lineContent, {
				type: "line",
				data: {
					labels: [
						"Jan 18",
						"Feb 18",
						"Apr 18",
						"Mar 18",
						"May 18",
						"Jun 18",
						"Jul 18",
						"Aug 18"
					],
					datasets: [{
						data: [25, 10, 0, 20, 15, 10, 48, 0],
						backgroundColor: "#b2cc9d",
						borderColor: "#4b6d30",
						pointBackgroundColor: "#4b6d30"
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					legend: {
						display: false
					},
					scales: {
						xAxes: [{
							gridLines: {
								display: false
							},
							ticks: {
								fontColor: "#414141",
								fontSize: 14
							},
							scaleLabel: {
								display: true,
								labelString: "Month",
								fontSize: 16,
								fontColor: "#414141"
							}
						}],
						yAxes: [{
							stacked: true,
							ticks: {
								max: 75,
								min: 0,
								stepSize: 25,
								fontColor: "#414141",
								fontSize: 14
							},
							scaleLabel: {
								display: true,
								labelString: "Hours",
								fontSize: 16,
								fontColor: "#414141"
							}
						}]
					},
					elements: {
						line: {
							tension: 0
						}
					}
				}
			});

			var currentYear = new Date().getFullYear();
			var yearsListing = [];
			for (var index = currentYear; index > (currentYear - 5); index--) {
				yearsListing.push([index, index]);
			}
			this.yearList = yearsListing;
		},
		methods: {
			updateYear(value) {
				this.defaultYear = value.selectedVal;
				this.filterData.year = value.selectedId
				this.getDashboardData(this.filterData)
			},
			updateMonth(value) {
				this.defaultMonth = value.selectedVal;
				this.filterData.month = value.selectedId
				this.getDashboardData(this.filterData)
			},
			updateMissionTitle(value) {
				this.defaultMissionTitle = value.selectedVal;
			},
			handleActive() {},
			missionListing() {
				storyMissionListing().then(response => {
					// missionTitle
					var array = [];
					if(response.error == false) {
						let missionArray = response.data
						if(missionArray) {
							missionArray.filter((data,index) => {
								array[index] = new Array(2);
								array[index][0] = data.mission_id
								array[index][1] = data.title
							})
							this.missionTitle = array
						}
					}
				})
			},
			getDashboardData(filterData) {
				myDashboard(filterData).then(response => {
					if(response.error == false) {
						if(response.data) {
							if(response.data.total_hours && response.data.total_hours != '') {
								this.stats.totalHours = response.data.total_hours 
							}
							if(response.data.volunteering_rank && response.data.volunteering_rank != '') {
								this.stats.volunteeringRank = response.data.volunteering_rank
							}
							if(response.data.open_volunteering_requests && response.data.open_volunteering_requests != '') {
								this.stats.openVolunteeringRequests = response.data.open_volunteering_requests
							}
							if(response.data.mission_count && response.data.mission_count != '') {
								this.stats.missionCount = response.data.mission_count
							}
							if(response.data.voted_missions && response.data.voted_missions != '') {
								this.stats.votedMissions = response.data.voted_missions
							}
							if(response.data.organization_count && response.data.organization_count != '') {
								this.stats.organizationCount = response.data.organization_count
							}
							if(response.data.completed_goal_hours && response.data.completed_goal_hours != '') {
								this.completedGoalHours = response.data.completed_goal_hours
							}
							if(response.data.total_goal_hours && response.data.total_goal_hours != '') {
								this.totalGoalHours = response.data.total_goal_hours
								let axes = (this.totalGoalHours/10);
								this.max = Math.ceil(axes)
								let xValue = 0;
								for(var i=0;i<10;i++) {
									xValue = xValue + this.max;
									this.xvalues.push(xValue)
								}
							}
						}
					}
				})
			}
		},
		created() {
			this.languageData = JSON.parse(store.state.languageLabel);
			this.defaultYear =  this.languageData.label.years
			this.defaultMonth =  this.languageData.label.month
			this.defaultMissionTitle =  this.languageData.label.mission_title
			let currentYear = new Date().getFullYear()
			let currentMonth = moment().format('MM')
			this.defaultYear = currentYear.toString();
			this.monthList.filter((data,index) => {
				if(data[0] == currentMonth) {
					this.defaultMonth = data[1]
				}
			})
			this.filterData.year = currentYear
			this.filterData.month = currentMonth
			this.missionListing()
			this.getDashboardData(this.filterData)
		}
	};
</script>