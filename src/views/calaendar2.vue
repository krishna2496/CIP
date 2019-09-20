<template>
  <div class="dashboard-timesheet inner-pages">
    <div class="col-sm-4 header-center">
        {{currentMonthName}} {{currentYearNumber}}
    <div class="btn-group">
        <button @click.stop="goPrev" class="btn btn-outline btn-primary">&lArr;</button>
        <button class="btn btn-outline btn-default today-button">{{currentMonthName}}</button>
        <button @click.stop="goNext" class="btn btn-outline btn-primary" 
            v-bind:class=
            "{disabled :isPreviousButtonDisable}"
        > &rArr;</button>
    </div>
    <div>
        {{daysInCurrentMonth}}
        {{sortNameOfMonth}}
        {{weekNameArray}}
        <!-- {{currentYearNumber}} -->
    </div>
</div>
  </div>
</template>

<script>
import moment from 'moment'

export default {
        name: 'calendar2',
        data() {
            return {
                 currentMonth: moment().startOf('month'),
                 firstDay : 1,
                 appLocale :'en',
                 allEvents: [],
                 daysInCurrentMonth : 0,
                 currentMonthName : '',
                 currentMonthNumber : '',
                 currentYearNumber : '',
                 dayName : "",
                 sortNameOfMonth : "",
                 weekNameArray : [],
                 isPreviousButtonDisable : false,
                 currentMonthFix : moment().startOf('month'),
            }
        },
        components: {
            
        },
        created() {
           // this.daysInCurrentMonth = this.currentMonth.daysInMonth();
           // this.currentMonthName = this.currentMonth.format('MMMM');
           // this.currentMonthNumber = this.currentMonth.format('M');
           // this.currentYearNumber = this.currentMonth.format('Y');
           // this.dayName = this.currentMonth.format('dddd');
           // this.sortNameOfMonth = this.currentMonth.format('MMM')
            this.Weeks();
           this.changeMonth(this.currentMonth);
        },
        mounted() {
            let me = this;
           
            
        },
        computed: {

           
            events: function () {
                return this.allEvents;
            }
        },
        methods: {
            
            Weeks () {
                let monthMomentObject = this.getMonthViewStartDate(this.currentMonth, this.firstDay);
                let weeks = [], week = [];

                let daysInCurrentMonth = this.currentMonth.daysInMonth();

                for ( let weekIndex=0; weekIndex < 5; weekIndex++) {

                    week = [];
                    for (let dayIndex=0; dayIndex < 7; dayIndex++) {

                        week.push(this.getDayObject(monthMomentObject, dayIndex));

                        monthMomentObject.add(1, 'day');
                    }

                    weeks.push(week);
                }

                let diff = daysInCurrentMonth-weeks[4][6].date.format('D');


                if(diff > 0 && diff < 3){
                    week = [];
                    for (let dayIndex=0; dayIndex < 7; dayIndex++) {

                        week.push(this.getDayObject(monthMomentObject, dayIndex));

                        monthMomentObject.add(1, 'day');
                    }

                    weeks.push(week);
                }
                console.log(weeks);
                return weeks;
            },
            	getEvents (date) {
				return this.events.filter(event => {
					return date.isSame(event.date, 'day')?event:null;
				});
			},

            getDayObject(monthMomentObject, dayIndex){
				return {
					isToday: monthMomentObject.isSame(moment(), 'day'),
					isCurrentMonth: monthMomentObject.isSame(this.currentMonth, 'month'),
					weekDay: dayIndex,
					isWeekEnd: (dayIndex == 5 || dayIndex == 6),
					date: moment(monthMomentObject),
					events: this.getEvents(monthMomentObject)
				};
			},
            getMonthViewStartDate (date, firstDay) {
				firstDay = parseInt(firstDay);

				let start = moment(date).locale(this.appLocale);
				let startOfMonth = moment(start.startOf('month'));

				start.subtract(startOfMonth.day(), 'days');

				if (startOfMonth.day() < firstDay) {
					start.subtract(7, 'days');
				}

				start.add(firstDay, 'days');

				return start;
			},
            getWeekDayNameOfMonth(month,year){
            let _this = this
                var start = moment(year+"-"+month,"YYYY-MMM");
                for(var end = moment(start).add(1,'month');  start.isBefore(end); start.add(1,'day')){
                     this.weekNameArray[start.format('D')] = start.format('dddd').toLowerCase(); 
                  
                }         
            },
            goPrev () {
                let payload = moment(this.currentMonth).subtract(1, 'months').startOf('month');
                this.changeMonth(payload);
            },
            goNext () {
                let payload = moment(this.currentMonth).add(1, 'months').startOf('month');
                this.changeMonth(payload);
            },
            changeMonth(payload) {
                this.currentMonth = payload;
                this.daysInCurrentMonth = this.currentMonth.daysInMonth();
                this.currentMonthName = this.currentMonth.format('MMMM');
                this.currentMonthNumber = this.currentMonth.format('M');
                this.currentYearNumber = this.currentMonth.format('Y');
                this.sortNameOfMonth = this.currentMonth.format('MMM')
                console.log(this.currentMonthFix.format('M'));
                console.log(this.currentMonth.format('M'));
                if(this.currentMonthFix.format('M') == this.currentMonth.format('M')) {
                    this.isPreviousButtonDisable = true;
                } else {
                    this.isPreviousButtonDisable = false;
                }
                this.getWeekDayNameOfMonth(this.sortNameOfMonth,this.currentYearNumber)
            },
              
        },
        filters: {
            // weekDayName (weekday, firstDay, locale) {
            //     firstDay = parseInt(firstDay);
            //     const localMoment = moment().locale(locale);
            //     return localMoment.localeData().weekdaysShort()[(weekday + firstDay) % 7];
            // }
        }
  }  
</script>