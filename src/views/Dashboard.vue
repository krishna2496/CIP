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
										<span>1h50</span>{{languageData.label.hours}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/certified-ic.svg'" alt />
									</i>
									<p>
										<span>{{languageData.label.top}} 2%</span>{{languageData.label.volunteering_rank}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/request-ic.svg'" alt />
									</i>
									<p>
										<span>25</span>{{languageData.label.open_volunteering_requests}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/target-ic.svg'" alt />
									</i>
									<p>
										<span>10</span>{{languageData.label.mission}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/vote-ic.svg'" alt />
									</i>
									<p>
										<span>55</span>{{languageData.label.voted_missions}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/group-ic.svg'" alt />
									</i>
									<p>
										<span>101</span>{{languageData.label.organisation}}
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
										<b-progress :max="max">
											<b-progress-bar :value="value">
												{{languageData.label.completed}}:
												<span>{{ value }} {{languageData.label.hours}}</span>
											</b-progress-bar>
										</b-progress>
										<ul class="progress-axis">
											<li v-for="xvalue in xvalues" :key="xvalue">{{xvalue}}</li>
										</ul>
										<p class="progress-label">{{languageData.label.goal}}: 500 {{languageData.label.hours}}</p>
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
	import Chart from "chart.js";
	import store from '../store';

	export default {
		components: {
			ThePrimaryHeader,
			AppCustomDropdown,
			TheSecondaryFooter,
			Chart,
			DashboardBreadcrumb
		},

		name: "Dashboard",

		data() {
			return {
				isShownComponent : true,
				isPageLoaded : true,
				value: 210,
				max: 500,
				xvalues: [0, 50, 100, 150, 200, 250, 300, 350, 400, 450, 500],
				defaultYear: "",
				defaultMonth: "",
				defaultMissionTitle: "",
				yearList: [],
				missionTitle: [
					["title1","Mission title1"],
					["title2","Mission title2"],
					["title3","Mission title3"],
					["title4","Mission title4"]
				],
				monthList: [
					["1","january"],
					["2","february"],
					["3","march"],
					["4","april"],
					["5","may"],
					["6","june"],
					["7","july"],
					["8","august"],
					["9","september"],
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
				languageData : []
			};
		},
		mounted() {
			var lineChartRefs = this.$refs.lineChartRefs;
			var lineContent = lineChartRefs.getContext("2d");
			lineChartRefs.height = 350;
			var lineChart = new Chart(lineContent, {
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
			},
			updateMonth(value) {
				this.defaultMonth = value.selectedVal;
			},
			updateMissionTitle(value) {
				this.defaultMissionTitle = value.selectedVal;
			},
			handleActive() {}
		},
		created() {
			this.languageData = JSON.parse(store.state.languageLabel);
			this.defaultYear =  this.languageData.label.years
			this.defaultMonth =  this.languageData.label.month
			this.defaultMissionTitle =  this.languageData.label.mission_title
		}
	};
</script>