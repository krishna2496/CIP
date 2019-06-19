<template>
    <div class="bottom-header">
        <b-container>
            <b-row>
                <b-col xl="6" lg="5" class="search-block">
                    <div class="icon-input">
                        <b-form-input
                            type="text"
                            :placeholder="$t('label.search')+' '+$t('label.mission')"
                            @focus="handleFocus()"
                            @blur="handleBlur()"
                            onfocus="this.placeholder=''"
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
                        <CustomDropdown
                            :optionList="countryList"
                            :defaultText="defaut_country"
                            translationEnable= "true" 
                            @updateCall="updateCountry"
                        />
                    </b-list-group-item>
                    <b-list-group-item>
                        <CheckboxDropdown :filterTitle="'City'" :checkList="CityList"/>
                            </b-list-group-item>
                            <b-list-group-item>
                        <CheckboxDropdown :filterTitle="'Theme'" :checkList="ThemeList"/>
                            </b-list-group-item>
                            <b-list-group-item>
                        <CheckboxDropdown :filterTitle="'Skills'" :checkList="SkillsList"/>
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
import CustomDropdown from "../CustomDropdown";
import CheckboxDropdown from "../CheckboxDropdown";

export default {
    components: { CustomDropdown, CheckboxDropdown },
    name: "SecondaryHeader",
    data() {
        return {
            defaut_country: "Country",
            countryList: [
                "Afghanistan",
                "Albania",
                "Algeria",
                "Andorra",
                "Angola",
                "Antigua",
                "Argentina",
                "Armenia",
                "Australia",
                "Austria",
                "Azerbaijan",
                "Bahrain",
                "Bangladesh",
                "Barbados",
                "Belarus",
                "Belgium",
                "Belize",
                "Benin",
                "Bhutan",
                "Bolivia",
                "Cambodia",
                "Cameroon",
                "Canada",
                "Dominica",
                "Eritrea",
                "Estonia",
                "Fiji",
                "Finland",
                "France",
                "Gabon",
                "India",
                "Indonesia",
                "Iran",
                "Iraq"
            ],
            CityList: [
                { value: " Tirana " },
                { value: "Durrës " },
                { value: "Vlorë " },
                { value: "Elbasan " },
                { value: "Shkodër" },
                { value: "Fier" }
            ],
            ThemeList: [
                { value: "Education " },
                { value: "Children " },
                { value: "Health" },
                { value: "Animals " },
                { value: "Nutritions" },
                { value: "Environment" }
            ],
            SkillsList: [
                { value: " Anthropology " },
                { value: "Archeolgy" },
                { value: "Astronomy" },
                { value: "Computer Science" },
                { value: "History" },
                { value: "Reserch" }
            ],
            showDropdown: false,
            showDropdown1: false,
            showDropdown2: false,
            form: {
            text: ""
            },
            show: false
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
            this.defaut_country = value;
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
        }
    }
};
</script>