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
                <b-button class="btn-bordersecondary ml-auto">{{langauageData.label.export}}</b-button>
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

export default {
    name: "VolunteeringRequest",
    components: {
    },
    props: {
        items : Array,
        headerField : Array,
        headerLable : String,
        currentPage : Number,
        totalRow : Number
    },
    data: function() {
        return {
            langauageData : [],     
            perPage : 5,
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
    },
    created() {
    	this.langauageData = JSON.parse(store.state.languageLabel)
    }
};
</script>

