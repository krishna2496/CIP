<template>
  <div class="signin-page-wrapper">
    <SigninSlider/>
    <div class="signin-form-wrapper">
      <div class="lang-drodown-wrap">
         <customDropdown :optionList="langList" :default_text="defaut_lang" />
      </div>
      <div class="signin-form-block">

        <div class="form-title-block">
          <h1>New Password</h1>
          <p>Please enter a new password in the fields below.</p>
        </div>

         <!-- success or error msg -->
        <b-alert show :variant="classVariant" dismissible v-model="showDismissibleAlert"> {{ message }}</b-alert>

        <!-- reset password form start -->
        <b-form class="signin-form">

          <b-form-group :state="false">
            <label for="">New Password</label>
            <b-form-input id="" type="password" v-model="resetPassword.password" :class="{ 'is-invalid': $v.resetPassword.password.$error }" value="Password" placeholder="Enter Password"></b-form-input>
            <div v-if="submitted && !$v.resetPassword.password.required" class="invalid-feedback">Password is required</div>
            <div v-if="submitted && !$v.resetPassword.password.minLength" class="invalid-feedback">Password lenght should be minimum 6 character</div>
          </b-form-group>

          <b-form-group>
            <label for="">Confirm New Password</label>
            <b-form-input id="" type="password" v-model="resetPassword.confirmPassword" :class="{ 'is-invalid': $v.resetPassword.confirmPassword.$error }" placeholder="Enter Password" value="Password"></b-form-input>
            <div v-if="submitted && !$v.resetPassword.confirmPassword.required" class="invalid-feedback">Confirm  Password is required</div>
            <div v-if="submitted && !$v.resetPassword.confirmPassword.minLength" class="invalid-feedback">Confirm Password lenght should be minimum 6 character</div>
            <div v-if="submitted && !$v.resetPassword.confirmPassword.sameAsPassword" class="invalid-feedback">Passwords must be identical.</div>
          </b-form-group>

          <b-button type="button" @click="handleSubmit" class="btn btn-bordersecondary" title="Change Password">Change Password</b-button>
        </b-form>

        <div class="form-link">
        <b-link href="/" title="resetPassword">resetPassword</b-link>

  </div>
  </div>
  <SigninFooter/>
        </div>
    </div>
</template>
<script>
import SigninSlider from '../components/SigninSlider';
import SigninFooter from '../components/Footer/SigninFooter';
import customDropdown from '../components/customDropdown';
import { required, email,sameAs, minLength, between } from 'vuelidate/lib/validators';
import axios from "axios";

export default {
  components: {
   SigninSlider,
    SigninFooter, 
    customDropdown, 
  },
  data() {
    return {
      myValue: '',
      defaut_lang: "EN",
      langList:[],
      resetPassword: {
          password: '',
          confirmPassword: '',
      },
      submitted: false,
      classVariant: 'danger',
      message: null,
      showDismissibleAlert: false,
    };
  },

  validations: {
        resetPassword: {
            password: {required, minLength: minLength(6)},
            confirmPassword: {required, minLength: minLength(6),sameAsPassword: sameAs('password')}
        }
  },

  methods:{
          handleSubmit(e) {
                  this.submitted = true;
                  // stop here if form is invalid
                  this.$v.$touch();

                  if (this.$v.$invalid) {
                      return;
                  }
                  return false;
                  // login api call with params email address and password
                  axios.post(process.env.VUE_APP_API_ENDPOINT+"login", this.login,
                     )
                          .then((response) => {
                              this.message = null;
                              this.showDismissibleAlert = true
                              this.classVariant = 'success'

                              //store token in local storage
                              localStorage.setItem('isLoggedIn', response.data.data.token)
                              localStorage.setItem('token', response.data.data.token)
                              store.commit('loginUser', response.data.data.token)
                              //set success msg
                              this.message = response.data.message   
                          })
                          .catch(error => {
                              if(error.response.data.errors[0].message){
                                  this.message = null;
                                  this.showDismissibleAlert = true
                                  this.classVariant = 'danger'
                                  //set error msg
                                  this.message = error.response.data.errors[0].message  
                              }

                          })
              },
    },

  created() {
    // set language list and default language fetching from local storage
    this.langList = JSON.parse(localStorage.getItem('listOfLanguage'))
    this.defaut_lang = localStorage.getItem('defaultLanguage') 
  
  },

  
}
</script>
<style lang="scss">

</style>