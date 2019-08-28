<template>
    <div class="home-page inner-pages filter-header">
        <header @scroll="handleScroll">
             <ThePrimaryHeader @exploreMisison="exploreMisison" 
             @getMissions = "getMissions"
             v-if="isShownComponent" ></ThePrimaryHeader>
             <TheSecondaryHeader :search="search" :missionList="missionList" ref="secondaryHeader" 
             @storeMisisonSearch="storeSearch"
             @getMissions="getMissions"
             @clearMissionFilter="clearMissionFilter"
              v-if="isShownComponent"></TheSecondaryHeader>
        </header>
        <main>
            <b-container class="home-content-wrapper">
                <div v-if="missionList.length > 0 && isQuickAccessDisplay">
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
                    <b-button class="clear-btn" @click="clearMissionFilter">{{langauageData.label.clear_all}}</b-button>
                </div>
                </div>
                <div class="heading-section" v-if="missionList.length > 0">
                    <h2 v-if="isTotalMissionDisplay">
                        <template v-if="rows > 0">{{ langauageData.label.explore}}
                         <strong>{{rows}}</strong> 
                         <strong v-if="rows > 1" class="ml-1">{{ langauageData.label.missions}}</strong>
                         <strong v-else class="ml-1">{{ langauageData.label.mission}}</strong>
                        </template>
                    </h2>
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
                            <i class="grid icon-wrap" @click="activeView = 'gridView'" v-b-tooltip.hover :title = "langauageData.label.grid_view" v-if="missionList.length > 0">
                            <img class="img-normal" :src="$store.state.imagePath+'/assets/images/grid.svg'" alt="Down Arrow" />
                            <img class="img-rollover" :src="$store.state.imagePath+'/assets/images/grid-h.svg'" alt="Down Arrow" />
                         </i>
                        </template>
                        <GridView 
                        id="gridView"
                        :items="missionList"
                        :p:per-page="perPage"
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
                            <i class="list icon-wrap" @click="activeView = 'listView'" v-b-tooltip.hover :title = "langauageData.label.list_view" v-if="missionList.length > 0">
                            <img class="img-normal" :src="$store.state.imagePath+'/assets/images/list.svg'" alt="Down Arrow" />
                            <img class="img-rollover" :src="$store.state.imagePath+'/assets/images/list-h.svg'" alt="Down Arrow" />
                            </i>
                        </template>
                        <ListView
                        id="listView"
                        :items="missionList"
                        :p:per-page="perPage"
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
        <back-to-top bottom="68px" right="40px" :title="langauageData.label.back_to_top">
         <i class="icon-wrap">
            <img class="img-normal" :src="$store.state.imagePath+'/assets/images/down-arrow.svg'" alt="Down Arrow" />
            <img class="img-rollover" :src="$store.state.imagePath+'/assets/images/down-arrow-black.svg'" alt="Down Arrow" />
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
import constants from '../constant';

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
            langauageData : [],
            isTotalMissionDisplay : true,
            isQuickAccessDisplay : true
        };
    },

    methods: {
        storeSearch(searchString) {
            this.search = searchString
        },
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
                    var _this = this;
                    setTimeout(function(){ 
                        var labelString = 'label.'
                        var sortByValue = labelString.concat(sortBy); 
                        _this.sortByDefault = _this.langauageData.label[sortBy];
                    },200);
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
            if(store.state.exploreMissionType == ''){
                this.filterData.countryId = filterParmas.countryId;
            } else {
                this.filterData.countryId = '';
            }

            if(store.state.exploreMissionType == ''){
                this.filterData.cityId =  filterParmas.cityId;
            } else {
                this.filterData.cityId =  '';
            }

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
            this.search = '';
            this.filterData.search =  '';
            this.filterData.countryId = '';
            this.filterData.cityId =  '';
            this.filterData.themeId = '';
            this.filterData.skillId = '';
            this.filterData.tags = '';
            this.filterData.sortBy = '';
            this.sortByDefault = this.langauageData.label.sort_by;
            if(filters.parmasType) {
                filteExplore.exploreMissionType = filters.parmasType;
            }
            if(filters.parmas) {
                filteExplore.exploreMissionParams = filters.parmas;
            }
            store.commit('userFilter',this.filterData)
            store.commit('exploreFilter',filteExplore);
            this.$refs.secondaryHeader.changeSearch();
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
        this.langauageData = JSON.parse(store.state.languageLabel);
       
            this.sortByFilterSet = this.settingEnabled(constants.SORTING_MISSIONS)
        if (this.$route.params.searchParamsType){
            let filteExplore = {};
            filteExplore.exploreMissionParams  = '';
            filteExplore.exploreMissionType = this.$route.params.searchParamsType;
            if(this.$route.params.searchParams) {
                filteExplore.exploreMissionParams = this.$route.params.searchParams;
            }
            store.commit('exploreFilter',filteExplore);
            store.commit('clearFilter')
             this.getMissions();

        } else {
            let filteExplore = {};
            filteExplore.exploreMissionType = '';
            filteExplore.exploreMissionParams = '';
            store.commit('exploreFilter',filteExplore);
            // Mission listing
            this.missionFilter();
        }
        var _this = this;
        this.isTotalMissionDisplay = this.settingEnabled(constants.Total_MISSIONS_IN_PLATEFORM)
        this.isQuickAccessDisplay = this.settingEnabled(constants.QUICK_ACCESS_FILTERS)
        searchUser().then(response => {
            this.userList = response;
        });
               
        setTimeout(function(){ 
            
            _this.sortByDefault = _this.langauageData.label.sort_by;
        },200);
        window.addEventListener("scroll", this.handleScroll);
    },
    destroyed() {
        window.removeEventListener("scroll", this.handleScroll);
    }
};
</script>