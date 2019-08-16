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
                <div v-bind:class="{ 'content-loader-wrap': true, 'profile-image-loader': imageLoader}">
                    <div class="content-loader"></div>
                </div>
                <picture-input 
                      v-if="isPrefilLoaded"
                      ref="fileInput" 
                      @change="changeImage"  
                      accept="image/jpeg,image/png"
                      :prefillOptions="prefillOptionArray"
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
                  title="change password"
                  class="btn-link-border"
                  @click="$refs.changePasswordModal.show()"
                >{{langauageData.label.change_password}}</b-button>
            </div>
            <b-form-group>
                <label>{{langauageData.label.language}}</label>
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
                    <label>{{langauageData.label.timezone}}</label>
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
                        <label for>{{langauageData.label.name}}</label>
                        <b-form-input id type="text" 
                        v-model="profile.firstName" 
                        :class="{ 'is-invalid': submitted && $v.profile.firstName.$error }" 
                        @keydown.space.prevent
                        autofocus 
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
                            <label for>{{langauageData.label.surname}}</label>
                            <b-form-input id type="text" 
                            v-model="profile.lastName" 
                            :class="{ 'is-invalid': submitted && $v.profile.lastName.$error }" 
                            @keydown.space.prevent
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
                                v-model="profile.employeeId"    
                                @keydown.space.prevent
                                maxlength="16"
                                :placeholder="langauageData.placeholder.employee_id">
                            </b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.manager}}</label>
                            <b-form-input id type="text"
                            v-model="profile.managerName"                     
                            @keydown.space.prevent
                            :placeholder="langauageData.placeholder.manager"
                            maxlength="16"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.title}}</label>
                            <b-form-input id type="text"
                            v-model="profile.title" 
                            @keydown.space.prevent
                            :placeholder="langauageData.placeholder.title"
                            maxlength="25"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label for>{{langauageData.label.department}}</label>
                            <b-form-input id type="text"
                            v-model="profile.department" 
                            @keydown.space.prevent
                            :placeholder="langauageData.placeholder.department"></b-form-input>
                            
                        </b-form-group>
                    </b-col>
                    <b-col md="12">
                        <b-form-group>
                            <label>{{langauageData.label.my_profile}}</label>
                            <b-form-textarea
                            id
                            :placeholder="langauageData.placeholder.my_profile"
                            size="lg"
                            no-resize
                            v-model="profile.profileText" 
                            :class="{ 'is-invalid': submitted && $v.profile.profileText.$error }" 
                            @keydown.space.prevent
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
                        <label>{{langauageData.label.why_i_volunteer}}</label>
                        <b-form-textarea
                        id
                        v-model="profile.whyiVolunteer" 
                        :class="{ 'is-invalid': submitted && $v.profile.whyiVolunteer.$error }" 
                        @keydown.space.prevent
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
                    <label>{{langauageData.label.country}}</label>
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
                    <label>{{langauageData.label.city}}</label>
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
                        <label>{{langauageData.label.availablity}}</label>
                        <CustomFieldDropdown               
                            v-model="profile.availability" 
                            :errorClass="submitted && $v.profile.availability.$error" 
                            :defaultText="availabilityDefault"
                            :optionList="availabilityList"
                            @updateCall="updateAvailability"
                            translationEnable= "false"
                        />
                          <div v-if="submitted && !$v.profile.availability.required" class="invalid-feedback">
                        {{ langauageData.errors.availablity_required }}</div>
                    </b-form-group>
                  
                </b-col>
                <b-col md="6">
                    <b-form-group>
                        <label>{{langauageData.label.linked_in}}</label>
                        <b-form-input id 
                         v-model="profile.linkedInUrl" 
                        :class="{ 'is-invalid': submitted && $v.profile.linkedInUrl.$error }" 
                        @keydown.space.prevent
                        :placeholder="langauageData.placeholder.linked_in"
                        ></b-form-input>
                          <div v-if="submitted && !$v.profile.linkedInUrl.validLinkedInUrl" class="invalid-feedback">
                        {{ langauageData.errors.valid_linked_in_url }}</div>
                    </b-form-group>
                  
                </b-col>
            </b-row>
            <b-row class="row-form" v-if="isShownComponent">
                <b-col cols="12">
                    <h2 class="title-with-border">
                        <span>{{langauageData.label.custom_field}}</span>
                    </h2>
                </b-col>
                <b-col cols="12">
                    <CustomField
                    :optionList="customFieldList"
                    :optionListValue="customFieldValue"
                    @detectChangeInCustomFeild = "detectChangeInCustomFeild"
                    />
                </b-col>
              
                
            </b-row>
            <b-row class="row-form">
                <b-col cols="12">
                  <h2 class="title-with-border">
                    <span>{{langauageData.label.my_skills}}</span>
                  </h2>
                </b-col>
                <b-col cols="12">
                    <ul class="skill-list-wrapper" v-if="resetUserSkillList != null && resetUserSkillList.length > 0">
                        <li  v-for="(toitem, idx) in resetUserSkillList">{{toitem.name}}</li>
                    </ul>
                    <ul v-else class="skill-list-wrapper" >
                        <li>{{langauageData.label.no_record_found}}</li>
                    </ul>
                    <MultiSelect
                        v-if="isShownComponent"
                        :fromList="skillListing"
                        :toList="userSkillList"
                        @resetData = "resetSkillListingData"
                        @saveSkillData = "saveSkillData"
                    />
                </b-col>
                <div class="btn-wrapper">
                    <b-button class="btn-bordersecondary" @click="handleSubmit">{{langauageData.label.save}}</b-button>
                </div>
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
          <template slot="modal-title">{{langauageData.label.change_password}}</template>
            <b-alert show :variant="classVariant" dismissible v-model="showErrorDiv">
                    {{ message }}
            </b-alert>
          <form action class="form-wrap">
            <b-form-group>
              <b-form-input id type="password" 
               v-model="resetPassword.oldPassword" 
                :class="{ 'is-invalid': passwordSubmit && $v.resetPassword.oldPassword.$error }" 
                @keydown.space.prevent
              :placeholder="langauageData.placeholder.old_password"
              ></b-form-input>
              <div v-if="passwordSubmit && !$v.resetPassword.oldPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.field_required }}</div>
            </b-form-group>

            <b-form-group>
              <b-form-input id type="password" 
                v-model="resetPassword.newPassword" 
                :class="{ 'is-invalid': passwordSubmit && $v.resetPassword.newPassword.$error }" 
                @keydown.space.prevent
               :placeholder="langauageData.placeholder.new_password"
              ></b-form-input>
                <div v-if="passwordSubmit && !$v.resetPassword.newPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.field_required }}</div>
                <div v-if="passwordSubmit && !$v.resetPassword.newPassword.minLength" class="invalid-feedback">
                {{ langauageData.errors.invalid_password }}</div>
            </b-form-group>

            <b-form-group>
              <b-form-input id 
               v-model="resetPassword.confirmPassword" 
                :class="{ 'is-invalid': passwordSubmit && $v.resetPassword.confirmPassword.$error }" 
                @keydown.space.prevent
                :placeholder="langauageData.placeholder.confirm_password"
                type="password"> 
              </b-form-input>
                <div v-if="passwordSubmit && !$v.resetPassword.confirmPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.field_required }}</div>
                <div v-if="passwordSubmit && !$v.resetPassword.confirmPassword.sameAsPassword" class="invalid-feedback">
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
              @click="changePassword()"
             
            >{{langauageData.label.change_password}}</b-button>
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
import PictureInput from 'vue-picture-input'
import {getUserDetail,saveProfile,changeUserPassword,changeProfilePicture,changeCity,saveUserProfile,saveSkill,loadLocaleMessages} from "../services/service";
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
            languageDefault: "",
            userIcon: require("@/assets/images/user-img-large.png"),
            timeList: [],
            timeDefault: "",
            countryList: [],
            countryDefault: '',
            availabilityList: [],
            passwordSubmit : false,
            availabilityDefault: "",
            file: "null",
            langauageData : [],
            skillListing : [],
            resetSkillList : [],
            prefillOptionArray : {
                mediaType: 'image/png'
            },
            clientImage : "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/tatva/assets/images/volunteer9.png",
            newUrl : "",
            isPrefilLoaded : false,
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
            time : '',
            customFieldList : [],
            customFieldValue :[],
            returnCustomFeildData : [],
            userSkillList : [],
            resetUserSkillList : [],
            imageLoader : true,
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
    mounted() {},

    methods: {
        updateLang(value) {
            this.languageDefault = value.selectedVal;
            this.profile.languageCode = value.selectedVal;
            this.profile.language =  value.selectedId;
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
                    this.disableApply = true;
                    this.makeToast("success",response.message);
                    store.commit("changeAvatar",response.data)
                }
                this.imageLoader = false;
                
            })
        },
        saveSkillData(skillList) {
            this.resetUserSkillList = skillList
        },
        // Get user detail
        async getUserProfileDetail() {
            await getUserDetail().then(response => {
                if(response.error == true){
                    this.$router.push('/404');
                } else {
                    var _this = this;
                    this.userData = response.data;
                    this.newUrl = this.userData.avatar_base64;
                    var lowerCase = this.newUrl.toLowerCase();
                    if (lowerCase.indexOf("png") !== -1) {
                        this.prefilImageType.mediaType = "png"
                    }
                    else if (lowerCase.indexOf("jpg") !== -1 || lowerCase.indexOf("jpeg") !== -1) {
                        
                        this.prefilImageType.mediaType = "jpg"
                    }  

                    this.isPrefilLoaded = true
                    this.countryList = Object.keys(this.userData.country_list).map(function(key) {
                        return [Number(key), _this.userData.country_list[key]];
                    });
                    this.cityList = Object.keys(this.userData.city_list).map(function(key) {
                        return [Number(key), _this.userData.city_list[key]];
                    });
                    this.availabilityList = Object.keys(this.userData.availability_list).map(function(key) {
                        return [Number(key), _this.userData.availability_list[key]];
                    });
                    this.languageList = Object.keys(this.userData.language_list).map(function(key) {
                        return [Number(key), _this.userData.language_list[key]];
                    });
                    this.timeList = Object.keys(this.userData.timezone_list).map(function(key) {
                        return [Number(key), _this.userData.timezone_list[key]];
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
                    this.languageDefault = this.userData.language_code
                }
                if( this.userData.timezone.timezone != '' &&  this.userData.timezone.timezone != null) {
                    this.timeDefault = this.userData.timezone.timezone
                }


                if(this.userData.skill_list) {
                        Object.keys(this.userData.skill_list).map(function(key) {
                            if(_this.userData.skill_list[key]) {
                                _this.skillListing.push({
                                    name:_this.userData.skill_list[key],
                                    id: key
                                });
                                _this.resetSkillList.push({
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
                                _this.resetUserSkillList.push({
                                    name:_this.userData.user_skills[key].translations,
                                    id: _this.userData.user_skills[key].skill_id
                                });
                            }
                    });
                }
                } 
                this.imageLoader = false;
                this.isShownComponent = true;
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
           
            // this.userSkillList = this.resetUserSkillList;
        },
        detectChangeInCustomFeild (data) {
            this.returnCustomFeildData = data;
        },
        //submit form
        handleSubmit(e) {
            var _this = this;
            this.submitted = true;
            this.$v.$touch();
            // stop here if form is invalid
            // console.log(this.$v.profile);
            if (this.$v.profile.$invalid) {
                return;
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
        
                Object.keys(this.returnCustomFeildData).map(function(key) {
                    
                        _this.saveProfileData.custom_fields.push({
                            field_id: key,
                            value : _this.returnCustomFeildData[key]
                        });
                    });
            
             // console.log(this.saveProfileData);
            // Call to save profile service 
            saveUserProfile(this.saveProfileData).then( response => {
                if(response.error == true){
                    this.makeToast("danger",response.message);
                } else {
                    saveSkill(this.userSkillList).then( skillResponse => {
                        if(skillResponse.error == true){
                            this.makeToast("danger",skillResponse.message);
                        } else {
                            this.isShownComponent = false;
                            loadLocaleMessages(this.profile.languageCode).then(langaugeResponse =>{
                                this.langauageData = JSON.parse(store.state.languageLabel); 
                                this.isShownComponent = true;
                                this.disableApply = true;
                                this.makeToast("success",response.message);
                            });
                            store.commit("changeUserDetail",this.profile)
                        }
                    });
                }
            });
           


        },   
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
                }
            });
            // changePassword
        },
        changeCityData(countryId) {
            if(countryId) {
                changeCity(countryId).then( response => {
                    // console.log(response);
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
    },
    created() {
        var _this =this
        this.langauageData = JSON.parse(store.state.languageLabel);
        this.countryDefault = this.langauageData.placeholder.country 
        this.cityDefault = this.langauageData.placeholder.city 
        this.availabilityDefault = this.langauageData.placeholder.availablity 
        this.languageDefault = this.langauageData.placeholder.language 
        this.timeDefault = this.langauageData.placeholder.timezone 
        
        this.getUserProfileDetail();
    }

};
</script>