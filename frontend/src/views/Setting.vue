<template>
<div class="profile-page inner-pages donation-profile">
    <header>
        <ThePrimaryHeader v-if="isShownComponent"></ThePrimaryHeader>
    </header>
    <main>
        <b-container>
            <b-row class="dashboard-tab-content" v-if="errorPage && pageLoaded">
                <b-col xl="12" lg="12" md="12">
                    <b-alert show variant="danger">
                        {{errorPageMessage}}
                    </b-alert>
                </b-col>
            </b-row>
            <b-row class="is-profile-complete" v-if="isUserProfileComplete != 1">
                <b-col xl="12" lg="12" md="12">
                    <b-alert show variant="warning">
                        {{languageData.label.fill_up_mandatory_fields_to_access_platform}}
                    </b-alert>
                </b-col>
            </b-row>
            <b-row class="profile-content" v-if="showPage && (!errorPage) && pageLoaded">
                <b-col xl="3" lg="4" md="12" class="profile-left-col">
                    <div class="profile-details">
                        <div class="profile-block">
                            <div v-bind:class="{ 'content-loader-wrap': true, 'loader-active ': isPrefilLoaded}">
                                <div class="content-loader"></div>
                            </div>
                            <picture-input :title="changePhoto" ref="pictureInput" @change="changeImage" accept="image/jpeg,image/png" :prefill="newUrl" buttonClass="btn" :customStrings="{
                                        upload: '<h1>Bummer!</h1>',
                                        drag: 'Drag a 😺 GIF or GTFO'
                                    }">
                            </picture-input>
                        </div>
                        <h4>{{userData.first_name}} {{userData.last_name}}</h4>
                        <b-list-group class="social-nav">

                            <b-list-group-item v-if="userData.linked_in_url != null && userData.linked_in_url != ''  ">
                                <b-link :href="userData.linked_in_url" target="_blank" :title="languageData.label.linked_in" class="linkedin-link">
                                    <img :src="`${$store.state.imagePath}/assets/images/linkedin-ic-blue.svg`" class="normal-img" alt="linkedin img" />
                                    <img :src="`${$store.state.imagePath}/assets/images/linkedin-ic.svg`" class="hover-img" alt="linkedin hover img" />
                                </b-link>
                            </b-list-group-item>
                        </b-list-group>
                    </div>
                    <!-- my account breadcrumb -->
                    <MyAccountDashboardBreadcrumb></MyAccountDashboardBreadcrumb>
                </b-col>
                <b-col xl="9" lg="8" md="12" class="profile-form-wrap">
                    <b-form class="profile-form">
                        <b-row class="row-form">
                            <b-col cols="12">
                                <h2 class="title-with-border">
                                    <span>{{languageData.label.privacy}}</span>
                                </h2>
                            </b-col>
                            <b-col cols="12">
                                <b-form-group class="checkbox-group-wrap">
                                    <div class="site-checkbox">
                                        <b-form-checkbox class="custom-control-input" v-model="is_profile_visible">{{languageData.label.allow_all_my}} <strong>{{languageData.label.co_workers}} </strong>{{languageData.label.to_see_my_profile}}
                                        </b-form-checkbox>
                                    </div>
                                    <div class="site-checkbox">
                                        <b-form-checkbox class="custom-control-input" v-model="public_avatar_and_linkedin">{{languageData.label.allow}} <strong>{{languageData.label.public}} </strong>
                                            {{languageData.label.fundraisers_and_funds_to_view_avatar_and_my_linkedin}}
                                        </b-form-checkbox>
                                    </div>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <b-row class="row-form">
                            <b-col cols="12">
                                <h2 class="title-with-border">
                                    <span>{{languageData.label.change_password}}</span>
                                </h2>
                            </b-col>
                            <b-alert show :variant="classletiant" dismissible v-model="showErrorDiv">
                                {{ message }}
                            </b-alert>
                            <b-col md="6">
                                <b-form-group>
                                    <label for>{{languageData.label.current_pasword}}*</label>
                                    <b-form-input id type="password" ref="oldPassword" v-model.trim="oldPassword" :class="{ 'is-invalid': $v.oldPassword.$error }" :placeholder="languageData.placeholder.old_password"></b-form-input>
                                    <div v-if="!$v.oldPassword.required" class="invalid-feedback">
                                        {{ languageData.errors.field_is_required }}</div>
                                </b-form-group>
                            </b-col>
                            <b-col md="6"></b-col>
                            <b-col md="6">
                                <b-form-group>
                                    <label for>{{languageData.placeholder.new_password}}*
                                    </label>
                                    <b-form-input id type="password" v-model.trim="newPassword" :class="{ 'is-invalid': $v.newPassword.$error }" :placeholder="languageData.placeholder.new_password"></b-form-input>
                                    <div v-if="!$v.newPassword.required" class="invalid-feedback">
                                        {{ languageData.errors.field_is_required }}</div>
                                    <div v-if="!$v.newPassword.minLength" class="invalid-feedback">
                                        {{ languageData.errors.invalid_password }}</div>
                                </b-form-group>
                            </b-col>
                            <b-col md="6"></b-col>
                            <b-col md="6">
                                <b-form-group>
                                    <label for>
                                        {{languageData.placeholder.confirm_password}}*
                                    </label>
                                    <b-form-input id v-model.trim="confirmPassword" :class="{ 'is-invalid': $v.confirmPassword.$error }" :placeholder="languageData.placeholder.confirm_password" @keypress.enter.prevent="changePassword" type="password">
                                    </b-form-input>
                                    <div v-if="!$v.confirmPassword.required" class="invalid-feedback">
                                        {{ languageData.errors.field_is_required }}</div>
                                    <div v-if="$v.confirmPassword.required && !$v.confirmPassword.sameAsPassword" class="invalid-feedback">
                                        {{ languageData.errors.identical_password }}</div>
                                </b-form-group>
                            </b-col>
                            <b-col md="6"></b-col>
                        </b-row>
                        <b-row class="row-form">
                            <b-col cols="12">
                                <h2 class="title-with-border">
                                    <span>{{languageData.label.preferences}}</span>
                                </h2>
                            </b-col>
                            <b-col md="6">
                                <b-form-group>
                                    <label>{{languageData.label.language}}*</label>
                                    <CustomFieldDropdown v-model="language" :errorClass="submitted && $v.language.$error" :defaultText="languageDefault" :optionList="languageList" @updateCall="updateLang" translationEnable="false" />
                                    <div v-if="submitted && !$v.language.required" class="invalid-feedback">
                                        {{ languageData.errors.language_required }}
                                    </div>
                                </b-form-group>
                            </b-col>
                            <b-col md="6"></b-col>
                            <b-col md="6">
                                <b-form-group>
                                    <label>{{languageData.label.timezone}}*</label>
                                    <model-select class="search-dropdown" v-bind:class="{'is-invalid' :submitted && $v.time.$error}" :options="timeList" v-model="time" :placeholder="timeDefault" @input="updateTime">
                                    </model-select>
                                    <div v-if="submitted && !$v.time.required" class="invalid-feedback">
                                        {{ languageData.errors.timezone_required }}
                                    </div>
                                </b-form-group>
                            </b-col>
                            <b-col md="6"></b-col>
                            <b-col md="6">
                                <b-form-group>
                                    <label>{{languageData.label.currency}}*</label>
                                    <model-select class="search-dropdown" v-bind:class="{'is-invalid' :submitted && $v.currency.$error}" :options="currencyList" v-model="currency" :placeholder="currencyDefault" @input="updateCurrency">
                                    </model-select>
                                    <div v-if="submitted && !$v.currency.required" class="invalid-feedback">
                                        {{ languageData.errors.currency_required }}
                                    </div>
                                </b-form-group>
                            </b-col>
                            <b-col md="6"></b-col>
                        </b-row>
                        <b-row class="row-form">
                            <b-col cols="12">
                                <div class="btn-wrapper">
                                    <b-button class="btn-bordersecondary btn-save" :title="languageData.label.save" @click="handleSubmit()">{{ languageData.label.save }}
                                    </b-button>
                                </div>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-col>
            </b-row>
        </b-container>
    </main>
    <footer>
        <TheSecondaryFooter v-if="isShownComponent"></TheSecondaryFooter>
    </footer>
</div>
</template>

<script>
import CustomFieldDropdown from "../components/CustomFieldDropdown";
import MultiSelect from "../components/MultiSelect";
import CustomField from "../components/CustomField";
import store from "../store";
import PictureInput from '../components/vue-picture-input'
import {
    ModelSelect
} from 'vue-search-select'
import {
    getUserDetail,
    changeUserPassword,
    changeProfilePicture,
    changeCity,
    saveUserProfile,
    loadLocaleMessages,
    country,
    skill,
    timezone,
    settingListing,
    submitSetting
} from "../services/service";
import {
    required,
    maxLength,
    sameAs,
    minLength
} from 'vuelidate/lib/validators';
import constants from '../constant';

export default {
    components: {
        ThePrimaryHeader: () => import("../components/Layouts/ThePrimaryHeader"),
        TheSecondaryFooter: () => import("../components/Layouts/TheSecondaryFooter"),
        CustomFieldDropdown,
        MultiSelect,
        PictureInput,
        CustomField,
        ModelSelect,
        CustomFieldDropdown,
        MyAccountDashboardBreadcrumb: () => import("../components/MyAccountDashboardBreadcrumb")
    },
    data() {
        return {
            isUserProfileComplete: 1,
            languageList: [],
            errorPage: false,
            pageLoaded: false,
            errorPageMessage: false,
            isQuickAccessFilterDisplay: true,
            isSkillDisplay: true,
            languageDefault: "",
            userIcon: require("@/assets/images/user-img-large.png"),
            timeList: [],
            timeDefault: "",
            countryList: [],
            countryDefault: '',
            availabilityList: [],
            passwordSubmit: false,
            isCustomFieldSubmit: false,
            availabilityDefault: "",
            file: "null",
            languageData: [],
            skillListing: [],
            resetSkillList: [],
            newUrl: "",
            isPrefilLoaded: true,
            prefilImageType: {
                mediaType: ''
            },
            userData: [],
            isShownComponent: false,
            cityList: [],
            cityDefault: "",
            resetPassword: {
                oldPassword: "",
                newPassword: "",
                confirmPassword: ""
            },
            showErrorDiv: false,
            message: null,
            classletiant: "success",
            language: "",
            time: "",
            languageCode: "",
            oldPassword: "",
            newPassword: "",
            confirmPassword: "",
            currency: "",
            is_profile_visible: false,
            public_avatar_and_linkedin: false,
            currency: "",
            time: '',
            submitted: false,
            language: '',
            languageCode: null,

            CustomFieldList: [],
            CustomFieldValue: [],
            returnCustomFeildData: [],
            userSkillList: [],
            resetUserSkillList: [],
            imageLoader: true,
            changePhoto: "",
            showPage: true,
            saveProfileData: {
                password: '',
                confirm_password: '',
                is_profile_visible: true,
                public_avatar_and_linkedin: true,
                language_id: 0,
                timezone_id: 0,
                currency: 0,
                old_password: ''
            },
            currencyList: [{
                    value: "0",
                    text: "GMT-4"
                },
                {
                    value: "01",
                    text: "UTC-5"
                },
                {
                    value: "02",
                    text: "UTC-6"
                },
                {
                    value: "03",
                    text: "UTC-7"
                },
            ],
            currencyDefault: ""

        };
    },
    validations: {

        oldPassword: {
            required
        },
        newPassword: {
            required,
            minLength: minLength(constants.PASSWORD_MIN_LENGTH)
        },
        confirmPassword: {
            required,
            sameAsPassword: sameAs('newPassword')
        },
        language: {
            required
        },
        time: {
            required
        },
        currency: {
            required
        }
    },
    updated() {

    },
    methods: {
        updateLang(value) {
            this.languageDefault = value.selectedVal;
            this.languageCode = value.selectedVal;
            this.language = value.selectedId;
            this.languageCode = this.userData.language_code_list[value.selectedId];
        },
        updateCurrency(value) {
            this.currency = value
        },
        updateTime(value) {
            this.time = value;
        },
        updateCity(value) {
            this.cityDefault = value.selectedVal;
            this.city = value.selectedId;

        },
        updateCountry(value) {
            this.countryDefault = value.selectedVal;
            this.country = value.selectedId;

            this.changeCityData(value.selectedId);
        },
        updateAvailability(value) {
            this.availabilityDefault = value.selectedVal;
            this.availability = value.selectedId;
        },
        changeImage(image) {
            this.imageLoader = true;
            let imageData = {}

            imageData.avatar = image;
            changeProfilePicture(imageData).then(response => {
                if (response.error == true) {
                    this.makeToast("danger", response.message);
                } else {
                    this.makeToast("success", response.message);
                    store.commit("changeAvatar", response.data)
                }
                this.imageLoader = false;

            })
        },
        saveSkillData() {
            let data = JSON.parse(localStorage.getItem('currentSkill'));
            this.resetUserSkillList = data
        },
        // Get user detail
        async getUserProfileDetail() {
            await getUserDetail().then(response => {
                this.pageLoaded = true;
                if (response.error == true) {
                    this.isShownComponent = true
                    this.errorPage = true
                    this.errorPageMessage = response.message
                } else {
                    this.errorPage = false

                    this.userData = response.data;
                    this.newUrl = this.userData.avatar;
                    const img = new Image();
                    if (this.newUrl != '' && this.newUrl != null) {
                        img.src = this.newUrl;
                        img.onload = () => {
                            this.isPrefilLoaded = false
                        }
                    } else {
                        this.isPrefilLoaded = false
                    }
                    store.commit("changeAvatar", this.userData)

                    this.cityList = Object.keys(this.userData.city_list).map((key) => {
                        return [Number(key), this.userData.city_list[key]];
                    });
                    this.cityList.sort((a, b) => {
                        let cityOne = a[1].toLowerCase(),
                            cityTwo = b[1].toLowerCase();
                        if (cityOne < cityTwo) //sort string ascending
                            return -1;
                        if (cityOne > cityTwo)
                            return 1;
                        return 0; //default return value (no sorting)
                    });
                    this.availabilityList = Object.keys(this.userData.availability_list).map((key) => {
                        return [Number(key), this.userData.availability_list[key]];
                    });
                    this.languageList = Object.keys(this.userData.language_list).map((key) => {
                        return [Number(key), this.userData.language_list[key]];
                    });

                    this.CustomFieldList = this.userData.custom_fields

                    if (this.userData.user_custom_field_value) {
                        this.CustomFieldValue = Object.keys(this.userData.user_custom_field_value).map((
                            key) => {
                            return [
                                Number(this.userData.user_custom_field_value[key]
                                    .field_id),
                                this.userData.user_custom_field_value[key].value
                            ];
                        });
                    }

                    this.firstName = this.userData.first_name,
                        this.lastName = this.userData.last_name,
                        this.employeeId = this.userData.employee_id,
                        this.profileText = this.userData.profile_text,
                        this.title = this.userData.title,
                        this.whyiVolunteer = this.userData.why_i_volunteer
                    if (this.userData.linked_in_url != null) {
                        this.linkedInUrl = this.userData.linked_in_url
                    }
                    this.department = this.userData.department,
                        // this.availability = this.userData.availability_id,
                        this.userSkills = this.userData.user_skills
                    if (this.userData.country_id != 0) {
                        this.country = this.userData.country_id
                    }
                    if (this.userData.city_id != 0) {
                        this.city = this.userData.city_id
                    }
                    if (this.userData.availability_id != 0 && this.userData.availability_id != null) {
                        this.availability = this.userData.availability_id
                    }

                    if (this.userData.language_id != 0) {
                        this.language = this.userData.language_id
                    }
                    if (this.userData.timezone_id != 0) {
                        this.time = this.userData.timezone_id
                    }
                    this.languageCode = this.userData.language_code

                    if (this.userData.city_list != '' && this.userData.city_list != null) {
                        this.cityDefault = this.userData.city_list[this.userData.city_id]
                    }
                    if (this.userData.availability && this.userData.availability.type != '' && this.userData.availability.type !=
                        null) {
                        const translatedAvailability = this.userData.availability.translations
                            .find(translation => translation.lang === this.languageCode.toLowerCase());
                        this.availabilityDefault = translatedAvailability ?
                            translatedAvailability.title : this.userData.availability.type;
                    } else {
                        this.availabilityDefault = this.languageData.placeholder.availability;
                    }
                    if (this.userData.language_id != '' && this.userData.language_id != null) {
                        Object.keys(this.userData.language_list).map((key) => {
                            if (key == this.userData.language_id) {
                                this.languageDefault = this.userData.language_list[key]
                            }
                        });
                    }
                    if (this.userData.timezone && this.userData.timezone.timezone != '' && this.userData.timezone.timezone !=
                        null) {
                        this.timeDefault = this.userData.timezone.timezone
                    }
                    this.skillListing = [];
                    this.userSkillList = [];
                    this.resetUserSkillList = [];
                    store.commit("saveCurrentSkill", null)
                    store.commit("saveCurrentFromSkill", null)
                    country().then(responseData => {
                        if (responseData.error == false) {
                            this.countryList = responseData.data
                            if (this.countryList) {
                                this.countryList.filter((data, index) => {
                                    if (this.userData.country_id == data[0]) {
                                        this.countryDefault = data[1]
                                    }
                                })
                            }
                            this.countryList.sort((countryA, countryB) => {
                                let countryOne = countryA[1].toLowerCase(),
                                    countryTwo = countryB[1].toLowerCase();
                                if (countryOne < countryTwo) //sort string ascending
                                    return -1;
                                if (countryOne > countryTwo)
                                    return 1;
                                return 0; //default return value (no sorting)
                            });
                        }

                        timezone().then(responseData => {
                            if (responseData.error == false) {
                                var array = [];

                                responseData.data.filter((data, index) => {
                                    array.push({
                                        'text': data[1],
                                        'value': data[0]
                                    })
                                })
                                this.timeList = array
                            }

                            skill().then(responseData => {
                                if (responseData.error == false) {
                                    this.userData.skill_list = responseData.data
                                    Object.keys(this.userData.skill_list).map(
                                        (key) => {
                                            if (this.userData.skill_list[
                                                    key]) {
                                                this.skillListing.push({
                                                    name: this
                                                        .userData
                                                        .skill_list[
                                                            key],
                                                    id: key
                                                });

                                                this.skillListing.sort(function (first, next) {
                                                    first = first.name;
                                                    next = next.name;
                                                    return first < next ? -1 : (first > next ? 1 : 0);
                                                });
                                            }
                                        });
                                }
                                this.isShownComponent = true;
                            })
                        })
                    })

                    if (this.userData.user_skills) {
                        Object.keys(this.userData.user_skills).map((key) => {
                            if (this.userData.user_skills[key].translations) {
                                this.userSkillList.push({
                                    name: this.userData.user_skills[key].translations,
                                    id: this.userData.user_skills[key].skill_id
                                });
                                this.resetUserSkillList.push({
                                    name: this.userData.user_skills[key].translations,
                                    id: this.userData.user_skills[key].skill_id
                                });
                            }
                        });
                    }
                }
                this.imageLoader = false;

            })
        },

        async getSettingListing() {
            await settingListing().then(response => {
                this.pageLoaded = true;
                if (response.error == true) {
                    this.isShownComponent = true
                    this.errorPage = true
                    this.errorPageMessage = response.message
                } else {
                    this.time = response.data.preference.timezone_id
                    this.language = response.data.preference.language_id
                    this.currency = response.data.preference.currency
                    if (response.data.timezone) {
                        var timezoneArray = [];
                        let timeZone = Object.entries(response.data.timezone);

                        timeZone.filter((data, index) => {
                            if (data[0] == response.data.preference.timezone_id) {
                                this.timeDefault = data[1]
                            }
                            // this.time = this.userData.timezone_id
                            timezoneArray.push({
                                'text': data[1],
                                'value': data[0]
                            })
                        })
                        this.timeList = timezoneArray
                    }

                    if (response.data.languages) {
                        var languagesArray = [];
                        let languages = response.data.languages;
                        this.languageList = Object.keys(languages).map((key) => {
                            console.log(response.data.preference.language_id, languages[key]['language_id'])
                            if (response.data.preference.language_id == languages[key]['language_id']) {
                                this.languageDefault = languages[key]['name']
                            }
                            return [languages[key]['language_id'], languages[key]['name']];
                        });
                    }

                    if (response.data.currencies) {
                        var currenciesArray = [];
                        let currencies = Object.entries(response.data.currencies);
                        currencies.filter((data, index) => {
                            currenciesArray.push({
                                'text': data[1].code,
                                'value': data[1].code
                            })
                        })
                        this.currencyList = currenciesArray
                    }

                    this.isShownComponent = true;

                }
            })
        },
        resetSkillListingData() {
            this.skillListing = [];
            this.userSkillList = [];
            if (this.userData.skill_list) {
                Object.keys(this.userData.skill_list).map((key) => {
                    if (this.userData.skill_list[key]) {
                        this.skillListing.push({
                            name: this.userData.skill_list[key],
                            id: key
                        });
                    }
                });
            }
            if (this.userData.user_skills) {
                Object.keys(this.userData.user_skills).map((key) => {
                    if (this.userData.user_skills[key].translations) {
                        this.userSkillList.push({
                            name: this.userData.user_skills[key].translations,
                            id: this.userData.user_skills[key].skill_id
                        });
                    }
                });
            }
            this.userSkillList.filter((toItem) => {
                this.skillListing.filter((fromItem, fromIndex) => {
                    if (toItem.id == fromItem.id) {
                        this.skillListing.splice(fromIndex, 1);
                    }
                });
            });

        },
        //submit form
        handleSubmit() {

            this.submitted = true;
            this.$v.$touch();
            if (this.$v.$invalid) {
                return;
            }
            //  password: 0,
            //     confirm_password: 0,
            //     is_profile_visible: true,
            //     public_avatar_and_linkedin: true,
            //     language_id: 0,
            //     timezone_id: 0,
            //     currency: 0

            if (this.oldPassword) {
                this.saveProfileData.password = this.oldPassword;
            }
            if (this.newPassword) {
                this.saveProfileData.confirm_password = this.newPassword;
            }
            if (this.oldPassword) {
                this.saveProfileData.old_password = this.oldPassword;
            }
            this.saveProfileData.is_profile_visible = this.is_profile_visible;
            this.saveProfileData.public_avatar_and_linkedin = this.public_avatar_and_linkedin;
            this.saveProfileData.language_id = this.language;
            this.saveProfileData.timezone_id = this.time;
            this.saveProfileData.currency = this.currency;

            // Call to save profile service
            submitSetting(this.saveProfileData).then(response => {
                if (response.error == true) {
                    this.makeToast("danger", response.message);
                } else {
                    store.commit('setDefaultLanguageCode', this.languageCode)
                    this.showPage = false;
                    this.getSettingListing().then(() => {
                        this.showPage = true;
                        loadLocaleMessages(this.languageCode).then(() => {
                            this.languageData = JSON.parse(store.state.languageLabel);
                            this.makeToast("success", response.message);
                            this.isShownComponent = true;
                        });

                        // store.commit("changeUserDetail", this.profile)

                    });
                }
            });
        },

        makeToast(variant = null, message) {
            this.$bvToast.toast(message, {
                variant: variant,
                solid: true,
                autoHideDelay: 3000
            })
        },
        alphaNumeric(evt) {
            evt = (evt) ? evt : window.event;
            let keyCode = (evt.which) ? evt.which : evt.keyCode;
            if (!((keyCode >= 48 && keyCode <= 57) ||
                    (keyCode >= 65 && keyCode <= 90) ||
                    (keyCode >= 97 && keyCode <= 122)) &&
                keyCode != 8 && keyCode != 32) {
                evt.preventDefault();
            }
        }
    },
    created() {
        this.languageData = JSON.parse(store.state.languageLabel);
        this.countryDefault = this.languageData.placeholder.country
        this.cityDefault = this.languageData.placeholder.city
        this.availabilityDefault = this.languageData.placeholder.availablity
        this.languageDefault = this.languageData.placeholder.language
        this.timeDefault = this.languageData.placeholder.timezone
        this.changePhoto = this.languageData.label.edit
        this.languageCode = store.state.defaultLanguage
        this.currencyDefault = this.languageData.placeholder.currency
        this.isQuickAccessFilterDisplay = this.settingEnabled(constants.QUICK_ACCESS_FILTERS);
        this.isSkillDisplay = this.settingEnabled(constants.SKILLS_ENABLED);
        this.getSettingListing();
        // this.isShownComponent = true;
        // this.pageLoaded = true
        if (store.state.isProfileComplete != 1) {
            this.isUserProfileComplete = 0;
        }
        this.newUrl = store.state.avatar
        this.isPrefilLoaded = false
        this.imageLoader = false;
    }
};
</script>
