<template>
    <div class="home-page inner-pages filter-header">
        <header @scroll="handleScroll">
             <ThePrimaryHeader @exploreMisison="exploreMisison" 
             @getMissions = "getMissions"
             v-if="isShownComponent" ></ThePrimaryHeader>
             <TheSecondaryHeader :search="search" ref="secondaryHeader" 
             @getMissions="getMissions"
              v-if="isShownComponent"></TheSecondaryHeader>
        </header>
        <main>
            <b-container class="home-content-wrapper">
                <div>
                <div class="chip-container" v-if="tags != ''">
                    <span v-for="(item , i) in tags.country" >
                        <AppCustomChip :textVal="item" :tagId ="i" type ="country" 
                        @updateCall="changeTag"
                        />
                    </span>
                    <span v-for="(item , i) in tags.city" >
                        <AppCustomChip :textVal="item" :tagId ="i" type ="city"
                        @updateCall="changeTag"
                        />
                    </span>
                    <span v-for="(item , i) in tags.theme" >
                        <AppCustomChip :textVal="item" :tagId ="i" type ="theme"
                        @updateCall="changeTag"
                        />
                    </span>
                    <span v-for="(item , i) in tags.skill" >
                        <AppCustomChip :textVal="item" :tagId ="i" type ="skill"
                        @updateCall="changeTag"
                        />
                    </span>
                    <b-button class="clear-btn" @click="clearMissionFilter">{{$t("label.clear_all")}}</b-button>
                </div>
                </div>
                <div class="heading-section">
                    <h2><template v-if="rows > 0">{{ $t("label.explore")}} <strong>{{rows}} {{ $t("label.missions")}}</strong></template></h2>
                    <div class="right-section" v-if="sortByFilterSet">
                        <AppCustomDropdown
                        :optionList="sortByOptions"
                        :defaultText="sortByDefault"
                        translationEnable= "true"
                        @updateCall="updateSortTitle"
                        />
                    </div>
                </div>
                <!-- Tabing grid view and list view start -->
                <b-tabs class="view-tab">
                <!-- grid view -->
                    <b-tab class="grid-tab-content">
                        <template slot="title">
                        <i class="grid" @click="activeView = 'gridView'">
                            <svg
                            version="1.1"
                            id="Capa_1"
                            xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px"
                            y="0px"
                            viewBox="0 0 174.239 174.239"
                            style="enable-background:new 0 0 174.239 174.239;"
                            xml:space="preserve"
                            >
                            <g><g>
                            <path d="M174.239,174.239H96.945V96.945h77.294V174.239z M111.88,159.305h47.425V111.88H111.88V159.305z"></path>
                            </g><g>
                            <path d="M77.294,174.239H0V96.945h77.294V174.239z M14.935,159.305H62.36V111.88H14.935V159.305z"></path>
                            </g><g>
                            <path d="M174.239,77.294H96.945V0h77.294V77.294z M111.88,62.36h47.425V14.935H111.88V62.36z"></path>
                            </g><g>
                            <path d="M77.294,77.294H0V0h77.294V77.294z M14.935,62.36H62.36V14.935H14.935V62.36z"></path>
                            </g></g>
                            </svg>
                        </i>
                        </template>
                        <GridView 
                        id="gridView"
                        :items="missionList"
                        :per-page="perPage"
                        :current-page="currentPage"
                        v-if="isShownComponent"
                        :userList = "userList"
                        @getMissions = "getMissions"
                        small
                        />
                    </b-tab>
                        <!-- list view -->
                    <b-tab class="list-tab-content">
                        <template slot="title">
                        <i class="list" @click="activeView = 'listView'">
                            <svg
                            id="Layer_1"
                            data-name="Layer 1"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 22 22"
                            >
                            <path id="List" class="cls-1" d="M0,0H22V2H0ZM0,10H22v2H0ZM0,20H22v2H0Z"></path>
                            </svg>
                        </i>
                        </template>
                        <ListView
                        id="listView"
                        :items="missionList"
                        :per-page="perPage"
                        :current-page="currentPage"
                        v-if="isShownComponent"
                        :userList = "userList"
                        @getMissions = "getMissions"
                        small
                        />
                    </b-tab>          
                </b-tabs>
            <!-- Tabing grid view and list view end -->
            <!-- Pagination start -->
                <div class="pagination-block" v-if="rows > 0">
                    <b-pagination
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    align="center"
                    :simple="false"
                    :aria-controls= "activeView"
                    @change="pageChange">    
                    </b-pagination>
                </div>
            <!-- Pagination end -->
            </b-container>
        </main>
        <footer>
            <TheSecondaryFooter></TheSecondaryFooter>
        </footer>
        <back-to-top bottom="68px" right="40px" :title="$t('label.back_to_top')">
        <i>
            <svg
                version="1.1"
                id="Capa_1"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                x="0px"
                y="0px"
                width="451.847px"
                height="451.847px"
                viewBox="0 0 451.847 451.847"
                style="enable-background:new 0 0 451.847 451.847;"
                xml:space="preserve">
            <g>
            <path
            d="M225.923,354.706c-8.098,0-16.195-3.092-22.369-9.263L9.27,151.157c-12.359-12.359-12.359-32.397,0-44.751
            c12.354-12.354,32.388-12.354,44.748,0l171.905,171.915l171.906-171.909c12.359-12.354,32.391-12.354,44.744,0
            c12.365,12.354,12.365,32.392,0,44.751L248.292,345.449C242.115,351.621,234.018,354.706,225.923,354.706z"
            ></path>
            </g>
            </svg>
        </i>
        </back-to-top>
    </div>
</template>

<script>
import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
import TheSecondaryHeader from "../components/Layouts/TheSecondaryHeader";
import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
import GridView from "../components/MissionGridView";
import ListView from "../components/MissionListView";
import AppCustomDropdown from "../components/AppCustomDropdown";
import AppCustomChip from "../components/AppCustomChip";
import axios from "axios";
import store from '../store';
import {missionListing,missionFilterListing,searchUser} from '../services/service';

export default {
    components: {
        ThePrimaryHeader,
        TheSecondaryHeader,
        TheSecondaryFooter,
        GridView,
        ListView,
        AppCustomDropdown,
        AppCustomChip
    },

    name: "home",

    data() {
        return {
            rows: 0,
            perPage:10,
            currentPage: 1,
            sortByOptions: [
                ["newest","newest"],
                ["oldest","oldest"],
                ["lowest_available_seats","lowest_available_seats"],
                ["highest_available_seats","highest_available_seats"],
                ["my_favourite","my_favourite"],
                ["deadline","deadline"]],
            sortByDefault: '',
            missionList : [],
            activeView:"gridView",
            filter:[],
            search : "",
            selectedfilterParams: {
                    countryId : "",
                    cityId : "",
                    themeId : "",
                },
            isShownComponent :false,
            filterData : {
                "search" : "",
                "countryId": "",
                "cityId": "",
                "themeId": "",
                "skillId": "",
                "exploreMissionType" : "",
                "exploreMissionParams" : "",
                "tags" : [],
                "sortBy" : "",
            },
            tags:"",
            sortByFilterSet : true,
            userList :[],
        };
    },

    methods: {

        handleScroll() {
            var body = document.querySelector("body");
            var bheader = document.querySelector("header");
            var bheader_top = bheader.offsetHeight;
            if (window.scrollY > bheader_top) {
                body.classList.add("small-header");
            } else {
                body.classList.remove("small-header");
            }
        },

        updateSortTitle(value) {
            store.commit("sortByFilter",value.selectedId)
            this.sortByDefault = value.selectedVal;
            this.getMissions();
        },
        //Mission listing
        async getMissions(parmas = ""){

            let filter = {};
            filter.page = this.currentPage
            filter.search = store.state.search 
            filter.countryId = store.state.countryId
            filter.cityId = store.state.cityId 
            filter.themeId = store.state.themeId
            filter.skillId = store.state.skillId 
            filter.exploreMissionType = store.state.exploreMissionType
            filter.exploreMissionParams = store.state.exploreMissionParams
            filter.sortBy = store.state.sortBy 
            filter.addLoader = parmas       
            
            await missionListing(filter).then( response => {
                if (response.data) {
                    this.missionList = response.data;    
                } else {
                    this.missionList = [];
                }  
                if (response.pagination) {
                    this.rows = response.pagination.total;
                    this.perPage = response.pagination.per_page;
                    this.currentPage = response.pagination.current_page;
                } else {
                    this.rows = 0;
                    if (this.currentPage != 1) {
                        this.currentPage = 1;
                        this.getMissions();
                    }
                }          
                this.isShownComponent = true;
                if(store.state.search != null) {
                    this.search = store.state.search;
                }
                if(store.state.tags != null) {
                    this.tags = JSON.parse(store.state.tags);
                }
                if(store.state.sortBy != null && store.state.sortBy != '') {
                    var sortBy = store.state.sortBy;
                    var sortByFilter = sortBy[0].toUpperCase() + sortBy.slice(1);
                    var _this = this;
                    setTimeout(function(){ 
                        _this.sortByDefault =  sortByFilter.split('_').join(' ');
                    },300);
                }
              
            }); 
        },

        async missionFilter(){
            await missionFilterListing().then( response => {
                this.getMissions();
            }); 
        },

        pageChange (page) {
            //Change pagination
            this.currentPage = page;
            this.getMissions();
        },

        searchMissions(searchParams,filterParmas) {
            this.filterData.search =  searchParams;
            this.filterData.countryId = filterParmas.countryId;
            this.filterData.cityId =  filterParmas.cityId;
            this.filterData.themeId = filterParmas.themeId;
            this.filterData.skillId = filterParmas.skillId;
            this.filterData.tags = filterParmas.tags;
             this.filterData.sortBy = '';
            if(store.state.sortBy != null){
                this.filterData.sortBy = store.state.sortBy;
            }
            store.commit('userFilter',this.filterData)
            this.getMissions(); 
        },
        
        changeView(currentView){
            //Change View 
            this.activeView = currentView;
        },

        exploreMisison(filters) {
            let filteExplore = {};
            filteExplore.exploreMissionType = '';
            filteExplore.exploreMissionParams  = '';

            this.filterData.search =  '';
            this.filterData.countryId = '';
            this.filterData.cityId =  '';
            this.filterData.themeId = '';
            this.filterData.skillId = '';
            this.filterData.tags = '';
            this.filterData.sortBy = store.state.sortBy;

            if(filters.parmasType) {
                filteExplore.exploreMissionType = filters.parmasType;
            }
            if(filters.parmas) {
                filteExplore.exploreMissionParams = filters.parmas;
            }
            store.commit('userFilter',this.filterData)
            store.commit('exploreFilter',filteExplore);
            this.getMissions(); 
        },
        changeTag(data){
            this.$refs.secondaryHeader.removeItems(data);
        },
        clearMissionFilter(){
          this.$refs.secondaryHeader.clearAllFilter();  
        }
    },
    created() { 
        let filterSetting = JSON.parse(store.state.tenantSetting);
        if(filterSetting.sorting_missions != 1){
            this.sortByFilterSet = false;
        }
        if (this.$route.params.searchParamsType){
            let filteExplore = {};
            filteExplore.exploreMissionParams  = '';
            filteExplore.exploreMissionType = this.$route.params.searchParamsType;
            if(this.$route.params.searchParams) {
                filteExplore.exploreMissionParams = this.$route.params.searchParams;
            }
            store.commit('exploreFilter',filteExplore);

        } else {
            let filteExplore = {};
            filteExplore.exploreMissionType = '';
            filteExplore.exploreMissionParams = '';
            store.commit('exploreFilter',filteExplore);
        }
        var _this = this;
        // Mission listing
        this.missionFilter();
        searchUser().then(response => {
            this.userList = response;
         });
               
        setTimeout(function(){ 
            _this.sortByDefault = _this.$i18n.t("label.sort_by");
        },200);
        window.addEventListener("scroll", this.handleScroll);
    },
    destroyed() {
        window.removeEventListener("scroll", this.handleScroll);
    }
};
</script>