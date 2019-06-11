<template>
    <div class="signin-page-wrapper">
        <SigninSlider/>
        <div class="signin-form-wrapper">
        <div class="lang-drodown-wrap">
        <CustomDropdown :optionList="langList" :default_text="defautLang" @updateCall="setLanguage"/>
        </div>
        <div class="signin-form-block">
        <div class="form-title-block">
        <h1>{{ $t("label.new_password") }}</h1>
        <p>{{ $t("label.new_password_messgae") }}</p>
        </div>
        <!-- success or error msg -->
        <b-alert show :variant="classVariant" dismissible v-model="showDismissibleAlert"> {{ message }}</b-alert>

        <!-- reset password form start -->
        <b-form class="signin-form">

        <b-form-group :state="false">
        <label for="">{{ $t("label.new_password") }}</label>
        <b-form-input id="" type="password" v-model="resetPassword.password" :class="{ 'is-invalid': $v.resetPassword.password.$error }" value="Password" 
        v-bind:placeholder='$t("placeholder.password")' 
        autofocus></b-form-input>
        <div v-if="submitted && !$v.resetPassword.password.required" class="invalid-feedback">
            {{ $t("errors.password_required") }}
        </div>
        <div v-if="submitted && !$v.resetPassword.password.minLength" class="invalid-feedback">
            {{ $t("errors.invalid_password") }}
        </div>
        </b-form-group>

        <b-form-group>
        <label for="">{{ $t("label.confirm_new_password") }}</label>
        <b-form-input id="" type="password" v-model="resetPassword.confirmPassword" :class="{ 'is-invalid': $v.resetPassword.confirmPassword.$error }" 
        v-bind:placeholder='$t("placeholder.password")' 
        @keypress.enter.prevent="handleSubmit" value="Password"></b-form-input>
        <div v-if="submitted && !$v.resetPassword.confirmPassword.required" class="invalid-feedback">
             {{ $t("errors.password_required") }}
        </div>
        <div v-if="submitted && !$v.resetPassword.confirmPassword.minLength" class="invalid-feedback">
             {{ $t("errors.invalid_password") }}
        </div>
        <div v-if="submitted && $v.resetPassword.confirmPassword.required && $v.resetPassword.confirmPassword.minLength && !$v.resetPassword.confirmPassword.sameAsPassword" class="invalid-feedback">
             {{ $t("errors.identical_password") }}
        </div>
        </b-form-group>

        <b-button type="button" @click="handleSubmit" class="btn btn-bordersecondary">
            {{ $t("label.change_password") }}
        </b-button>

        </b-form>

        <div class="form-link">
        <b-link to="/">{{ $t("label.login") }}</b-link>
        </div>
        </div>
        <SigninFooter/>
    </div>
    </div>
</template>
<script>
import SigninSlider from '../components/SigninSlider';
import SigninFooter from '../components/Footer/SigninFooter';
import CustomDropdown from '../components/CustomDropdown';
import store from '../store';
import { required, email,sameAs, minLength, between } from 'vuelidate/lib/validators';
import {loadLocaleMessages} from '../services/service';
import axios from "axios";

export default {
    components: {
    SigninSlider,
    SigninFooter, 
    CustomDropdown, 
},

data() {    
return {
    myValue: '',
    defautLang: "",
    langList:[],
    resetPassword: {
    email:'',
    password: '',
    confirmPassword: '',
    token:'',
    },
    submitted: false,
    classVariant: 'danger',
    message: null,
    showDismissibleAlert: false,
};
},

validations: {
    resetPassword: {
    password: {required, minLength: minLength(8)},
    confirmPassword: {required, minLength: minLength(8),sameAsPassword: sameAs('password')}
    }
},

methods:{
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
    this.$v.$touch();
    // stop here if form is invalid
    if (this.$v.$invalid) {
    return;
    }
    // reset password api call with params token,email,password,password_conformation
    axios.put(process.env.VUE_APP_API_ENDPOINT+"password_reset",{
        "reset_password_token":this.resetPassword.token,
        "email" :this.resetPassword.email,
        "password":this.resetPassword.password,
        "password_confirmation":this.resetPassword.confirmPassword
    }).then((response) => {
        this.message = null;
        this.showDismissibleAlert = true
        this.classVariant = 'success'
        //set success msg
        this.message = response.data.message 
        //Reset to blank
        this.submitted = false;
        this.resetPassword.password = ''
        this.resetPassword.confirmPassword = ''
        this.$v.$reset();  
    }) .catch(error => {
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

mounted() {

}, 

created() {
    //get token and email from url
    let tokenData = this.$route.path.split('/');
    this.resetPassword.token = tokenData[tokenData.length-1]
    this.resetPassword.email = this.$route.query.email  

    // set language list and default language fetching from local storage
    this.langList = (localStorage.getItem('listOfLanguage') !== null) ? JSON.parse(localStorage.getItem('listOfLanguage')) : []
    this.defautLang = localStorage.getItem('defaultLanguage') 

},


}
</script>