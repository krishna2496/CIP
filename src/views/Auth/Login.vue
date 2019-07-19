<template>
    <div class="signin-page-wrapper">
        <TheSlider v-if="isShowComponent"/>
        <div class="signin-form-wrapper">
            <div class="lang-drodown-wrap">
                <AppCustomDropdown :optionList="langList" :defaultText="defautLang" 
                translationEnable= "false" 
                @updateCall="setLanguage" v-if="isShowComponent" />
            </div>
            <div class="signin-form-block">
                <router-link to="/" class="logo-wrap" v-if="this.$store.state.logo">
                    <img :src="this.$store.state.logo">
                </router-link>
                <!-- success or error msg -->
                <b-alert show :variant="classVariant" dismissible v-model="showDismissibleAlert">{{ message }}</b-alert>
                <!-- login form start -->
                <b-form class="signin-form">
                    <b-form-group>
                        <label for="">{{ $t("label.email_address") }}</label>
                        <b-form-input id="" type="email" v-model="login.email" 
                        v-bind:placeholder='$t("placeholder.email_address")'
                        :class="{ 'is-invalid': $v.login.email.$error }" ref='email' autofocus 
                        maxlength="120"></b-form-input>
                        <div v-if="submitted && !$v.login.email.required" class="invalid-feedback">
                        {{ $t("errors.email_required") }}</div>
                        <div v-if="submitted && !$v.login.email.email" class="invalid-feedback">
                        {{ $t("errors.invalid_email") }}</div>
                    </b-form-group>
                    <b-form-group>
                        <label for="">{{ $t("label.password") }}</label>
                        <b-form-input id="" type="password" v-model="login.password" required 
                        v-bind:placeholder='$t("placeholder.password")' 
                        :class="{ 'is-invalid': $v.login.password.$error }" 
                        maxlength="120"
                        @keypress.enter.prevent="handleSubmit"
                        @keydown.space.prevent></b-form-input>
                        <div v-if="submitted && !$v.login.password.required" class="invalid-feedback">
                        {{ $t("errors.password_required") }}</div>
                    </b-form-group>
                    <b-button type="button" @click="handleSubmit" class=" btn-bordersecondary">
                    {{ $t("label.login") }}</b-button>
                </b-form>
                <!-- link to forgot-password -->
                <div class="form-link">
                    <b-link to="/forgot-password">{{ $t("label.lost_password") }}</b-link>
                </div>
            </div>
            <ThePrimaryFooter ref="ThePrimaryFooter" v-if="isShowComponent"/>
        </div>
    </div>
</template>

<script>
import TheSlider from '../../components/TheSlider';
import ThePrimaryFooter from "../../components/Layouts/ThePrimaryFooter";
import AppCustomDropdown from '../../components/AppCustomDropdown';
import { required, email, between } from 'vuelidate/lib/validators';
import store from '../../store';
import {loadLocaleMessages,login,databaseConnection,tenantSetting} from '../../services/service';
import constants from '../../constant';

export default {
    components: {       
        ThePrimaryFooter,
        AppCustomDropdown,
        TheSlider,
    },
    data() {
        return {
            flag: false,
            myValue: '',
            defautLang: '',
            langList: [],
            login: {
                email: '',
                password: '',
            },
            submitted: false,
            classVariant: 'danger',
            message: null,
            showDismissibleAlert: false,
            isShowComponent : false,
        };
    },
    validations: {
        login: {
            email: {required, email},
            password: {required}
        }
    },
    methods: {
        async createConnection(){
            await databaseConnection(this.langList).then(response => {
                    this.isShowComponent = true
                    //Get langauage list from Local Storage
                    this.langList = JSON.parse(store.state.listOfLanguage)
                    this.defautLang = store.state.defaultLanguage
                    // Get tenant setting
                    tenantSetting(); 
            })       
        },
        
        async setLanguage(language){
            var _this = this;
            this.defautLang = language.selectedVal;
            store.commit('setDefaultLanguage',language);
            this.$i18n.locale = language.selectedVal.toLowerCase()
            await loadLocaleMessages(this.$i18n.locale);   
            _this.$forceUpdate();
            _this.$refs.ThePrimaryFooter.$forceUpdate()
        },

        handleSubmit(e) {
            this.submitted = true;
            this.$v.$touch();
            // stop here if form is invalid
            if (this.$v.$invalid) {
                return;
            }
            // Call to login service with params email address and password
            login(this.login).then( response => {
                if (response.error === true) { 
                    this.message = null;
                    this.showDismissibleAlert = true
                    this.classVariant = 'danger'
                    //set error msg
                    this.message = response.message
                } else {
                    //redirect to landing page
                    this.$router.replace({
                        name: "home"
                    });
                }
            });
        },
    },
    mounted() {
        //Autofocus
        this.$refs.email.focus();        
    },        
    created() {
        //Database connection and fetching tenant options api
        this.createConnection()

        var domain = '';
        var imagePath = '';
        var currentUrl = (((window.location.origin).split('.')));
        if (currentUrl[0]) {
            domain = ((currentUrl[0]).split('//'));
            if(domain[1]) {
                // imagePath = constants.IMAGE_PATH + domain[1];
                imagePath = constants.IMAGE_PATH + "tatva";
                store.commit('setImagePath', imagePath);
            }
        }
    },
};
</script>