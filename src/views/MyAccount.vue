<template>
    <div class="profile-page inner-pages">
    <header>
        <ThePrimaryHeader v-if="isShownComponent"></ThePrimaryHeader>
    </header>
    <main>
    <b-container>
        <b-row class="profile-content">
        <b-col xl="3" lg="4" md="12" class="profile-left-col">
            <div class="profile-details">
            <div  class="profile-block">
                <div v-bind:class="{ 'content-loader-wrap': true, 'loader-active ': isPrefilLoaded}">
                    <div class="content-loader"></div>
                </div>

                <picture-input 
                    :title="changePhoto"
                    ref="pictureInput" 
                    @change="changeImage"  
                    accept="image/jpeg,image/png"
                    :prefill="newUrl"
                    buttonClass="btn"
                    :customStrings="{
                    upload: '<h1>Bummer!</h1>',
                    drag: 'Drag a 😺 GIF or GTFO'
                    }">
                </picture-input>
            </div>
            <h4>{{userData.first_name}} {{userData.last_name}}</h4>
            <b-list-group class="social-nav">
            
            </b-list-group-item>
            <b-list-group-item v-if="userData.linked_in_url != null && userData.linked_in_url != ''  ">
                <b-link :href="userData.linked_in_url" target="_blank" title="linked in" class="linkedin-link">
                    <img
                        :src="$store.state.imagePath+'/assets/images/linkedin-ic-blue.svg'"
                        class="normal-img"
                        alt="linkedin img"
                    />
                    <img
                        :src="$store.state.imagePath+'/assets/images/linkedin-ic.svg'"
                        class="hover-img"
                        alt="linkedin img"
                    />
                </b-link>
            </b-list-group-item>
            </b-list-group>
            <div class="link-wrap">
                <b-button
                  class="btn-link-border"
                  @click="handleModel"
                >{{langauageData.label.change_password}}</b-button>
            </div>
            <b-form-group>
                <label>{{langauageData.label.language}}*</label>
                <CustomFieldDropdown
                    v-model="profile.language" 
                    :errorClass="submitted && $v.profile.language.$error" 
                    :defaultText="languageDefault"
                    :optionList="languageList"
                    @updateCall="updateLang"
                    translationEnable= "false"
                />
                <div v-if="submitted && !$v.profile.language.required" class="invalid-feedback">
                            {{ langauageData.errors.language_required }}
                </div>
            </b-form-group>
                <b-form-group>
                    <label>{{langauageData.label.timezone}}*</label>
                    <CustomFieldDropdown
                        v-model="profile.time" 
                        :errorClass="submitted && $v.profile.time.$error" 
                        :defaultText="timeDefault"
                        :optionList="timeList"
                        @updateCall="updateTime"
                        translationEnable= "false"
                    />
                    <div v-if="submitted && !$v.profile.time.required" class="invalid-feedback">
                                {{ langauageData.errors.timezone_required }}
                    </div>
                </b-form-group>
            </div>
        </b-col>
        <b-col xl="9" lg="8" md="12" class="profile-form-wrap">
            <b-form class="profile-form">
                <b-row class="row-form">
                    <b-col cols="12">
                        <h2 class="title-with-border">
                            <span>{{langauageData.label.basic_information}}</span>
                        </h2>
                    </b-col>
                    <b-col md="6">
                    <b-form-group>
                        <label for>{{langauageData.label.name}}*</label>
                        <b-form-input id type="text" 
                        v-model.trim="profile.firstName" 
                        :class="{ 'is-invalid': submitted && $v.profile.firstName.$error }" 
                        @keypress="alphaNumeric($event)"
                        :placeholder="langauageData.placeholder.name" 
                        maxlength="16"
                        ></b-form-input>
                        <div v-if="submitted && !$v.profile.firstName.required" class="invalid-feedback">
                            {{ langauageData.errors.name_required }}
                        </div>
                    </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.surname}}*</label>
                            <b-form-input id type="text" 
                            v-model.trim="profile.lastName" 
                            :class="{ 'is-invalid': submitted && $v.profile.lastName.$error }" 
                            @keypress="alphaNumeric($event)"
                            :placeholder="langauageData.placeholder.surname"
                            maxlength="16"
                            ></b-form-input>
                            <div v-if="submitted && !$v.profile.lastName.required" class="invalid-feedback">
                            {{ langauageData.errors.last_name_required }}</div>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.employee_id}}</label>
                            <b-form-input id type="text" 
                                v-model.trim="profile.employeeId"    
                                @keypress="alphaNumeric($event)"
                                maxlength="16"
                                :placeholder="langauageData.placeholder.employee_id">
                            </b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.manager}}</label>
                            <b-form-input id type="text"
                            v-model.trim="profile.managerName"                     
                            @keypress="alphaNumeric($event)"
                            :placeholder="langauageData.placeholder.manager"
                            maxlength="16"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.title}}</label>
                            <b-form-input id type="text"
                            v-model.trim="profile.title" 
                            @keypress="alphaNumeric($event)"
                            :placeholder="langauageData.placeholder.title"
                            maxlength="25"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.department}}</label>
                            <b-form-input id type="text"
                            v-model.trim="profile.department" 
                            @keypress="alphaNumeric($event)"
                             maxlength="16"
                            :placeholder="langauageData.placeholder.department"></b-form-input>
                            
                        </b-form-group>
                    </b-col>
                    <b-col md="12">
                        <b-form-group>
                            <label>{{langauageData.label.my_profile}}*</label>
                            <b-form-textarea
                            id
                            :placeholder="langauageData.placeholder.my_profile"
                            size="lg"
                            no-resize
                            @keypress="alphaNumeric($event)"
                            v-model.trim="profile.profileText" 
                            :class="{ 'is-invalid': submitted && $v.profile.profileText.$error }" 
        
                            rows="5"
                            ></b-form-textarea>
                            <div v-if="submitted && !$v.profile.profileText.required" class="invalid-feedback">
                            {{ langauageData.errors.my_profile_required }}</div>
                            <div v-if="submitted && !$v.profile.profileText.maxLength && $v.profile.profileText.required" class="invalid-feedback">
                            {{ langauageData.errors.my_profile_max_length }}</div>
                        </b-form-group>
                    </b-col>
                    <b-col md="12">
                    <b-form-group>
                        <label>{{langauageData.label.why_i_volunteer}}*</label>
                        <b-form-textarea
                        id
                        v-model.trim="profile.whyiVolunteer" 
                        @keypress="alphaNumeric($event)"
                        :class="{ 'is-invalid': submitted && $v.profile.whyiVolunteer.$error }" 
                        :placeholder="langauageData.placeholder.why_i_volunteer"
                        size="lg"
                        no-resize
                        rows="5"
                        ></b-form-textarea>
                        <div v-if="submitted && !$v.profile.whyiVolunteer.required" class="invalid-feedback">
                        {{ langauageData.errors.why_i_volunteer_required }}</div>
                        <div v-if="submitted && !$v.profile.whyiVolunteer.maxLength && $v.profile.whyiVolunteer.required" class="invalid-feedback">
                        {{ langauageData.errors.why_i_volunteer_max_length }}</div>
                    </b-form-group>
                    </b-col>
            </b-row>
            <b-row class="row-form">
            <b-col cols="12">
                <h2 class="title-with-border">
                    <span>{{langauageData.label.address_information}}</span>
                </h2>
            </b-col>
            <b-col md="6">
                <b-form-group>
                    <label>{{langauageData.label.country}}*</label>
                    <CustomFieldDropdown
                        v-model="profile.country" 
                        :errorClass="submitted && $v.profile.country.$error" 
                        :defaultText="countryDefault"
                        :optionList="countryList"
                        @updateCall="updateCountry"
                        translationEnable= "false"
                    />
                    <div 
                v-if="submitted && !$v.profile.country.required" class="invalid-feedback">
                {{ langauageData.errors.country_required }}
                </div>
                </b-form-group>
                
            </b-col>
            <b-col md="6">
                <b-form-group>
                    <label>{{langauageData.label.city}}*</label>
                     <CustomFieldDropdown
                        v-model="profile.city" 
                        :errorClass="submitted && $v.profile.city.$error" 
                        :defaultText="cityDefault"
                        :optionList="cityList"
                        @updateCall="updateCity"
                        translationEnable= "false"
                    />  
                     <div v-if="submitted && !$v.profile.city.required" class="invalid-feedback">
                        {{ langauageData.errors.city_required }}</div>
                </b-form-group>
               
            </b-col>
            </b-row>
            <b-row class="row-form">
                <b-col cols="12">
                    <h2 class="title-with-border">
                        <span>{{langauageData.label.professional_information}}</span>
                    </h2>
                </b-col>
                <b-col md="6">
                    <b-form-group>
                        <label>{{langauageData.label.availability}}*</label>
                        <CustomFieldDropdown               
                            v-model="profile.availability" 
                            :errorClass="submitted && $v.profile.availability.$error" 
                            :defaultText="availabilityDefault"
                            :optionList="availabilityList"
                            @updateCall="updateAvailability"
                            translationEnable= "false"
                        />
                          <div v-if="submitted && !$v.profile.availability.required" class="invalid-feedback">
                        {{ langauageData.errors.availability_required }}</div>
                    </b-form-group>
                  
                </b-col>
                <b-col md="6">
                    <b-form-group>
                        <label>{{langauageData.label.linked_in}}</label>
                        <b-form-input id 
                         v-model.trim="profile.linkedInUrl" 
                        :class="{ 'is-invalid': submitted && $v.profile.linkedInUrl.$error }" 
                        :placeholder="langauageData.placeholder.linked_in"
                        ></b-form-input>
                          <div v-if="submitted && !$v.profile.linkedInUrl.validLinkedInUrl" class="invalid-feedback">
                        {{ langauageData.errors.valid_linked_in_url }}</div>
                    </b-form-group>
                  
                </b-col>
            </b-row>
            <b-row class="row-form" v-if="isShownComponent && customFieldList.length > 0">
                <b-col cols="12">
                    <h2 class="title-with-border">
                        <span>{{langauageData.label.custom_field}}</span>
                    </h2>
                </b-col>
                <b-col cols="12">
                    <CustomField
                    :optionList="customFieldList"
                    :optionListValue="customFieldValue"
                    :isSubmit="isCustomFieldSubmit"
                    @detectChangeInCustomFeild = "detectChangeInCustomFeild"
                    />
                </b-col>
              
                
            </b-row>
            <b-row class="row-form">
                <b-col cols="12" v-if="isSkillDisplay">
                  <h2 class="title-with-border">
                    <span>{{langauageData.label.my_skills}}</span>
                  </h2>
                </b-col>
                <b-col cols="12" v-if="isSkillDisplay">
                    <ul class="skill-list-wrapper" v-if="resetUserSkillList != null && resetUserSkillList.length > 0">
                        <li  v-for="(toitem, idx) in resetUserSkillList">{{toitem.name}}</li>
                    </ul>
                    <ul v-else class="skill-list-wrapper" >
                        <li>{{langauageData.label.no_skill_found}}</li>
                    </ul>
                    <MultiSelect
                        v-if="isShownComponent"
                        :fromList="skillListing"
                        :toList="userSkillList"
                        @resetData = "resetSkillListingData"
                        @saveSkillData = "saveSkillData"
                        @resetPreviousData = "resetPreviousData"
                    />
                     <div class="btn-wrapper">
                    <b-button class="btn-bordersecondary" @click="handleSubmit">{{langauageData.label.save}}</b-button>
                </div>
                </b-col>
            </b-row>
            </b-form>
        </b-col>
        </b-row>

        <b-modal
          ref="changePasswordModal"
          :modal-class="'password-modal sm-popup'"
          centered
          hide-footer
        >
            <template slot="modal-header" slot-scope="{ close }">
                <i class="close"  @click="close()" v-b-tooltip.hover :title="langauageData.label.close"></i>
              <h5 class="modal-title">{{langauageData.label.change_password}}</h5>
            </template>
            <b-alert show :variant="classVariant" dismissible v-model="showErrorDiv">
                {{ message }}
            </b-alert>
            <form action class="form-wrap">
            <b-form-group>
              <b-form-input id type="password"
                ref="oldPassword" 
                v-model.trim="resetPassword.oldPassword" 
                :class="{ 'is-invalid': passwordSubmit && $v.resetPassword.oldPassword.$error }" 
                :placeholder="langauageData.placeholder.old_password"
              ></b-form-input>
              <div v-if="passwordSubmit && !$v.resetPassword.oldPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.field_is_required }}</div>
            </b-form-group>

            <b-form-group>
              <b-form-input id type="password" 
                v-model.trim="resetPassword.newPassword" 
                :class="{ 'is-invalid': passwordSubmit && $v.resetPassword.newPassword.$error }" 
               :placeholder="langauageData.placeholder.new_password"
              ></b-form-input>
                <div v-if="passwordSubmit && !$v.resetPassword.newPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.field_is_required }}</div>
                <div v-if="passwordSubmit && !$v.resetPassword.newPassword.minLength" class="invalid-feedback">
                {{ langauageData.errors.invalid_password }}</div>
            </b-form-group>

            <b-form-group>
              <b-form-input id 
               v-model.trim="resetPassword.confirmPassword" 
                :class="{ 'is-invalid': passwordSubmit && $v.resetPassword.confirmPassword.$error }" 
                :placeholder="langauageData.placeholder.confirm_password"
                @keypress.enter.prevent="changePassword"
                type="password"> 
              </b-form-input>
                <div v-if="passwordSubmit && !$v.resetPassword.confirmPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.field_is_required }}</div>
                <div v-if="passwordSubmit && $v.resetPassword.confirmPassword.required && !$v.resetPassword.confirmPassword.sameAsPassword" class="invalid-feedback">
                    {{ langauageData.errors.identical_password }}</div>
            </b-form-group>
          </form>
          <div class="btn-wrap">
            <b-button
            class="btn-borderprimary"
            @click="$refs.changePasswordModal.hide()"
            >{{langauageData.label.cancel}}</b-button>
            <b-button
                class="btn-bordersecondary"
                @click="changePassword()">
                {{langauageData.label.change_password}}
            </b-button>
          </div>
        </b-modal>

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
import {getUserDetail,saveProfile,changeUserPassword,changeProfilePicture,changeCity,saveUserProfile,saveSkill,loadLocaleMessages,country,skill,timezone} from "../services/service";
import { required,maxLength, email,sameAs, minLength, between,helpers} from 'vuelidate/lib/validators';
import constants from '../constant';

export default {
    components: {
        ThePrimaryHeader : () => import("../components/Layouts/ThePrimaryHeader"),
        TheSecondaryFooter: () => import("../components/Layouts/TheSecondaryFooter"),
        CustomFieldDropdown,
        MultiSelect,
        PictureInput,
        CustomField
    },
    data() {
        return {
            languageList: [],
            isQuickAccessFilterDisplay : true,
            isSkillDisplay : true,
            languageDefault: "",
            userIcon: require("@/assets/images/user-img-large.png"),
            timeList: [],
            timeDefault: "",
            countryList: [],
            countryDefault: '',
            availabilityList: [],
            passwordSubmit : false,
            isCustomFieldSubmit : false,
            availabilityDefault: "",
            file: "null",
            langauageData : [],
            skillListing : [],
            resetSkillList : [],
            clientImage : "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/tatva/assets/images/volunteer9.png",
            newUrl : "",
            isPrefilLoaded : true,
            prefilImageType : {
                mediaType: ''
            },
            userData : [],
            isShownComponent : false,
            cityList : [],
            cityDefault :"",
            resetPassword : {
                oldPassword : "",
                newPassword : "",
                confirmPassword : ""
            },
            showErrorDiv : false,
            message : null,
            classVariant :"success",
            profile : {
                firstName : "",
                lastName : "",
                employeeId : "",
                managerName : "",
                profileText : "",
                title : "",
                whyiVolunteer : "",
                linkedInUrl :"",
                department : "",
                country : "",
                city: "",
                availability : "",
                userSkills : [],
                language : "",
                time : "",
                languageCode : ""
            },
            submitted: false,
            language : '',
            languageCode: null,
            time : '',
            customFieldList : [],
            customFieldValue :[],
            returnCustomFeildData : [],
            userSkillList : [],
            resetUserSkillList : [],
            imageLoader : true,
            changePhoto : "",
            saveProfileData :{
                first_name: "",
                last_name: "",
                timezone_id: "",
                language_id : "",
                availability_id : "",
                why_i_volunteer : "",
                employee_id : "",
                department : "",
                manager_name : "",
                city_id : "",
                country_id : "",
                profile_text : "",
                linked_in_url : "",
                custom_fields: []
            }
        };
    },
    validations: {
        resetPassword : {
            oldPassword : {required},
            newPassword : {required,minLength: minLength(constants.PASSWORD_MIN_LENGTH)},
            confirmPassword : {required,
                        sameAsPassword: sameAs('newPassword')}
        },
        profile : {
            firstName : {required},
            lastName : {required},
            profileText : {required,maxLength: maxLength(255)},
            whyiVolunteer : {required,maxLength: maxLength(255)},
            linkedInUrl :{
                validLinkedInUrl(linkedInUrl) {
                    if(linkedInUrl == ''){
                        return true
                    }
                    const regexp =  /^http(s)?:\/\/([\w]+\.)?linkedin\.com\/[//A-z0-9_-]+\/?$/;
                    return (regexp.test(linkedInUrl));
              }
            },
            country : {required},
            city : {required},
            availability : {required},
            language : {required},
            time : {required},
           
        }
    },
    updated() { 
       
     },
    methods: {
        updateLang(value) {
            this.languageDefault = value.selectedVal;
            this.profile.languageCode = value.selectedVal;
            this.profile.language =  value.selectedId;
            this.languageCode = this.userData.language_code_list[value.selectedId];
        },
        updateTime(value) {
            this.timeDefault = value.selectedVal;
            this.profile.time =  value.selectedId;

        },
        updateCity(value) {
            this.cityDefault = value.selectedVal;
            this.profile.city =  value.selectedId;

        },
        updateCountry(value) { 
            this.countryDefault = value.selectedVal;
            this.profile.country =  value.selectedId;

            this.changeCityData(value.selectedId);
        },
        updateAvailability(value) {
            this.availabilityDefault = value.selectedVal;
            this.profile.availability =  value.selectedId;
        },
        changeImage(image) {
            this.imageLoader = true;
            let imageData = {}
          
            imageData.avatar = image;
            changeProfilePicture(imageData).then(response => {
                if(response.error == true){
                    this.makeToast("danger",response.message);
                } else {
                    this.makeToast("success",response.message);
                    store.commit("changeAvatar",response.data)
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
                if(response.error == true){
                    this.$router.push('/404');
                } else {
                    var _this = this;
                    this.userData = response.data;
                    this.newUrl = this.userData.avatar;
                    const img = new Image();
                    img.src = this.newUrl;
                    img.onload = () => {
                        this.isPrefilLoaded = false
                    }
                    store.commit("changeAvatar",this.userData)

                    this.cityList = Object.keys(this.userData.city_list).map(function(key) {
                        return [Number(key), _this.userData.city_list[key]];
                    });
                    this.availabilityList = Object.keys(this.userData.availability_list).map(function(key) {
                        return [Number(key), _this.userData.availability_list[key]];
                    });
                    this.languageList = Object.keys(this.userData.language_list).map(function(key) {
                        return [Number(key), _this.userData.language_list[key]];
                    });
                   
                    this.customFieldList = this.userData.custom_fields

                    if(this.userData.user_custom_field_value) {
                    this.customFieldValue = Object.keys(this.userData.user_custom_field_value).map(function(key) {
                            return [
                                    Number(_this.userData.user_custom_field_value[key].field_id),
                                    _this.userData.user_custom_field_value[key].value
                            ];
                        });
                    }

                    this.profile.firstName = this.userData.first_name,
                    this.profile.lastName = this.userData.last_name,
                    this.profile.employeeId = this.userData.employee_id,
                    this.profile.managerName = this.userData.manager_name,
                    this.profile.profileText = this.userData.profile_text,
                    this.profile.title = this.userData.title,
                    this.profile.whyiVolunteer = this.userData.why_i_volunteer,
                    this.profile.linkedInUrl = this.userData.linked_in_url,
                    this.profile.department = this.userData.department,
                    this.profile.availability = this.userData.availability_id,
                    this.profile.userSkills = this.userData.user_skills
                    this.profile.country =  this.userData.country_id,
                    this.profile.city= this.userData.city_id,
                    this.profile.availability =  this.userData.availability_id,
                    this.profile.language= this.userData.language_id,
                    this.profile.time= this.userData.timezone_id
                    this.profile.languageCode = this.userData.language_code

                if( this.userData.country.name != '' &&  this.userData.country.name != null) {
                    this.countryDefault = this.userData.country.name
                }
                if( this.userData.city.name != '' &&  this.userData.city.name != null) {
                    this.cityDefault = this.userData.city.name
                }
                if( this.userData.availability.type != '' &&  this.userData.availability.type != null) {
                    this.availabilityDefault = this.userData.availability.type
                }
                if( this.userData.language_id != '' &&  this.userData.language_id != null) {
                    Object.keys(_this.userData.language_list).map(function(key) {
                            if(key == _this.userData.language_id) {
                               _this.languageDefault = _this.userData.language_list[key]
                            }
                    });
                }
                if( this.userData.timezone.timezone != '' &&  this.userData.timezone.timezone != null) {
                    this.timeDefault = this.userData.timezone.timezone
                }
                this.skillListing = [];
                this.userSkillList = [];
                this.resetUserSkillList = [];

                country().then(responseData => {
                    if(responseData.error == false) {
                        this.countryList = responseData.data  
                    }    

                    timezone().then(responseData => {
                        if(responseData.error == false) {
                            this.timeList = responseData.data  
                        } 

                        skill().then(responseData => {
                            if(responseData.error == false) {
                                this.userData.skill_list = responseData.data
                                Object.keys(this.userData.skill_list).map(function(key) {
                                    if(_this.userData.skill_list[key]) {
                                        _this.skillListing.push({
                                            name:_this.userData.skill_list[key],
                                            id: key
                                        }); 
                                    }
                                });
                            } 
                            this.isShownComponent = true;   
                        })   
                    })
                })

                if(this.userData.user_skills) {
                    Object.keys(this.userData.user_skills).map(function(key) {
                            if(_this.userData.user_skills[key].translations) {
                                _this.userSkillList.push({
                                    name:_this.userData.user_skills[key].translations,
                                    id: _this.userData.user_skills[key].skill_id
                                });
                                _this.resetUserSkillList.push({
                                    name:_this.userData.user_skills[key].translations,
                                    id: _this.userData.user_skills[key].skill_id
                                });
                            }
                    });
                }
                } 
                this.imageLoader = false;
               
            })
        },
        resetSkillListingData() {
            this.skillListing = [];
            this.userSkillList = [];
            var _this = this;
            if(this.userData.skill_list) {
                        Object.keys(this.userData.skill_list).map(function(key) {
                            if(_this.userData.skill_list[key]) {
                                _this.skillListing.push({
                                    name:_this.userData.skill_list[key],
                                    id: key
                                });
                            }
                        });
            }
            if(this.userData.user_skills) {
                        Object.keys(this.userData.user_skills).map(function(key) {
                            if(_this.userData.user_skills[key].translations) {
                                _this.userSkillList.push({
                                    name:_this.userData.user_skills[key].translations,
                                    id: _this.userData.user_skills[key].skill_id
                                });      
                            }
                    });
            }
            var filteredObj  = this.userSkillList.filter(function (toItem, toIndex) { 
                var filteredObj  = _this.skillListing.filter(function (fromItem, fromIndex) { 
                    if(toItem.id == fromItem.id) {
                        _this.skillListing.splice(fromIndex,1);
                    }
                });    
            });
           
        },
        resetPreviousData() {
            let currentSkill = JSON.parse(localStorage.getItem('currentSkill'));
            this.userSkillList = currentSkill

            let currentFromSkill = JSON.parse(localStorage.getItem('currentFromSkill'));
            this.skillListing = currentFromSkill
        },
        detectChangeInCustomFeild (data) {
            this.returnCustomFeildData = data;
        },
        //submit form
        handleSubmit(e) {
            var _this = this;
            this.submitted = true;
            this.$v.$touch();
            var isCustomFieldInvalid = false;
            var isNormalFieldInvalid = false;
            let validationData = document.querySelectorAll('[validstate="true"]');    
            this.isCustomFieldSubmit = true;
            validationData.forEach(function(validateData) {
                validateData.classList.add("is-invalid");
                isCustomFieldInvalid = true;
            });
           
            if (this.$v.profile.$invalid) {
               isNormalFieldInvalid = true;
            }

            if(isNormalFieldInvalid == true || isCustomFieldInvalid == true) {
                return 
            } 

            let returnData = {};
                _this.saveProfileData.first_name  = _this.profile.firstName,
                _this.saveProfileData.last_name = _this.profile.lastName,
                _this.saveProfileData.title = _this.profile.title,
                _this.saveProfileData.timezone_id = _this.profile.time,
                _this.saveProfileData.language_id  =_this.profile.language,
                _this.saveProfileData.availability_id  = _this.profile.availability,
                _this.saveProfileData.why_i_volunteer  = _this.profile.whyiVolunteer,
                _this.saveProfileData.employee_id  =_this.profile.employeeId,
                _this.saveProfileData.department  = _this.profile.department,
                _this.saveProfileData.manager_name  = _this.profile.managerName,
                _this.saveProfileData.city_id  = _this.profile.city,
                _this.saveProfileData.country_id  = _this.profile.country,
                _this.saveProfileData.profile_text  = _this.profile.profileText,
                _this.saveProfileData.linked_in_url  = _this.profile.linkedInUrl,
                _this.saveProfileData.custom_fields = []
                _this.saveProfileData.skills = []
       
                Object.keys(this.returnCustomFeildData).map(function(key) { 
                        let customValue = _this.returnCustomFeildData[key];

                        if (Array.isArray(customValue)) {
                            customValue = customValue.join();
                        } 
                    
                        _this.saveProfileData.custom_fields.push({
                            field_id: key,
                            value : customValue
                        });
                    });
                
                if(this.userSkillList.length > 0 && this.isSkillDisplay) {
                    Object.keys(this.userSkillList).map(function(key) {
                        _this.saveProfileData['skills'].push({
                            skill_id:  _this.userSkillList[key].id,                
                        });
                    });
                }

                // Call to save profile service 
                saveUserProfile(this.saveProfileData).then( response => {
                    if(response.error == true){
                        this.makeToast("danger",response.message);
                    } else {    
                        store.commit('setDefaultLanguageCode', this.languageCode)                 
                        this.getUserProfileDetail().then(getResponse => {
                            this.isShownComponent = false;
                            loadLocaleMessages(this.profile.languageCode).then(langaugeResponse => {
                                this.langauageData = JSON.parse(store.state.languageLabel); 
                                this.makeToast("success",response.message);  
                                this.isShownComponent = true;  
                            });
                            store.commit("changeUserDetail",this.profile)
                        });
                    }
                });          
        },   
        // changePassword
        changePassword() {
            var _this = this;
            this.passwordSubmit = true;
            this.$v.$touch();
            // stop here if form is invalid 
            if (this.$v.resetPassword.$invalid) {
                return;
            }
            let resetPasswordData = {}
        
            resetPasswordData.old_password = this.resetPassword.oldPassword
            resetPasswordData.password = this.resetPassword.newPassword
            resetPasswordData.confirm_password = this.resetPassword.confirmPassword
            // Call to save profile service 
            changeUserPassword(resetPasswordData).then( response => {
                if (response.error === true) { 
                    this.message = null;
                    this.showErrorDiv = true
                    this.classVariant = 'danger'
                    //set error msg
                    this.message = response.message
                } else {
                    this.message = null;
                    this.showErrorDiv = true
                    this.classVariant = 'success'
                    //set success msg
                    this.message = response.message 
                    //Reset to blank
                    this.passwordSubmit = false;
                    this.resetPassword.oldPassword = ''
                    this.resetPassword.newPassword = ''
                    this.resetPassword.confirmPassword = ''
                    this.$v.$reset(); 
                    store.commit("changeToken",response.data.token)
                    setTimeout(function() {
                        _this.$refs.changePasswordModal.hide();
                        _this.showErrorDiv = false
                    },1000)
                }
            });
     
        },
        changeCityData(countryId) {
            if(countryId) {
                changeCity(countryId).then( response => {
                    if (response.error === true) { 
                        this.cityList = []
                    } else {
                       this.cityList = response.data
                    }
                    this.cityDefault = this.langauageData.placeholder.city 
                    this.profile.city = '';
                });
            }
        },
        makeToast(variant = null,message) {
            this.$bvToast.toast(message, {
                variant: variant,
                solid: true,
                autoHideDelay: 1000
            })
        },
        alphaNumeric(evt) {
            evt = (evt) ? evt : window.event;
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ( !( (keyCode >= 48 && keyCode <= 57) 
               ||(keyCode >= 65 && keyCode <= 90) 
               || (keyCode >= 97 && keyCode <= 122) ) 
               && keyCode != 8 && keyCode != 32) {
               evt.preventDefault();
            }
        },
        handleModel() {
            this.$refs.changePasswordModal.show()
            let _this = this
            setTimeout(function(){
                _this.$refs.oldPassword.focus();
            },100)
        }
    },
    created() {
        var _this =this
        this.langauageData = JSON.parse(store.state.languageLabel);
        this.countryDefault = this.langauageData.placeholder.country 
        this.cityDefault = this.langauageData.placeholder.city 
        this.availabilityDefault = this.langauageData.placeholder.availablity 
        this.languageDefault = this.langauageData.placeholder.language 
        this.timeDefault = this.langauageData.placeholder.timezone
        this.changePhoto =  this.langauageData.label.edit
        this.languageCode = store.state.defaultLanguage
        this.isQuickAccessFilterDisplay = this.settingEnabled(constants.QUICK_ACCESS_FILTERS);
        this.isSkillDisplay = this.settingEnabled(constants.SKILLS_ENABLED);
        this.getUserProfileDetail();
    }

};
</script>