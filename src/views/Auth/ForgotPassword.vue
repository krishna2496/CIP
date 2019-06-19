<template>
    <div class="signin-page-wrapper">
        <Slider/>
        <div class="signin-form-wrapper">
            <div class="lang-drodown-wrap">
                <CustomDropdown :optionList="langList" :defaultText="defautLang" 
                translationEnable= "false"  
                @updateCall="setLanguage"/>
            </div>
            <div class="signin-form-block">
                <router-link to="/" class="logo-wrap" v-if="this.$store.state.logo">
                    <img :src="this.$store.state.logo">
                </router-link>
                <div class="form-title-block">
                    <h1>{{ $t("label.forgot_password") }}</h1>
                    <p>{{ $t("label.forgot_password_message") }}</p>
                </div>
                <!-- success or error msg -->
                <b-alert show :variant="classVariant" dismissible v-model="showDismissibleAlert"> {{ message }}</b-alert>
                <!-- forgot password form start -->       
                <b-form class="signin-form">
                    <b-form-group>
                    <label for>{{ $t("label.email_address") }}</label>
                    <b-form-input id type="email" v-model="forgotPassword.email" :class="{ 'is-invalid': $v.forgotPassword.email.$error }" @keypress.enter.prevent="handleSubmit" 
                    maxlength="120"
                    v-bind:placeholder='$t("placeholder.email_address")'
                    ref='email' autofocus></b-form-input>
                    <div v-if="submitted && !$v.forgotPassword.email.required" class="invalid-feedback">
                        {{ $t("errors.email_required") }}</div>
                    <div v-if="submitted && !$v.forgotPassword.email.email" class="invalid-feedback">
                        {{ $t("errors.invalid_email") }}</div>
                    </b-form-group>         
                    <b-button type="button" @click="handleSubmit" class="btn btn-bordersecondary">{{ $t("label.reset_password_button") }}
                    </b-button>
                </b-form>
                <div class="form-link">
                <b-link to="/">{{ $t("label.login") }}</b-link>
                </div>
            </div>
            <PrimaryFooter ref="PrimaryFooter"/>
        </div>
    </div>
</template>

<script>
import Slider from "../../components/Slider";
import PrimaryFooter from "../../components/Layouts/PrimaryFooter";
import CustomDropdown from "../../components/CustomDropdown";
import { required, email, minLength, between } from 'vuelidate/lib/validators';
import {loadLocaleMessages,forgotPassword} from '../../services/service';
import store from '../../store';
import axios from "axios";

export default {
    components: {
        Slider,
        PrimaryFooter,
        CustomDropdown
    },
    data() {
        return {
            myValue: "",
            defautLang: "",
            langList:[],
            forgotPassword: {
            email: '',
            },
            submitted: false,
            classVariant: 'danger',
            message: null,
            showDismissibleAlert: false,
        };
    },

    validations: {
        forgotPassword: {
        email: {required, email},
    }
    },
    computed: {},
    methods: {
        async setLanguage(language){
            var _this = this;
            this.defautLang = language.selectedVal;
            store.commit('setDefaultLanguage',language);
            this.$i18n.locale = language.selectedVal.toLowerCase()
            await loadLocaleMessages(this.$i18n.locale);   
                _this.$forceUpdate();
                _this.$refs.PrimaryFooter.$forceUpdate()
        },
        handleSubmit(e) {
            this.submitted = true;
            // Stop here if form is invalid
            this.$v.$touch();
            if (this.$v.$invalid) {
                return;
            }
            // Call to Forgot Password service with params email address and password
            forgotPassword(this.forgotPassword).then( response => {
                if (response.error === true) { 
                    this.message = null;
                    this.showDismissibleAlert = true
                    this.classVariant = 'danger'
                    // Set error message
                    this.message = response.message
                } else {
                    this.message = null;
                    this.showDismissibleAlert = true
                    this.classVariant = 'success'
                    // Set success message
                    this.message = response.message  
                    //Reset to blank
                    this.submitted = false;
                    this.forgotPassword.email = '';
                    this.$v.$reset();
                }
            });
        },
    },
    mounted() {
    //Autofocus
        this.$refs.email.focus();
    },
    created() {
        // Set language list and default language fetch from Local Storage
        this.langList = (localStorage.getItem('listOfLanguage') !== null) ? JSON.parse(localStorage.getItem('listOfLanguage')) : []
        this.defautLang = localStorage.getItem('defaultLanguage') 
    },
};
</script>
