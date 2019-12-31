<template>
    <div class="table-col-inner">
        <div class="table-outer timesheet-table-outer">
            <div class="table-inner">
                <h3>{{headerLable}}</h3>
                <b-table v-if="items.length > 0" :items="items" responsive :fields="headerField"
                    class="volunteery-table">
                    <template slot="Mission" slot-scope="data">
                        <b-link :to="`/mission-detail/${data.item.mission_id}`" class="table-link" target="_blank">
                            {{ data.item.Mission }}</b-link>
                    </template>
                </b-table>
                <div class="text-center" v-else>
                    <h5>{{headerLable | firstLetterCapital}} {{languageData.label.not_found}}</h5>
                </div>
            </div>
            <div class="btn-block" v-if="items.length > 0">
                <b-button class="btn-bordersecondary ml-auto" @click="exportFile">{{languageData.label.export}}
                </b-button>
            </div>
        </div>
        <div class="pagination-block" v-if="items.length > 0 && totalPages > 1">
            <b-pagination 
            :hide-ellipsis="hideEllipsis"
            v-model="page" :total-rows="totalRow" :per-page="perPage" align="center" @change="pageChange">
            </b-pagination>
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import ExportFile from "../services/ExportFile";

    export default {
        name: "VolunteeringRequest",
        components: {},
        props: {
            items: Array,
            headerField: Array,
            headerLable: String,
            currentPage: Number,
            totalRow: Number,
            exportUrl: String,
            fileName: String,
            perPage: Number,
            nextUrl: String,
            totalPages: Number
        },
        data: function () {
            return {
                languageData: [],
                page: this.currentPage,
                hideEllipsis:true
            }
        },
        directives: {},
        computed: {

        },
        methods: {
            pageChange(page) {
                this.$emit("updateCall", page);
            },
            exportFile() {
                ExportFile(this.exportUrl, this.fileName);
            }
        },
        created() {
            this.languageData = JSON.parse(store.state.languageLabel)
        }
    };
</script>