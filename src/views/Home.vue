<template>
    <div class="home-page inner-pages filter-header">
        <header @scroll="handleScroll">
            <ThePrimaryHeader @exploreMisison="exploreMisison" @getMissions="getMissions" v-if="isShownComponent">
            </ThePrimaryHeader>
            <TheSecondaryHeader :search="search" :missionList="missionList" ref="secondaryHeader"
                @storeMisisonSearch="storeSearch" @getMissions="getMissions" @clearMissionFilter="clearMissionFilter"
                v-if="isShownComponent"></TheSecondaryHeader>
        </header>
        <main>
                <div v-bind:class="{ 'content-loader-wrap': true, 'loader-active': isAjaxCall ,'fixed-loader' : true}">
                    <div class="content-loader"></div>
                </div>
                <b-container class="home-content-wrapper">
                    <div v-if="missionList.length > 0 && isQuickAccessDisplay">
                        <div class="chip-container" v-if="tags != ''">
                            <span v-for="(item , i) in tags.country" v-if="isCountrySelectionSet" :key=i>
                                <AppCustomChip :textVal="item" :tagId="i" type="country" @updateCall="changeTag" />
                            </span>
                            <span v-for="(item , i) in tags.city" :key=i>
                                <AppCustomChip :textVal="item" :tagId="i" type="city" @updateCall="changeTag" />
                            </span>
                            <span v-for="(item , i) in tags.theme" v-if="isThemeDisplay" :key=i>
                                <AppCustomChip :textVal="item" :tagId="i" type="theme" @updateCall="changeTag" />
                            </span>
                            <span v-for="(item , i) in tags.skill" v-if="isSkillDisplay" :key=i>
                                <AppCustomChip :textVal="item" :tagId="i" type="skill" @updateCall="changeTag" />
                            </span>
                            <b-button class="clear-btn"
                                v-if="isCountrySelectionSet || tags.city || (tags.theme && isThemeDisplay) || (tags.skill && isSkillDisplay)"
                                @click="clearMissionFilterData">{{languageData.label.clear_all}}</b-button>
                        </div>
                    </div>
                    <div v-bind:class="{ 'heading-section': true, 
                        'justify-content-end' : !isTotalMissionDisplay
                                            }" v-if="missionList.length > 0">
                        <h2 v-if="isTotalMissionDisplay">
                            <template v-if="rows > 0">{{ languageData.label.explore}}
                                <strong>{{rows}}</strong>
                                <strong v-if="rows > 1" class="ml-1">{{ languageData.label.missions}}</strong>
                                <strong v-else class="ml-1">{{ languageData.label.mission}}</strong>
                            </template>
                        </h2>
                        <div class="right-section" v-if="sortByFilterSet">
                            <AppCustomDropdown :optionList="sortByOptions" :defaultText="sortByDefault"
                                translationEnable="true" @updateCall="updateSortTitle" />
                        </div>
                    </div>
                    <!-- Tabing grid view and list view start -->
                    
                    <b-tabs class="view-tab" v-model="tabNumber">
                    
                        <!-- grid view -->
                        <b-tab class="grid-tab-content" @click="changeCurrentView(0)">
                            <template slot="title">
                                <i class="grid icon-wrap" @click="activeView = 'gridView'" v-b-tooltip.hover.bottom
                                    :title="languageData.label.grid_view" v-if="missionList.length > 0">
                                    <img class="img-normal" :src="$store.state.imagePath+'/assets/images/grid.svg'"
                                        alt="Down Arrow" />
                                    <img class="img-rollover" :src="$store.state.imagePath+'/assets/images/grid-h.svg'"
                                        alt="Down Arrow" />
                                </i>
                            </template>
                            <GridView id="gridView" :items="missionList" :p:per-page="perPage" :current-page="currentPage"
                                :relatedMission=relatedMission
                                v-if="isShownComponent" :userList="userList" @getMissions="getMissions" small />
                        </b-tab>
                        <!-- list view -->
                        <b-tab class="list-tab-content" @click="changeCurrentView(1)">
                            <template slot="title">
                                <i class="list icon-wrap" @click="activeView = 'listView'" v-b-tooltip.hover.bottom
                                    :title="languageData.label.list_view" v-if="missionList.length > 0">
                                    <img class="img-normal" :src="$store.state.imagePath+'/assets/images/list.svg'"
                                        alt="Down Arrow" />
                                    <img class="img-rollover" :src="$store.state.imagePath+'/assets/images/list-h.svg'"
                                        alt="Down Arrow" />
                                </i>
                            </template>
                            <ListView id="listView" :items="missionList" :per-page="perPage" :current-page="currentPage"
                                v-if="isShownComponent" :userList="userList" @getMissions="getMissions" small />
                        </b-tab>

                    </b-tabs>
                    
                    <!-- Tabing grid view and list view end -->
                    <!-- Pagination start -->
                    <div class="pagination-block" v-if="rows > 0 && totalPages > 1">
                        <b-pagination v-model="currentPage" :total-rows="rows" :per-page="perPage" align="center"
                            :simple="false" :aria-controls="activeView" @change="pageChange">
                        </b-pagination>
                    </div>
                    <!-- Pagination end -->
                </b-container>
        </main>
        <footer>
            <TheSecondaryFooter></TheSecondaryFooter>
        </footer>
        <back-to-top bottom="50px" right="40px" :title="languageData.label.back_to_top">
            <i class="icon-wrap">
                <img class="img-normal" :src="$store.state.imagePath+'/assets/images/down-arrow.svg'"
                    alt="Down Arrow" />
                <img class="img-rollover" :src="$store.state.imagePath+'/assets/images/down-arrow-black.svg'"
                    alt="Down Arrow" />
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
    import store from '../store';
    import {
        missionListing,
        missionFilterListing,
        searchUser
    } from '../services/service';
    import constants from '../constant';
    import { setTimeout } from 'timers';

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
                relatedMission: false,
                perPage: 10,
                currentPage: 1,
                sortByOptions: [
                    ["newest", "newest"],
                    ["oldest", "oldest"],
                    ["lowest_available_seats", "lowest_available_seats"],
                    ["highest_available_seats", "highest_available_seats"],
                    ["my_favourite", "my_favourite"],
                    ["deadline", "deadline"]
                ],
                sortByDefault: '',
                missionList: [],
                activeView: "gridView",
                filter: [],
                search: "",
                selectedfilterParams: {
                    countryId: "",
                    cityId: "",
                    themeId: "",
                },
                isShownComponent: false,
                filterData: {
                    "search": "",
                    "countryId": "",
                    "cityId": "",
                    "themeId": "",
                    "skillId": "",
                    "exploreMissionType": "",
                    "exploreMissionParams": "",
                    "tags": [],
                    "sortBy": "",
                    "currentView" : 0
                },
                tabNumber : 0,
                tags: "",
                sortByFilterSet: true,
                userList: [],
                languageData: [],
                isTotalMissionDisplay: true,
                isQuickAccessDisplay: true,
                isThemeDisplay: true,
                isSkillDisplay: true,
                isCountrySelectionSet: false,
                totalPages: 0,
                defaultCountry: 0,
                isAjaxCall :true
            };
        },

        methods: {
            storeSearch(searchString) {
                this.search = searchString
            },
            handleScroll() {
                let body = document.querySelector("body");
                let bheader = document.querySelector("header");
                let bheaderTop = bheader.offsetHeight;
                if (window.scrollY > bheaderTop) {
                    body.classList.add("small-header");
                } else {
                    body.classList.remove("small-header");
                }
            },

            updateSortTitle(value) {
                store.commit("sortByFilter", value.selectedId)
                this.sortByDefault = value.selectedVal;
                this.getMissions();
            },
            //Mission listing
            async getMissions(parmas = "") {
                if (store.state.clearFilterSet == "") {
                    this.isAjaxCall = true
                }
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
                filter.currentView = this.tabNumber
                filter.addLoader = parmas
                await missionListing(filter).then(response => {
                    if (response.data) {
                        this.missionList = response.data;
                    } else {
                        this.missionList = [];
                    }
                    if (response.pagination) {
                        this.rows = response.pagination.total;
                        this.perPage = response.pagination.per_page;
                        this.currentPage = response.pagination.current_page;
                        this.totalPages = response.pagination.total_pages;
                    } else {
                        this.rows = 0;
                        if (this.currentPage != 1) {
                            this.currentPage = 1;
                            this.getMissions();
                        }
                    }

                    this.isShownComponent = true;
                    this.isAjaxCall = false
                    if (store.state.search != null) {
                        this.search = store.state.search;
                    }
                    if (store.state.tags != null) {
                        this.tags = JSON.parse(store.state.tags);
                    }
                    if (store.state.sortBy != null && store.state.sortBy != '') {
                        let sortBy = store.state.sortBy;
                        
                        setTimeout(() => {
                            this.sortByDefault = this.languageData.label[sortBy];
                        }, 200);
                    }

                });
            },

            async missionFilter() {

                await missionFilterListing().then(() => {
                    this.tabNumber = store.state.currentView
                    this.getMissions();
                });
            },
            changeCurrentView(number) {
                alert(number)
                this.tabNumber = number
                store.commit('changeCurrentView' ,number)
                this.getMissions();
            },

            pageChange(page) {
                //Change pagination
                setTimeout(() => {
                    window.scrollTo({
                        'behavior': 'smooth',
                        'top': 0
                    }, 0);
                });
                this.currentPage = page;
                this.getMissions();
            },

            searchMissions(searchParams, filterParmas) {

                this.filterData.search = searchParams;
                if (store.state.exploreMissionType == '') {
                    this.filterData.countryId = filterParmas.countryId;
                } else {
                    this.filterData.countryId = '';
                }

                if (store.state.exploreMissionType == '') {
                    this.filterData.cityId = filterParmas.cityId;
                } else {
                    this.filterData.cityId = '';
                }

                this.filterData.themeId = filterParmas.themeId;
                this.filterData.skillId = filterParmas.skillId;
                this.filterData.tags = filterParmas.tags;
                this.filterData.sortBy = '';
                this.filterData.currentView = this.tabNumber;
                if (store.state.sortBy != null) {
                    this.filterData.sortBy = store.state.sortBy;
                }
                store.commit('userFilter', this.filterData)
                this.getMissions();
            },

            changeView(currentView) {
                //Change View 
                this.activeView = currentView;
            },

            exploreMisison(filters) {
                let filteExplore = {};
                filteExplore.exploreMissionType = '';
                filteExplore.exploreMissionParams = '';
                this.search = '';
                this.filterData.search = '';
                this.filterData.countryId = '';
                this.filterData.cityId = '';
                this.filterData.themeId = '';
                this.filterData.skillId = '';
                this.filterData.tags = '';
                this.filterData.sortBy = '';
                this.sortByDefault = this.languageData.label.sort_by;
                if (filters.parmasType) {
                    filteExplore.exploreMissionType = filters.parmasType;
                }
                if (filters.parmas) {
                    filteExplore.exploreMissionParams = filters.parmas;
                }
                store.commit('userFilter', this.filterData)
                store.commit('exploreFilter', filteExplore);
                this.$refs.secondaryHeader.changeSearch();
                this.getMissions();
            },
            changeTag(data) {
                if (data.selectedType == "country" && data.selectedId == store.state.defaultCountryId) {
                    return
                }
                this.$refs.secondaryHeader.removeItems(data);
            },
            clearMissionFilter() {
                this.$refs.secondaryHeader.clearAllFilter();
            },
            clearMissionFilterData() {
                document.body.classList.add("loader-enable");
                store.commit('clearFilterClick', 'true');
                this.$refs.secondaryHeader.clearAllFilter();
                document.body.classList.remove("loader-enable");
                store.commit('clearFilterClick', '');
            }
        },
        created() {
           this.languageData = JSON.parse(store.state.languageLabel);
            this.sortByFilterSet = this.settingEnabled(constants.SORTING_MISSIONS)

            if (this.$route.params.searchParamsType) {
                let filteExplore = {};
                filteExplore.exploreMissionParams = '';
                filteExplore.exploreMissionType = this.$route.params.searchParamsType;
                if (this.$route.params.searchParams) {
                    filteExplore.exploreMissionParams = this.$route.params.searchParams;
                }
                store.commit('exploreFilter', filteExplore);
                store.commit('clearFilter')
                this.getMissions();

            } else {

                let filteExplore = {};
                filteExplore.exploreMissionType = '';
                filteExplore.exploreMissionParams = '';
                store.commit('exploreFilter', filteExplore);
                // Mission listing
                this.missionFilter();
            }
            
            
            this.isTotalMissionDisplay = this.settingEnabled(constants.Total_MISSIONS_IN_PLATEFORM)
            this.isQuickAccessDisplay = this.settingEnabled(constants.QUICK_ACCESS_FILTERS)
            this.isThemeDisplay = this.settingEnabled(constants.THEMES_ENABLED);
            this.isSkillDisplay = this.settingEnabled(constants.SKILLS_ENABLED);
            this.isCountrySelectionSet = this.settingEnabled(constants.IS_COUNTRY_SELECTION);
            this.defaultCountry = store.state.defaultCountryId
             this.activeView = 'listView'
            searchUser().then(response => {
                this.userList = response;
            });

            setTimeout(() => {
                this.sortByDefault = this.languageData.label.sort_by;
            }, 200);
            window.addEventListener("scroll", this.handleScroll);
        },
        destroyed() {
            window.removeEventListener("scroll", this.handleScroll);
        }
    };
</script>