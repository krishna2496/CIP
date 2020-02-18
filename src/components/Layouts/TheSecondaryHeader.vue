<template>
    <div
            v-bind:class="[handleFilterCount()]"
    >
        <b-container>
            <b-row>
                <b-col xl="6" lg="5" class="search-block">
                    <div class="icon-input">
                        <b-form-input type="text" v-on:keyup.enter="searchMission" :placeholder="searchPlaceHolder"
                                      @focus="handleFocus()" @blur="handleBlur()" v-model="searchString" id="search"
                                      @keyup="searchMissionString">
                        </b-form-input>
                        <i>
                            <img :src="$store.state.imagePath+'/assets/images/search-ic.svg'" alt="Search">
                        </i>
                    </div>
                    <i class="clear-btn" @click="clearSearchFilter">

                        <img :src="$store.state.imagePath+'/assets/images/cross-ic.svg'"
                             :title="languageData.label.clear_search" alt="clear" />
                    </i>
                </b-col>
                <b-col xl="6" lg="7" class="filter-block"  v-if="quickAccessFilterSet">
                    <div class="mobile-top-block">
                        <b-button class="btn btn-back" @click="handleBack">
                            <img :src="$store.state.imagePath+'/assets/images/down-arrow.svg'" alt="Back Icon">
                        </b-button>
                        <b-button class="btn btn-clear" @click="clearMissionFilters">{{languageData.label.clear_all}}
                        </b-button>
                    </div>

                    <b-list-group>
                        <b-list-group-item v-if="isCountrySelectionSet">
                            <AppFilterDropdown :optionList="countryList" :defaultText="defautCountry"
                                               translationEnable="false" @updateCall="changeCountry" v-if="isComponentVisible" />
                        </b-list-group-item>
                        <b-list-group-item>
                            <AppCheckboxDropdown v-if="isComponentVisible" :filterTitle="defautCity"
                                                 :selectedItem="selectedCity" :checkList="cityList" @updateCall="changeCity" />
                        </b-list-group-item>
                        <b-list-group-item v-if="isThemeDisplay">
                            <AppCheckboxDropdown v-if="isComponentVisible" :filterTitle="defautTheme"
                                                 :selectedItem="selectedTheme" :checkList="themeList" @changeParmas="changeThemeParmas"
                                                 @updateCall="changeTheme" />
                        </b-list-group-item>
                        <b-list-group-item v-if="isSkillDisplay">
                            <AppCheckboxDropdown v-if="isComponentVisible" :filterTitle="defautSkill"
                                                 :checkList="skillList" :selectedItem="selectedSkill" @changeParmas="changeSkillParmas"
                                                 @updateCall="changeSkill" />
                        </b-list-group-item>
                    </b-list-group>
                </b-col>

                <div class="filter-icon" @click="handleFilter" @click.stop v-if="quickAccessFilterSet">
                    <img :src="$store.state.imagePath+'/assets/images/filter-ic.svg'" alt="filter">
                </div>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    import AppFilterDropdown from "../AppFilterDropdown";
    import AppCheckboxDropdown from "../AppCheckboxDropdown";
    import {
        filterList
    } from "../../services/service";
    import store from "../../store";
    import {
        eventBus
    } from "../../main";
    import constants from '../../constant';

    export default {
        components: {
            AppFilterDropdown,
            AppCheckboxDropdown
        },
        name: "TheSecondaryHeader",
        props: [
            'search',
            'missionList'
        ],
        data() {
            return {
                searchPlaceHolder: '',
                defautCountry: "Country",
                defautCity: "",
                defautTheme: "",
                defautSkill: "",
                countryList: [],
                cityList: [],
                themeList: [],
                skillList: [],
                filterList: [],
                selectedCity: [],
                selectedSkill: [],
                selectedTheme: [],
                form: {
                    text: ""
                },
                selectedfilterParams: {
                    countryId: "",
                    cityId: "",
                    themeId: "",
                    skillId: "",
                    tags: [],
                    sortBy: "",
                    search: "",
                    exploreMissionType : '',
                    exploreMissionParams :''
                },
                show: false,
                isComponentVisible: false,
                tagsFilter: [],
                quickAccessFilterSet: true,
                isThemeDisplay: true,
                isSkillDisplay: true,
                isCountryChange: false,
                isCityChange: false,
                isThemeChange: false,
                isCountrySelectionSet: false,
                searchString: this.search,
                languageData: [],
            };
        },
        mounted() {
            let mobileFilter = document.querySelector(".filter-block");
            if(mobileFilter != null){
                mobileFilter.addEventListener("click", function (e) {
                    if (window.innerWidth < 992) {
                        e.stopPropagation();
                    }
                });
            }
        },
        methods: {
            changeThemeParmas() {
                this.isCountryChange = false;
                this.isCityChange = false;
            },

            changeSearch() {
                this.searchString = '';
                this.selectedfilterParams.search = '';
                this.filterSearchListing();
            },

            changeSkillParmas() {
                this.isCountryChange = false;
                this.isCityChange = false;
                this.isThemeChange = false;
            },

            searchMissionString() {
                this.$emit('storeMisisonSearch', this.searchString);
            },

            handleFocus() {
                this.searchPlaceHolder = '';
                let b_header = document.querySelector(".bottom-header");
                b_header.classList.add("active");
            },

            removeItems(data) {
                if (data.selectedType == "country") {
                    data.selectedId = "";
                    this.changeCountry(data)
                }
                if (data.selectedType == "city") {
                    this.isCountryChange = false;
                    this.isCityChange = false;
                    this.isThemeChange = false;
                    let selectedData = store.state.cityId.toString().split(',');
                    let filteredCity = selectedData.filter((value) =>  {
                        return value != data.selectedId;
                    });
                    this.selectedCity = filteredCity;
                    this.selectedTheme = [];
                    this.selectedSkill = [];
                }
                if (data.selectedType == "theme") {
                    this.isCountryChange = false;
                    this.isCityChange = false;
                    this.isThemeChange = false;
                    let selectedData = store.state.themeId.toString().split(',');
                    let filteredTheme = selectedData.filter((value) => {
                        return value != data.selectedId;
                    });
                    this.selectedSkill = [];
                    this.selectedTheme = filteredTheme;

                }
                if (data.selectedType == "skill") {
                    let selectedData = store.state.skillId.toString().split(',');
                    let filteredSkill = selectedData.filter( (value) => {
                        return value != data.selectedId;
                    });
                    this.selectedSkill = filteredSkill;
                }
            },

            handleBlur() {
                this.searchPlaceHolder = this.languageData.label.search + ' ' + this.languageData.label.mission;
                let b_header = document.querySelector(".bottom-header");
                let input_edit = document.querySelector(".search-block input");
                b_header.classList.remove("active");
                if (input_edit.value.length > 0) {
                    b_header.classList.add("active");
                } else {
                    b_header.classList.remove("active");
                }
            },

            handleFilter() {
                let body = document.querySelectorAll("body, html");
                body.forEach(function (e) {
                    e.classList.add("open-filter");
                });
            },

            handleBack() {
                let body = document.querySelectorAll("body, html");
                body.forEach(function (e) {
                    e.classList.remove("open-filter");
                });
            },
            handleFilterCount() {
                let returnData = [];

                if(this.searchString != '') {
                    returnData.push('active')
                } else {
                    returnData = [];
                }

                returnData.push('bottom-header')
                let filterCount = document.querySelectorAll(
                    ".filter-block .list-group-item"
                ).length;

                if (filterCount != null) {
                    if (filterCount == 3) {
                        returnData.push('three-filters')
                    } else if (filterCount == 2) {
                        returnData.push('two-filters')
                    } else if (filterCount == 1) {
                        returnData.push('one-filter')
                    }else if( filterCount == 0){
                        returnData.push('zero-filter')
                    }
                }
                return returnData;
            },

            async changeCountry(country) {
                this.isCountryChange = true;
                this.selectedfilterParams.countryId = country.selectedId;
                if (country.selectedId != '') {
                    this.defautCountry = country.selectedVal.replace(/<\/?("[^"]*"|'[^']*'|[^>])*(>|$)/g, "");
                    this.defautCountry = this.defautCountry.replace(/[^a-zA-Z\s]+/g, '');
                } else {
                    this.defautCountry = this.languageData.label.country;
                }
                this.selectedfilterParams.cityId = '';
                this.selectedfilterParams.themeId = '';
                this.selectedfilterParams.skillId = '';
                this.selectedfilterParams.exploreMissionType = store.state.exploreMissionType
                this.selectedfilterParams.exploreMissionParams = store.state.exploreMissionParams;
                this.cityList = [];
                this.themeList = [];
                this.skillList = [];
                // let filters = {};
                // filters.exploreMissionType = '';
                // filters.exploreMissionParams = '';
                // store.commit("exploreFilter", filters);
                // this.$router.push({
                //     name: 'home'
                // })
                await filterList(this.selectedfilterParams).then(response => {
                    if (response) {
                        if (response.city) {
                            this.cityList = Object.entries(response.city);
                            this.selectedCity = [];
                        }

                        if (response.themes) {
                            this.themeList = Object.entries(response.themes);
                            this.selectedTheme = [];
                        }

                        if (response.skill) {
                            this.skillList = Object.entries(response.skill);
                            this.selectedSkill = [];
                        }
                    }
                    this.$parent.searchMissions(this.search, this.selectedfilterParams);
                });
                this.isCountryChange = false;
            },

            async changeCity(city) {
                this.isCityChange = true;
                if (!this.isCountryChange) {
                    this.selectedfilterParams.cityId = city;
                    this.selectedfilterParams.themeId = '';
                    this.selectedfilterParams.skillId = '';
                    this.selectedfilterParams.exploreMissionType = store.state.exploreMissionType
                    this.selectedfilterParams.exploreMissionParams = store.state.exploreMissionParams;
                    this.themeList = [];
                    this.skillList = [];
                    // let filters = {};
                    // filters.exploreMissionType = '';
                    // filters.exploreMissionParams = '';
                    // store.commit("exploreFilter", filters);
                    // this.$router.push({
                    //     name: 'home'
                    // })
                    await filterList(this.selectedfilterParams).then(response => {
                        if (response) {
                            if (response.themes) {
                                this.themeList = Object.entries(response.themes);
                                this.selectedTheme = [];
                            }

                            if (response.skill) {
                                this.skillList = Object.entries(response.skill);
                                this.selectedSkill = [];
                            }
                        }
                        this.$parent.searchMissions(this.search, this.selectedfilterParams);
                    });
                    this.isCityChange = false;
                }
            },

            async changeTheme(theme) {
                this.isThemeChange = true;
                if (!this.isCountryChange && !this.isCityChange) {
                    this.selectedfilterParams.themeId = theme;
                    this.selectedfilterParams.skillId = '';
                    this.selectedfilterParams.exploreMissionType = store.state.exploreMissionType
                    this.selectedfilterParams.exploreMissionParams = store.state.exploreMissionParams;
                    this.skillList = [];
                    this.selectedSkill = [];
                    // let filters = {};
                    // filters.exploreMissionType = '';
                    // filters.exploreMissionParams = '';
                    // store.commit("exploreFilter", filters);
                    // this.$router.push({
                    //     name: 'home'
                    // })
                    await filterList(this.selectedfilterParams).then(response => {
                        if (response) {
                            if (response.skill) {
                                this.skillList = Object.entries(response.skill);
                                this.selectedSkill = [];
                            }
                        }
                        this.$parent.searchMissions(this.search, this.selectedfilterParams);
                    });
                    this.isThemeChange = false;
                }
            },

            async changeSkill(skill) {
                if (!this.isCountryChange && !this.isCityChange && !this.isThemeChange) {
                    this.selectedfilterParams.skillId = skill;
                    this.selectedfilterParams.exploreMissionType = store.state.exploreMissionType
                    this.selectedfilterParams.exploreMissionParams = store.state.exploreMissionParams;
                    // let filters = {};
                    // filters.exploreMissionType = '';
                    // filters.exploreMissionParams = '';
                    // store.commit("exploreFilter", filters);
                    // this.$router.push({
                    //     name: 'home'
                    // })
                    this.$parent.searchMissions(this.search, this.selectedfilterParams);
                }
            },

            // Filter listing
            filterListing() {
                let tags = {
                    'country': [],
                    'city': [],
                    'theme': [],
                    'skill': []
                }

                setTimeout( () => {
                    this.defautCity = this.languageData.label.city,
                        this.defautTheme = this.languageData.label.theme,
                        this.defautSkill = this.languageData.label.skills
                }, 500)

                this.selectedfilterParams.countryId = store.state.countryId;
                this.selectedfilterParams.cityId = store.state.cityId;
                this.selectedfilterParams.themeId = store.state.themeId;
                this.selectedfilterParams.skillId = store.state.skillId;
                this.selectedfilterParams.search = store.state.search;
                this.selectedfilterParams.exploreMissionType = store.state.exploreMissionType
                this.selectedfilterParams.exploreMissionParams = store.state.exploreMissionParams;
                this.selectedCity = [];
                this.selectedTheme = [];
                this.selectedSkill = [];
                filterList(this.selectedfilterParams).then(response => {
                    if (response) {
                        if (response.country) {
                            this.countryList = Object.entries(response.country);
                        }

                        if (response.city) {
                            this.cityList = Object.entries(response.city);
                        }

                        if (response.themes) {
                            this.themeList = Object.entries(response.themes);
                        }

                        if (response.skill) {
                            this.skillList = Object.entries(response.skill);
                        }

                        if (store.state.countryId != '') {
                            if (this.countryList) {
                                let selectedCountryData = this.countryList.filter((country) => {
                                    if (store.state.countryId == country[1].id) {
                                        return country;
                                    }
                                });
                                if (selectedCountryData[0]) {
                                    this.defautCountry = selectedCountryData[0][1].title;
                                    tags.country[0] = selectedCountryData[0][1].id + '_' + selectedCountryData[
                                        0][1].title;
                                } else {
                                    this.defautCountry = this.languageData.label.country;
                                    tags.country[0] = '';
                                }
                            }
                        } else {
                            this.defautCountry = this.languageData.label.country;
                        }

                        if (store.state.cityId != '') {
                            this.selectedCity = store.state.cityId.toString().split(',')
                        }

                        if (store.state.themeId != '') {
                            this.selectedTheme = store.state.themeId.toString().split(',')
                        }

                        if (store.state.skillId != '') {
                            this.selectedSkill = store.state.skillId.toString().split(',')
                        }

                    }
                    this.isComponentVisible = true;
                });
            },

            searchMission() {
                this.$parent.searchMissions(this.search, this.selectedfilterParams);
                this.selectedfilterParams.search = this.search
                this.filterSearchListing();
            },

            fetchFilters() {
                this.$emit('cmsListing', this.$route.params.slug);
            },

            clearFilter() {

                this.selectedfilterParams.countryId = '';
                this.defautCountry = this.languageData.label.country;
                this.selectedfilterParams.cityId = '';
                this.selectedfilterParams.themeId = '';
                this.selectedfilterParams.skillId = '';
                this.selectedfilterParams.sortBy = '';
                if(this.$route.params.searchParamsType) {
                    this.selectedfilterParams.exploreMissionType = this.$route.params.searchParamsType
                }
                if(this.$route.params.searchParams) {
                    this.selectedfilterParams.exploreMissionParams = this.$route.params.searchParams;
                }

                this.selectedCity = [];
                this.selectedSkill = [];
                this.selectedTheme = [];
                filterList(this.selectedfilterParams).then(response => {
                    if (response) {
                        if (response.city) {
                            this.cityList = Object.entries(response.city);
                            this.selectedCity = [];
                        }
                    }
                });

            },
            // Filter listing
            filterSearchListing() {

                filterList(this.selectedfilterParams).then(response => {
                    if (response) {
                        if (response.country) {
                            this.countryList = Object.entries(response.country);
                        } else {
                            this.defautCountry = this.languageData.label.country;
                        }

                        if (response.city) {
                            this.cityList = Object.entries(response.city);
                        } else {
                            this.cityList = []
                        }

                        if (response.themes) {
                            this.themeList = Object.entries(response.themes);
                        } else {
                            this.themeList = []
                        }

                        if (response.skill) {
                            this.skillList = Object.entries(response.skill);
                        } else {
                            this.skillList = []
                        }
                    } else {
                        this.defautCountry = this.languageData.label.country;
                        this.cityList = []
                        this.themeList = []
                        this.skillList = []
                        this.selectedCity = []
                        this.selectedSkill = []
                        this.selectedTheme = []
                    }
                });
            },
            clearSearchFilter() {
                this.searchString = '';
                this.selectedfilterParams.search = '';
                this.$parent.searchMissions(this.searchString, this.selectedfilterParams);
                this.filterSearchListing();

                setTimeout(() => {
                    this.handleBlur()
                }, 200)

            },
            clearAllFilter() {
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
                store.commit("exploreFilter", filters);
                let userFilter = {};
                userFilter.search = store.state.search;
                userFilter.sortBy = store.state.sortBy;
                userFilter.countryId = '';
                userFilter.cityId = '';
                userFilter.themeId = '';
                userFilter.skillId = '';
                userFilter.tags = [];
                userFilter.sortBy = store.state.sortBy;
                store.commit("userFilter", userFilter);
                this.$router.push({
                    name: 'home'
                })
                this.$parent.getMissions("removeLoader");
                setTimeout(() => {
                    this.selectedfilterParams.countryId = store.state.countryId;
                    this.selectedfilterParams.cityId = store.state.cityId;
                    filterList(this.selectedfilterParams).then(response => {
                        if (response) {
                            if (response.country) {
                                this.countryList = Object.entries(response.country);
                            }

                            if (response.city) {
                                this.cityList = Object.entries(response.city);
                            }
                            if (response.themes) {
                                this.themeList = Object.entries(response.themes);
                            }

                            if (response.skill) {
                                this.skillList = Object.entries(response.skill);
                            }
                            if (store.state.countryId != '') {
                                if (this.countryList) {
                                    let selectedCountryData = this.countryList.filter((
                                        country) => {
                                        if (store.state.countryId == country[1].id) {
                                            return country;
                                        }
                                    });
                                    this.defautCountry = selectedCountryData[0][1].title;
                                }
                            } else {
                                this.defautCountry = this.languageData.label.country;
                            }

                            if (store.state.cityId != '') {
                                this.selectedCity = store.state.cityId.toString().split(',')
                            }
                        }
                    });
                }, 500);
            },
            clearMissionFilters() {
                this.$parent.clearMissionFilter();
            }
        },
        created() {
            this.languageData = JSON.parse(store.state.languageLabel);

            this.searchPlaceHolder = this.languageData.label.search + ' ' + this.languageData.label.mission;
            this.quickAccessFilterSet = this.settingEnabled(constants.QUICK_ACCESS_FILTERS);
            this.isThemeDisplay = this.settingEnabled(constants.THEMES_ENABLED);
            this.isSkillDisplay = this.settingEnabled(constants.SKILLS_ENABLED);
            this.isCountrySelectionSet = this.settingEnabled(constants.IS_COUNTRY_SELECTION);
            eventBus.$on('clearAllFilters', () => {
                this.clearFilter();
            });
            eventBus.$on('setDefaultText', () => {
                this.defautCountry = this.languageData.label.country;
            });
            eventBus.$on('setDefaultData', () => {
                this.filterListing();
            });
            // Fetch Filters
            this.filterListing();
            if (store.state.search != null) {
                this.search = store.state.search;
            }
            if (this.missionList.length < 0) {
                this.countryList = [];
                this.cityList = [];
                this.themeList = [];
                this.skillList = [];
            }
            setTimeout(() => {
                this.handleFilterCount();
            });
        }
    };
</script>