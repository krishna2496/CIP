<template>
	<div class="tab-with-picker">
		<h2>{{langauageData.label[currentMonthName]}} {{currentYearNumber}}</h2>
		<div class="inner-wrap">
			<!-- <button class="add-entry"  @click="$refs.timeHoursModal.show()">
				<img :src="$store.state.imagePath+'/assets/images/plus-ic-black'.svg" alt="plus-ic"/>
			</button> -->
			<div class="picker-btn-wrap">
				<button class="prev-btn picker-btn" :title="langauageData.label.previous" @click.stop="goPrev">
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
			<!-- <div class="picker-wrapper">
    			<div class="select-time-period">
    				<span>{{langauageData.label.day}}</span>
    				<span>{{langauageData.label.week}}</span>
    				<span class="current">{{langauageData.label.month}}</span>
    			</div>
    			 <div class="datepicker-block">
                  <img :src="$store.state.imagePath+'/assets/images/datepicker-ic.svg'" alt="datepicker-ic" />
                  <date-picker v-model="value2" range appendToBody :lang="lang" confirm></date-picker>
            </div>
          </div> -->
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
		props: [],
		data: function () {
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
					},
				},
				defaultYear: "",
				yearListing: [],
				langauageData: [],
				currentMonth: moment().startOf('month'),
				daysInCurrentMonth: 0,
				currentMonthName: '',
				currentMonthNumber: '',
				currentYearNumber: '',
				dayName: "",
				sortNameOfMonth: "",
				weekNameArray: [],
				isPreviousButtonDisable: false,
				currentMonthFix: moment().startOf('month')
			}
		},
		mounted() {
			var currentYear = new Date().getFullYear();
			var yearsList = [];
			for (var index = currentYear; index > (currentYear - 5); index--) {
				yearsList.push([index, index]);
			}
			this.yearListing = yearsList;
		},
		directives: {},
		computed: {

		},
		methods: {
			getWeekDayNameOfMonth(month, year) {
				let _this = this
				var start = moment(year + "-" + month, "YYYY-MMM");
				for (var end = moment(start).add(1, 'month'); start.isBefore(end); start.add(1, 'day')) {
					let dayName = start.format('dddd').toLowerCase();
					this.weekNameArray[start.format('D')] = this.langauageData.label[dayName];
				}
			},
			goPrev() {
				let payload = moment(this.currentMonth).year(this.currentYearNumber).subtract(1, 'months').startOf(
				'month');
				this.changeMonth(payload);
			},
			goNext() {
				let payload = moment(this.currentMonth).year(this.currentYearNumber).add(1, 'months').startOf('month');
				this.changeMonth(payload);
			},
			changeYear(year) {
				let payload = moment(this.currentMonth).year(year.selectedId)
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
				if (this.currentMonthFix.format('M') == this.currentMonth.format('M') && this.currentMonthFix.format(
						'YYYY') == this.currentMonth.format('YYYY')) {
					this.isPreviousButtonDisable = true;
				} else {
					this.isPreviousButtonDisable = false;
				}
				this.getWeekDayNameOfMonth(this.sortNameOfMonth, this.currentYearNumber)
				var selectedData = []
				selectedData['month'] = this.currentMonthNumber;
				selectedData['year'] = this.currentYearNumber;
				selectedData['weekdays'] = this.weekNameArray;
				this.$emit("updateCall", selectedData);
			},
		},
		created() {
			this.langauageData = JSON.parse(store.state.languageLabel);
			this.changeMonth(this.currentMonth);
		}
	};
</script>