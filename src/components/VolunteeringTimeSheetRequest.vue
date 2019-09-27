<template>
    <div>
        <div class="table-outer timesheet-table-outer">
            <div class="table-inner">
                <h3>{{headerLable}}</h3>
                <b-table-simple v-if="items.length > 0" small responsive class="timesheet-request">
                    <b-thead>
                        <b-tr>
                            <b-th v-for="(item,key) in headerField">{{item.key}}</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody v-if="requestType == 'time'">
                        <b-tr v-for="(item,key) in items">
                            <b-td>
                                <a target="_blank" :href="`mission-detail/${item.missionId}`">{{item.mission}}</a>
                            </b-td>
                            <b-td>
                                {{item.time}}
                            </b-td>
                            <b-td>
                                {{item.hours}}
                            </b-td>
                            <b-td>
                                {{item.organization}}
                            </b-td>
                        </b-tr>
                    </b-tbody>
                    <b-tbody v-if="requestType == 'goal'">
                        <b-tr v-for="(item,key) in items">
                            <b-td>
                                <a target="_blank" :href="`mission-detail/${item.missionId}`">{{item.mission}}</a>
                            </b-td>
                            <b-td>
                                {{item.action}}
                            </b-td>
                            <b-td>
                                {{item.organization}}
                            </b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
                </b-table>
                <div class="text-center" v-else>
                    <h5>{{languageData.label.no_record_found}}</h5>
                </div>
            </div>
            <div class="btn-block" v-if="items.length > 0">
                <b-button class="btn-bordersecondary ml-auto" @click="exportFile">{{languageData.label.export}}
                </b-button>
            </div>
        </div>
        <div class="pagination-block" v-if="items.length > 0">
            <b-pagination v-model="page" :total-rows="totalRow" :per-page="perPage" align="center" @change="pageChange">
            </b-pagination>
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import moment from 'moment';
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
            requestType: String
        },
        data: function () {
            return {
                languageData: [],
                page: this.currentPage
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