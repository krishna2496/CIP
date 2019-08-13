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
                <div v-bind:class="{ 'content-loader-wrap': true, 'slider-loader': true}">
                    <div class="content-loader"></div>
                </div>
                <picture-input 
                      v-if="isPrefilLoaded"
                      ref="fileInput" 
                      @change="onChange"  
                      accept="image/jpeg,image/png"
                      :prefillOptions="{mediaType: 'image/png'}"
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
                <AppCustomDropdown
                    v-model="profile.language" 
                    :errorClass="$v.profile.language.$error" 
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
                    <AppCustomDropdown
                        v-model="profile.time" 
                        :errorClass="$v.profile.time.$error" 
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
                        :class="{ 'is-invalid': $v.profile.firstName.$error }" 
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
                            :class="{ 'is-invalid': $v.profile.lastName.$error }" 
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
                            :class="{ 'is-invalid': $v.profile.title.$error }" 
                            @keydown.space.prevent
                            :placeholder="langauageData.placeholder.title"
                            maxlength="25"
                            ></b-form-input>
                            <div v-if="submitted && !$v.profile.title.required" class="invalid-feedback">
                            {{ langauageData.errors.title_required }}</div>
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
                            :class="{ 'is-invalid': $v.profile.profileText.$error }" 
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
                        :class="{ 'is-invalid': $v.profile.whyiVolunteer.$error }" 
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
                    <AppCustomDropdown
                        v-model="profile.country" 
                        :errorClass="$v.profile.country.$error" 
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
                     <AppCustomDropdown
                         v-model="profile.city" 
                        :errorClass="$v.profile.city.$error" 
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
                        <AppCustomDropdown
                              v-model="profile.availability" 
                            :errorClass="$v.profile.availability.$error" 
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
                        :class="{ 'is-invalid': $v.profile.linkedInUrl.$error }" 
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
                  <MultiSelect/>
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
          <form action class="form-wrap">

            <b-form-group>
              <b-form-input id type="password" 
               v-model="resetPassword.oldPassword" 
                :class="{ 'is-invalid': $v.resetPassword.oldPassword.$error }" 
                @keydown.space.prevent
              :placeholder="langauageData.placeholder.old_password"
              ></b-form-input>
              <div v-if="submitted && !$v.resetPassword.oldPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.password_required }}</div>
            </b-form-group>

            <b-form-group>
              <b-form-input id type="password" 
                v-model="resetPassword.newPassword" 
                :class="{ 'is-invalid': $v.resetPassword.newPassword.$error }" 
                @keydown.space.prevent
               :placeholder="langauageData.placeholder.new_password"
              ></b-form-input>
                <div v-if="submitted && !$v.resetPassword.newPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.password_required }}</div>
                <div v-if="submitted && !$v.resetPassword.newPassword.minLength" class="invalid-feedback">
                {{ langauageData.errors.invalid_password }}</div>
            </b-form-group>

            <b-form-group>
              <b-form-input id 
               v-model="resetPassword.confirmPassword" 
                :class="{ 'is-invalid': $v.resetPassword.confirmPassword.$error }" 
                @keydown.space.prevent
                :placeholder="langauageData.placeholder.confirm_password"
                type="password"> 
                <div v-if="submitted && !$v.resetPassword.confirmPassword.required" class="invalid-feedback">
                    {{ langauageData.errors.password_required }}</div>
                <div v-if="submitted && !$v.resetPassword.confirmPassword.sameAsPassword" class="invalid-feedback">
                    {{ langauageData.errors.identical_password }}</div>
              </b-form-input>

            </b-form-group>
          </form>
          <div class="btn-wrap">
            <b-button
              class="btn-borderprimary"
              @click="$refs.changePasswordModal.hide()"
             
            >{{langauageData.label.cancel}}</b-button>
            <b-button
              class="btn-bordersecondary"
              @click="save()"
             
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
import AppCustomDropdown from "../components/AppCustomDropdown";
import MultiSelect from "../components/MultiSelect";
import CustomField from "../components/CustomField";
import store from "../store";
import PictureInput from 'vue-picture-input'
import {getUserDetail} from "../services/service";
import { required,maxLength, email,sameAs, minLength, between,helpers} from 'vuelidate/lib/validators';
import constants from '../constant';

export default {
    components: {
        ThePrimaryHeader : () => import("../components/Layouts/ThePrimaryHeader"),
        TheSecondaryFooter: () => import("../components/Layouts/TheSecondaryFooter"),
        AppCustomDropdown,
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
            availabilityList: [
            "1 :test"
            ],
            availabilityDefault: "",
            file: "null",
            langauageData : [],
            clientImage : "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/tatva/assets/images/volunteer9.png",
            newUrl : "",
            isPrefilLoaded : false,
            prefilImageType : {
                mediaType: 'image/png'
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
                time : ""
            },
            submitted: false,
            language : '',
            time : '',
            customFieldList : [],
            customFieldValue :[]
        };
    },
    validations: {
        resetPassword : {
            oldPassword : {required},
            newPassword : {required,minLength: minLength(constants.PASSWORD_MIN_LENGTH)},
            confirmPassword : {required},
                        sameAsPassword: sameAs('password')
        },
        profile : {
            firstName : {required},
            lastName : {required},
            profileText : {required,maxLength: maxLength(255)},
            title : {required},
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
            time : {required}
        }
    },
    mounted() {},

    methods: {
        updateLang(value) {
            this.langDefault = value.selectedVal;
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
        },
        updateAvailability(value) {
            this.availabilityDefault = value.selectedVal;
            this.profile.availability =  value.selectedId;
        },
        onChange (image) {
            alert(image);
          console.log('New picture selected!')
          if (image) {
            console.log('Picture loaded.')
            // this.image = image
          } else {
            console.log('FileReader API not supported: use the <form>, Luke!')
          }
        },
        // Get user detail
        getUserProfileDetail() {
            getUserDetail().then(response => {
                if(response.error == true){
                    // this.$router.push('/404');
                } else {
                    var _this = this;
                    this.userData = response.data;
                    this.newUrl = this.userData.avatar_base64;
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
                    this.profile.title = this.userData.linked_in_url,
                    this.profile.whyiVolunteer = this.userData.why_i_volunteer,
                    this.profile.linkedInUrl = this.userData.linked_in_url,
                    this.profile.department = this.userData.department,
                    this.profile.availability = this.userData.availability_id,
                    this.profile.userSkills = this.userData.user_skills
                    // this.profile.country =  this.userData.country_id,
                    this.profile.city= this.userData.city_id,
                    this.profile.availability =  this.userData.availability_id,
                    this.profile.language= this.userData.language_id,
                    this.profile.time= this.userData.timezone_id

                if( this.userData.country.name != '' &&  this.userData.country.name != null) {
                    // this.countryDefault = this.userData.country.name
                }
                if( this.userData.city.name != '' &&  this.userData.city.name != null) {
                    this.cityDefault = this.userData.city.name
                }
                if( this.userData.availability.type != '' &&  this.userData.availability.type != null) {
                    this.availabilityDefault = this.userData.availability.type
                }
                if( this.userData.language_id != '' &&  this.userData.language_id != null) {
                    this.languageDefault = "2"
                }
                if( this.userData.timezone.timezone != '' &&  this.userData.timezone.timezone != null) {
                    this.timeDefault = this.userData.timezone.timezone
                }
               
                } 
                this.isShownComponent = true;
            })
        },
        //submit form
        handleSubmit(e) {
            this.submitted = true;
            this.$v.$touch();
            // stop here if form is invalid
            if (this.$v.$invalid) {
                return;
            }
            // // Call to login service with params email address and password
            // login(this.login).then( response => {
            //     if (response.error === true) { 
            //         this.message = null;
            //         this.showDismissibleAlert = true
            //         this.classVariant = 'danger'
            //         //set error msg
            //         this.message = response.message
            //     } else {
            //         //redirect to landing page
            //         this.$router.replace({
            //             name: "home"
            //         });
            //     }
            // });
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