<template>
    <div class="bottom-header">
        <b-container>
            <b-row>
                <b-col xl="6" lg="5" class="search-block">
                    <div class="icon-input">
                        <b-form-input
                            type="text"
                            @keypress.enter="searchMission"
                            :placeholder="$t('label.search')+' '+$t('label.mission')"
                            @focus="handleFocus()"
                            @blur="handleBlur()"
                            onfocus="this.placeholder=''"
                            id="search"
                            v-model="search"
                            onblur="this.placeholder='Search mission...'">                           
                        </b-form-input>
                        <i>
                            <img :src="$store.state.imagePath+'/assets/images/search-ic.svg'" alt="Search">
                        </i>
                    </div>
                </b-col>

                <b-col xl="6" lg="7" class="filter-block">
                    <div class="mobile-top-block">
                        <b-button class="btn btn-back" @click="handleBack">
                            <img :src="$store.state.imagePath+'/assets/images/down-arrow.svg'" alt="Back Icon">
                        </b-button>
                        <b-button class="btn btn-clear">{{$t("label.clear_all")}}</b-button>
                    </div>
                <b-list-group>
                    <b-list-group-item>
                        <AppFilterDropdown
                            :optionList="countryList"
                            :defaultText="defautCountry"
                            translationEnable= "false" 
                            @updateCall="changeCountry"
                            v-if="isComponentVisible"
                        />
                    </b-list-group-item>
                    <b-list-group-item>
                        <AppCheckboxDropdown 
                            v-if="isComponentVisible" 
                            :filterTitle="defautCity" 
                            :selectedItem="selectedCity"
                            :checkList="cityList"
                            @updateCall="changeCity"
                        />
                    </b-list-group-item>
                    <b-list-group-item>
                        <AppCheckboxDropdown 
                            v-if="isComponentVisible" 
                            :filterTitle="defautTheme" 
                            :selectedItem="selectedTheme"
                            :checkList="themeList"
                            @updateCall="changeTheme"
                        />
                    </b-list-group-item>
                    <b-list-group-item>
                        <AppCheckboxDropdown 
                            v-if="isComponentVisible" 
                            :filterTitle="defautSkill" 
                            :checkList="skillList"
                            :selectedItem="selectedSkill"
                            @updateCall="changeSkill"
                        />
                    </b-list-group-item>
                </b-list-group>
                </b-col>

                <div class="filter-icon" @click="handleFilter" @touchend.stop>
                    <img :src="$store.state.imagePath+'/assets/images/filter-ic.svg'" alt="filter">
                </div>
            </b-row>
        </b-container>
    </div>
</template>

<script>
import AppFilterDropdown from "../AppFilterDropdown";
import AppCheckboxDropdown from "../AppCheckboxDropdown";
import {filterList,missionFilterListing} from "../../services/service";
import store from "../../store";
import {eventBus} from "../../main";
export default {
    components: { AppFilterDropdown, AppCheckboxDropdown },
    name: "TheSecondaryHeader", 
    props: ['search'],
    data() {
        return {
            defautCountry: "Country",
            defautCity: "",
            defautTheme: "",
            defautSkill: "",
            countryList: [],
            cityList: [],
            themeList: [],
            skillList: [],
            filterList:[],
            selectedCity:[],
            selectedSkill:[],
            selectedTheme:[],
            form: {
                text: ""
            },
            selectedfilterParams: {
                countryId : "",
                cityId : "",
                themeId : "",
                skillId : "",
            },
            show: false,
            isComponentVisible:false,
            tagsFilter : [],

        };
    },
    methods: {
        handleFocus() {
            var b_header = document.querySelector(".bottom-header");
            b_header.classList.add("active");
        },

        removeItems(data){
            if(data.selectedType == "country"){
                data.selectedId = "";
                this.changeCountry(data)
            }
            if(data.selectedType == "city"){
                let selectedData =store.state.cityId.toString().split(',');
                var filteredCity = selectedData.filter(function(value, index, arr){
                    return value !=  data.selectedId;
                });
                this.selectedCity = filteredCity;
            }
            if(data.selectedType == "theme"){
                let selectedData =store.state.themeId.toString().split(',');
                var filteredTheme = selectedData.filter(function(value, index, arr){
                    return value !=  data.selectedId;
                });
                this.selectedTheme = filteredTheme;
            }
            if(data.selectedType == "skill"){
                let selectedData =store.state.skillId.toString().split(',');
                var filteredSkill = selectedData.filter(function(value, index, arr){
                    return value !=  data.selectedId;
                });
                this.selectedSkill = filteredSkill;
            }
        },

        handleBlur() {
            var b_header = document.querySelector(".bottom-header");
            var input_edit = document.querySelector(".search-block input");
            b_header.classList.remove("active");
            if (input_edit.value.length > 0) {
                b_header.classList.add("active");
            } else {
                b_header.classList.remove("active");
            }
        },

        handleFilter() {
            var body = document.querySelectorAll("body, html");
            body.forEach(function(e) {
                e.classList.add("open-filter");
            });
        },

        handleBack() {
            var body = document.querySelectorAll("body, html");
            body.forEach(function(e) {
                e.classList.remove("open-filter");
            });
        },

        changeCountry(country) {
            this.selectedfilterParams.countryId = country.selectedId;
            if(country.selectedId != ''){
                this.defautCountry = country.selectedVal.replace(/<\/?("[^"]*"|'[^']*'|[^>])*(>|$)/g,""); 
                this.defautCountry = this.defautCountry.replace(/[^a-zA-Z\s]+/g,'');
            } else {
                this.defautCountry = this.$i18n.t("label.country");
            }
            this.selectedfilterParams.cityId = '';
            this.selectedfilterParams.themeId = '';
            this.selectedfilterParams.skillId = '';
            this.cityList = [];
            this.themeList = [];
            this.skillList = [];
            let filters = {};
            filters.exploreMissionType = '';
            filters.exploreMissionParams = '';
            store.commit("exploreFilter",filters);
            this.$router.push({ name: 'home' })
            filterList(this.selectedfilterParams).then( response => {
                if (response) {       
                    if(response.city) {             
                        this.cityList = Object.entries(response.city);
                        this.selectedCity = [];
                    }

                    if(response.themes) {
                        this.themeList = Object.entries(response.themes);
                        this.selectedTheme = [];
                    } 

                    if(response.skill) {
                        this.skillList = Object.entries(response.skill);
                        this.selectedSkill = [];
                    }
                }
                this.$parent.searchMissions(this.search,this.selectedfilterParams);        
            });

        },

        changeCity(city) {
            this.selectedfilterParams.cityId = city;
            this.selectedfilterParams.themeId = '';
            this.themeList = [];
            this.skillList = [];
            let filters = {};
            filters.exploreMissionType = '';
            filters.exploreMissionParams = '';
            store.commit("exploreFilter",filters);
            this.$router.push({ name: 'home' })
            filterList(this.selectedfilterParams).then( response => {
                if (response) {
                    if(response.themes) {                   
                        this.themeList = Object.entries(response.themes);
                        this.selectedTheme = [];
                    }

                    if(response.skill) {
                        this.skillList = Object.entries(response.skill);
                        this.selectedSkill = []; 
                    } 
                }
                this.$parent.searchMissions(this.search,this.selectedfilterParams);
            });  
        },

        changeTheme(theme) {
            this.selectedfilterParams.themeId = theme;
            this.skillList = [];
            let filters = {};
            filters.exploreMissionType = '';
            filters.exploreMissionParams = '';
            store.commit("exploreFilter",filters);
            this.$router.push({ name: 'home' })
            filterList(this.selectedfilterParams).then( response => {
                if (response) {   
                    if(response.skill) {                 
                        this.skillList = Object.entries(response.skill);
                        this.selectedSkill = [];
                    }
                }  
                this.$parent.searchMissions(this.search,this.selectedfilterParams);              
            });   
        },

        changeSkill(skill) {
            this.selectedfilterParams.skillId = skill;
            let filters = {};
            filters.exploreMissionType = '';
            filters.exploreMissionParams = '';
            store.commit("exploreFilter",filters);
            this.$router.push({ name: 'home' }) 
            this.$parent.searchMissions(this.search,this.selectedfilterParams);    
        },

        // Filter listing
        filterListing() {
            let tags = {
                'country':[],
                'city' : [],
                'theme' :[],
                'skill' :[]
            }

            this.selectedfilterParams.countryId = store.state.countryId;
            this.selectedfilterParams.cityId = store.state.cityId;
            this.selectedfilterParams.themeId = store.state.themeId;
            this.selectedfilterParams.skillId = store.state.skillId;
            this.selectedCity = [];
            this.selectedTheme = [];
            this.selectedSkill = [];
            var i=0;
                       
            filterList(this.selectedfilterParams).then( response => {
                    if (response) { 
                        if(response.country) {
                            this.countryList = Object.entries(response.country);
                        }

                        if(response.city) {
                            this.cityList = Object.entries(response.city);
                        }

                        if(response.themes) {
                            this.themeList = Object.entries(response.themes);
                        }

                        if(response.skill) {
                            this.skillList = Object.entries(response.skill);
                        }

                        if(store.state.countryId != '') {
                                if(this.countryList) {
                                    let selectedCountryData = this.countryList.filter(function(country) {
                                        if(store.state.countryId == country[1].id){
                                            return country;
                                        }
                                    });
                                    this.defautCountry = selectedCountryData[0][1].title;
                                    tags.country[0] = selectedCountryData[0][1].id+'_'+selectedCountryData[0][1].title;
                                }
                        } else {
                                this.defautCountry = this.$i18n.t("label.country");
                        }

                        if(store.state.cityId != ''){
                            this.selectedCity = store.state.cityId.toString().split(',')
                        }

                        if(store.state.themeId != ''){
                            this.selectedTheme = store.state.themeId.toString().split(',')
                        }

                        if(store.state.skillId != ''){
                            this.selectedSkill = store.state.skillId.toString().split(',')
                        }

                        this.defautCity =  this.$i18n.t("label.city"),
                        this.defautTheme =  this.$i18n.t("label.theme"),
                        this.defautSkill = this.$i18n.t("label.skills")
                    }            
                    this.isComponentVisible =true;
            }); 
        },

        searchMission($event) {
            this.$parent.searchMissions(this.search,this.selectedfilterParams);
        },

        fetchFilters() {
            this.$emit('cmsListing',this.$route.params.slug);
        },

        clearFilter() {
            var _this = this; 
            this.selectedfilterParams.countryId = '';
            this.defautCountry = this.$i18n.t("label.country");
            this.selectedfilterParams.cityId = '';
            this.selectedfilterParams.themeId = '';
            this.selectedfilterParams.skillId = '';
            this.selectedCity = [];
            this.selectedSkill = [];
            this.selectedTheme = [];
            filterList(this.selectedfilterParams).then( response => {
                if (response) {
                    if(response.city) {  
                        this.cityList = Object.entries(response.city);
                        this.selectedCity = [];
                    } 
                }
            });  
            
        },
        clearAllFilter(){
            this.selectedfilterParams.countryId = '';
            this.selectedfilterParams.cityId = '';
            this.selectedfilterParams.themeId = '';
            this.selectedfilterParams.skillId = '';
            this.cityList = [];
            this.themeList = [];
            this.skillList = [];
            let filters = {};
            filters.exploreMissionType = '';
            filters.exploreMissionParams = '';
            store.commit("exploreFilter",filters);
            let userFilter = {};
            userFilter.search = '';
            userFilter.countryId = '';
            userFilter.cityId = '';
            userFilter.themeId = '';
            userFilter.skillId = '';
            userFilter.tags = [];
            store.commit("userFilter",userFilter);
            this.$router.push({ name: 'home' })
            
            this.$parent.getMissions();
            
            setTimeout(() => {
            this.selectedfilterParams.countryId = store.state.countryId;
            this.selectedfilterParams.cityId = store.state.cityId;
            filterList(this.selectedfilterParams).then( response => {
                    if (response) { 
                        if(response.country) {
                            this.countryList = Object.entries(response.country);
                        }

                        if(response.city) {
                            this.cityList = Object.entries(response.city);
                        }
                        if(response.themes) {
                            this.themeList = Object.entries(response.themes);
                        }

                        if(response.skill) {
                            this.skillList = Object.entries(response.skill);
                        }
                        if(store.state.countryId != '') {
                                if(this.countryList) {
                                    let selectedCountryData = this.countryList.filter(function(country) {
                                        if(store.state.countryId == country[1].id){
                                            return country;
                                        }
                                    });
                                    this.defautCountry = selectedCountryData[0][1].title;
                                }
                        } else {
                                this.defautCountry = this.$i18n.t("label.country");
                        }

                        if(store.state.cityId != ''){
                            this.selectedCity = store.state.cityId.toString().split(',')
                            console.log( this.selectedCity);
                        }
                    }            
            }); 
            }, 500); 
        } 
    },
    created() {
        var _this = this;
        eventBus.$on('clearAllFilters', (message) => {
            this.clearFilter();
        });
        eventBus.$on('setDefaultText', (message) => {
            this.defautCountry = this.$i18n.t("label.country");
        });
        eventBus.$on('setDefaultData', (message) => {        
            this.filterListing();
        });
        // Fetch Filters
        this.filterListing();
        if(store.state.search != null) {
            this.search = store.state.search;
        }
    }
};
</script>