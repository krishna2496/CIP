<template>
  <div class="signin-page-wrapper">
    <SigninSlider/>
    <div class="signin-form-wrapper">
      <div class="lang-drodown-wrap">
        <CustomDropdown :optionList="langList" :default_text="defautLang" @updateCall="setLanguage"/>
      </div>
      <div class="signin-form-block">
        <a href="/home" class="logo-wrap" title>
          <img src="../assets/images/optimy-logo.png">
        </a>
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
          <b-button type="button" @click="handleSubmit" class="btn btn-bordersecondary">{{ $t("label.reset_password_button") }}</b-button>
        </b-form>
        
        <!-- link to login  -->
        <div class="form-link">
          <b-link to="/">{{ $t("label.login") }}</b-link>
        </div>
      </div>
    <SigninFooter/>
    </div>
  </div>
</template>

<script>

import SigninSlider from "../components/SigninSlider";
import SigninFooter from "../components/Footer/SigninFooter";
import CustomDropdown from "../components/CustomDropdown";
import { required, email, minLength, between } from 'vuelidate/lib/validators';
import {loadLocaleMessages} from '../services/service';
import store from '../store';
import axios from "axios";

export default {
  components: {
    SigninSlider,
    SigninFooter,
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
            _this.$refs.signinFooter.$forceUpdate()
      },

    handleSubmit(e) {
      this.submitted = true;
      // Stop here if form is invalid
      this.$v.$touch();
      if (this.$v.$invalid) {
        return;
      }
      // Forgot Password API call with params email address
      axios.post(process.env.VUE_APP_API_ENDPOINT + "request_password_reset", this.forgotPassword)
        .then(response => {
          this.message = null;
          this.showDismissibleAlert = true
          this.classVariant = 'success'
          // Set success message
          this.message = response.data.message  
          //Reset to blank
          this.submitted = false;
          this.forgotPassword.email = '';
          this.$v.$reset();
        })
        .catch(error => {
          if(error.response.data.errors[0].message){
            this.message = null;
            this.showDismissibleAlert = true
            this.classVariant = 'danger'
            // Set error message
            this.message = error.response.data.errors[0].message
          }
        })
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
