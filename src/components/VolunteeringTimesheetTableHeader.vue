<template>
	<div class="tab-with-picker">
		<div class="table-header">
		<h2>{{langauageData.label[currentMonthName]}} {{currentYearNumber}}</h2>
		<div class="inner-wrap">
			<div class="picker-btn-wrap table-action-btn">
				<button class="prev-btn picker-btn" 
				v-bind:class="{disabled :previousButtonDisable}"
				v-b-tooltip.hover :title="langauageData.label.previous +' '+langauageData.label.week.toLowerCase()" @click.stop="goPrevWeek">
					<img :src="$store.state.imagePath+'/assets/images/back-arrow-black.svg'"
						:alt="langauageData.label.previous" />
				</button>

				<!-- <span>{{currentWeak}}</span> -->
				<button class="next-btn picker-btn" v-b-tooltip.hover  :title="langauageData.label.next+' '+langauageData.label.week.toLowerCase()"
					v-bind:class="{disabled :disableNextWeek}" @click.stop="goNextWeek">
					<img :src="$store.state.imagePath+'/assets/images/next-arrow-black.svg'"
						:alt="langauageData.label.next" />
				</button>
			</div>
			<div class="picker-btn-wrap">
				<button class="prev-btn picker-btn" 
				v-bind:class="{disabled :previousButtonDisable}"
				:title="langauageData.label.previous" @click.stop="goPrev">
					<img :src="$store.state.imagePath+'/assets/images/back-arrow-black.svg'"
						:alt="langauageData.label.previous" />
				</button>

				<span>{{langauageData.label[currentMonthName]}}</span>
				<button class="next-btn picker-btn" :title="langauageData.label.next"
					v-bind:class="{disabled :isPreviousButtonDisable}" @click.stop="goNext">
					<img :src="$store.state.imagePath+'/assets/images/next-arrow-black.svg'"
						:alt="langauageData.label.next" />
				</button>
			</div>		
			<div>
				<AppCustomDropdown :optionList="yearListing" @updateCall="changeYear" :defaultText="defaultYear"
					translationEnable="false" />
			</div>

		</div>
		</div>
	</div>
</template>

<script>
	import store from '../store';
	import moment from 'moment'
	import DatePicker from "vue2-datepicker";
	import AppCustomDropdown from "../components/AppCustomDropdown";

	export default {
		name: "VolunteeringTimesheetHeader",
		components: {
			DatePicker,
			AppCustomDropdown
		},
		props: {
            currentWeek: Number
        },
		data: function () {
			return {
				time1: "",
				value2: "",
				currentWeak : this.currentWeek,
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
					},
				},
				defaultYear: "",
				yearListing: [],
				langauageData: [],
				currentMonth: '',
				daysInCurrentMonth: 0,
				currentMonthName: '',
				currentMonthNumber: '',
				currentYearNumber: '',
				dayName: "",
				sortNameOfMonth: "",
				weekNameArray: [],
				daysArray : [],
				isPreviousButtonDisable: false,
				currentMonthFix: moment().startOf('date'),
				currentFixWeek : moment().week(),
				disableNextWeek : false,
				yearArray : [],
				monthArray : [],
				previousButtonDisable : false,
				lastYear : ''
			}
		},
		watch: { 
			currentWeek: function(newVal, oldVal) { // watch it
				this.currentWeak = newVal
				let payload = moment().startOf('date').week(this.currentWeak)
				this.changeMonth(payload);
			}
		},
		mounted() {
			var currentYear = new Date().getFullYear();
			var yearsList = [];
			for (var index = currentYear; index > (currentYear - 5); index--) {
				yearsList.push([index, index]);
			}
			this.yearListing = yearsList;
			this.lastYear = parseInt(yearsList[yearsList.length -1][1]);
		},
		directives: {},
		computed: {

		},
		methods: {
			goPrevWeek() {
				let payload = moment(this.currentMonth).year(this.currentYearNumber).subtract(7, 'day')
				this.currentWeak = moment(this.currentMonth).year(this.currentYearNumber).subtract(7, 'day').week()
				this.changeMonth(payload);
			},
			goNextWeek() {
				let payload = moment(this.currentMonth).year(this.currentYearNumber).add(7, 'day')
				this.currentWeak = moment(this.currentMonth).year(this.currentYearNumber).add(7, 'day').week()
				this.changeMonth(payload);
                this.$root.$emit('bv::hide::tooltip');
			},
			getWeekDayNameOfMonth(month, year) {
				//pass week number
				//stating date of week	
				let _this = this
				var start = moment().day("Monday").year(this.currentYearNumber).week(this.currentWeak);
				
				this.weekNameArray = []
				this.daysArray = []
				let i=0;
				let j = 1;
				for (var end = moment(start).add(1, 'week'); start.isBefore(end); start.add(1, 'day')) {
					let dayName = start.format('dddd').toLowerCase();
						this.weekNameArray[j] = this.langauageData.label[dayName];
						this.daysArray[i] = start.format('D')-1
						this.yearArray[i] = start.format('YYYY')
						this.monthArray[i] = start.format('M')
						i++;
						j++;
				}
			},
			goPrev() {
				let payload = moment(this.currentMonth).year(this.currentYearNumber).subtract(1, 'months').startOf(
				'month');
				this.currentWeak= moment(this.currentMonth).year(this.currentYearNumber).subtract(1, 'months').startOf(
				'month').week()
				
				this.changeMonth(payload);
			},
			goNext() {
				let payload = moment(this.currentMonth).year(this.currentYearNumber).add(1, 'months').startOf('month');
				this.currentWeak= moment(this.currentMonth).year(this.currentYearNumber).add(1, 'months').startOf(
				'month').week()
				this.changeMonth(payload);
			},
			changeYear(year) {
				let payload = moment(this.currentMonth).year(year.selectedId)
				
				if ((parseInt(this.currentMonthFix.format('M')) <= parseInt(payload.format('M'))) && (parseInt(this.currentMonthFix.format(
						'YYYY')) <= parseInt(payload.format('YYYY')))) {
					payload = moment().startOf('date');
					this.currentWeak= this.currentFixWeek;
				} else {
					this.currentWeak= moment(this.currentMonth).year(this.currentYearNumber).startOf('month').week()
				}
				this.changeMonth(payload);
			},
			changeMonth(payload) {
				this.currentMonth = payload;
				this.daysInCurrentMonth = this.currentMonth.daysInMonth();
				this.currentMonthName = this.currentMonth.format('MMMM').toLowerCase();
				this.currentMonthNumber = this.currentMonth.format('M');
				this.currentYearNumber = this.currentMonth.format('Y');
				this.sortNameOfMonth = this.currentMonth.format('MMM')
				this.defaultYear = this.currentMonth.format('Y');

				if ((parseInt(this.currentMonthFix.format('M')) <= parseInt(this.currentMonth.format('M'))) && (parseInt(this.currentMonthFix.format(
						'YYYY')) <= parseInt(this.currentMonth.format('YYYY')))) {		
					this.isPreviousButtonDisable = true;

					// previousButtonDisable
				} else {
					this.isPreviousButtonDisable = false;
				}
			
				if(this.currentFixWeek  <= this.currentWeak && (parseInt(this.currentMonthFix.format(
						'YYYY')) <= parseInt(this.currentMonth.format('YYYY'))) ) {
					this.disableNextWeek = true
				} else {
					this.disableNextWeek = false
				}
				
				if(this.lastYear == parseInt(this.currentYearNumber) && (this.currentMonthNumber <= 1)) {
					this.previousButtonDisable = true
				} else {
					this.previousButtonDisable = false
				}
				this.getWeekDayNameOfMonth(this.sortNameOfMonth, this.currentYearNumber)
				var selectedData = []
				selectedData['month'] = this.currentMonthNumber;
				selectedData['year'] = this.currentYearNumber;
				selectedData['weekdays'] = this.weekNameArray;
				selectedData['days'] = this.daysArray;
					// this.yearArray[day] = start.format('YYYY')
					// 	this.monthArray[day] = start.format('MMM')
				selectedData['yearArray'] = this.yearArray;
				selectedData['monthArray'] = this.monthArray;
				this.$emit("updateCall", selectedData);
			},
		},
		created() {
			this.langauageData = JSON.parse(store.state.languageLabel);
			this.currentMonth = moment().startOf('date').week(this.currentWeak);
			this.changeMonth(this.currentMonth);
		}
	};
</script>