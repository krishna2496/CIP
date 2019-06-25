<template>
    <div class="bottom-header">
        <b-container>
            <b-row>
                <b-col xl="6" lg="5" class="search-block">
                    <div class="icon-input">
                        <b-form-input
                            type="text"
                            @keypress.enter.prevent="searchMission"
                            :placeholder="$t('label.search')+' '+$t('label.mission')"
                            @focus="handleFocus()"
                            @blur="handleBlur()"
                            onfocus="this.placeholder=''"
                            id="search"
                            v-model="search"
                            onblur="this.placeholder='Search mission...'">                           
                        </b-form-input>
                        <i>
                            <img src="../../assets/images/search-ic.svg" alt="Search">
                        </i>
                    </div>
                </b-col>

                <b-col xl="6" lg="7" class="filter-block">
                    <div class="mobile-top-block">
                        <b-button class="btn btn-back" @click="handleBack">
                            <img src="../../assets/images/down-arrow.svg" alt="Back Icon">
                        </b-button>
                        <b-button class="btn btn-clear">{{$t("label.clear_all")}}</b-button>
                    </div>
                <b-list-group>
                    <b-list-group-item>
                        <AppCustomDropdown
                            :optionList="countryList"
                            :defaultText="defautCountry"
                            translationEnable= "false" 
                            @updateCall="updateCountry"
                        />
                    </b-list-group-item>
                    <b-list-group-item>
                        <AppCheckboxDropdown :filterTitle="defautCity" :checkList="cityList"/>
                            </b-list-group-item>
                            <b-list-group-item>
                        <AppCheckboxDropdown :filterTitle="defautTheme" :checkList="themeList"/>
                            </b-list-group-item>
                            <b-list-group-item>
                        <AppCheckboxDropdown :filterTitle="defautSkill" :checkList="skillList"/>
                    </b-list-group-item>
                </b-list-group>
                </b-col>

                <div class="filter-icon" @click="handleFilter" @touchend.stop>
                    <img src="../../assets/images/header/filter-ic.svg" alt="filter">
                </div>
            </b-row>
        </b-container>
    </div>
</template>

<script>
import AppCustomDropdown from "../AppCustomDropdown";
import AppCheckboxDropdown from "../AppCheckboxDropdown";
import {countryList,cityList,skillList,themeList} from "../../services/service";

export default {
    components: { AppCustomDropdown, AppCheckboxDropdown },
    name: "TheSecondaryHeader",
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
            form: {
                text: ""
            },
            show: false,
            search:''
        };
    },
    methods: {
        handleFocus() {
            var b_header = document.querySelector(".bottom-header");
            b_header.classList.add("active");
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

        updateCountry(value) {
            this.defautCountry = value.selectedVal;
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

        getCountry() {
            countryList().then( response => {
                if (response) {                    
                    this.countryList = response
                     //fetch city
                    this.getCity();   
                }            
            });   
        },

        getCity() {
            cityList().then( response => {
                if (response) {                    
                    this.cityList = response
                    // Fetch theme
                    this.getTheme();   
                }            
            });   
        },

        getTheme() {
            themeList().then( response => {
                if (response) {                    
                    this.themeList = response
                    // Fetch skill
                    this.getSkill();   
                }            
            });   
        },

        getSkill() {
                skillList().then( response => {
                if (response) {                    
                    this.skillList = response   
                }            
            });   
        },

        searchMission() {
            if (this.search != ''){
                this.$emit('searchMission',this.$route.params.slug);
            }
        },

        fetchFilters() {

        }
    },
    created() {
        // Fetch country
        // this.getCountry();
        //Fetch users filter
        this.fetchFilters();

        var _this = this;
        setTimeout(function(){ 
            _this.defautCountry = _this.$i18n.t("label.country");
            _this.defautCity =  _this.$i18n.t("label.city"),
            _this.defautTheme =  _this.$i18n.t("label.theme"),
            _this.defautSkill = _this.$i18n.t("label.skills")
        },400);
    }
};
</script>