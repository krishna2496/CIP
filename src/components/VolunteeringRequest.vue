<template>
    <div>
            <div class="table-outer timesheet-table-outer">
                <div class="table-inner">
                    <h3>{{headerLable}}</h3>
                    <b-table
                        :items="items"
                        responsive
                        :fields="headerField"
                        class="volunteery-table"
                    >
                   </b-table>
                </div>
                <div class="btn-block">
                    <b-button class="btn-bordersecondary ml-auto">{{langauageData.label.export}}</b-button>
                </div>  
            </div>
            <div class="pagination-block">
                <b-pagination
                v-model="currentPage"
                :total-rows="rows"
                :per-page="perPage"
                align="center"
                aria-controls=""
                ></b-pagination>
          </div>
      </div>
</template>

<script>
import store from '../store';
import moment from 'moment'
import DatePicker from "vue2-datepicker";

export default {
    name: "VolunteeringTimesheetHeader",
    components: {
        DatePicker
    },
    props: {
        items : Array,
        headerField : Array,
        headerLable : String
    },
    data: function() {
        return {
            langauageData : [],
            currentPage : 1,
            rows : 12,
            perPage : 5
        }
    },
    directives: {},
    computed: {
        
    },
    methods: {
		 
    },
    created() {
    	this.langauageData = JSON.parse(store.state.languageLabel)
    }
};
</script>

