<template>
    <div>       
        <div class="table-outer timesheet-table-outer" v-if="items.length > 0">
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
                <b-button class="btn-bordersecondary ml-auto" @click="exportFile">{{langauageData.label.export}}</b-button>
            </div>  
        </div>
        <div class="pagination-block" v-if="items.length > 0">
            <b-pagination
            v-model="currentPage"
            :total-rows="totalRow"
            :per-page="perPage"
            align="center"
            @change="pageChange"
            ></b-pagination>
      </div>
      </div>
</template>

<script>
import store from '../store';
import moment from 'moment'
import DatePicker from "vue2-datepicker";
import ExportFile from "../services/ExportFile";
export default {
    name: "VolunteeringRequest",
    components: {
        DatePicker
    },
    props: {
        items : Array,
        headerField : Array,
        headerLable : String,
        currentPage : Number,
        totalRow : Number,
        exportUrl : String,
        fileName : String
    },
    data: function() {
        return {
            langauageData : [],     
            perPage : 1,
            page : this.currentPage
        }
    },
    directives: {},
    computed: {
        
    },
    methods: {
        pageChange (page) {
            this.$emit("updateCall", page);
        },
        exportFile() {
            ExportFile(this.exportUrl, this.fileName)
            .then(response => {
                console.log(response);
            })
        }
    },
    created() {
    	this.langauageData = JSON.parse(store.state.languageLabel)
    }
};
</script>

