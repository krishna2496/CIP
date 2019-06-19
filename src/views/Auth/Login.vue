<template>
    <div class="signin-page-wrapper">
        <Slider v-if="isShowComponent"/>
        <div class="signin-form-wrapper">
            <div class="lang-drodown-wrap">
                <CustomDropdown :optionList="langList" :defaultText="defautLang" 
                translationEnable= "false" 
                @updateCall="setLanguage" v-if="isShowComponent" />
            </div>
            <div class="signin-form-block">
                <router-link to="/" class="logo-wrap" v-if="this.$store.state.logo">
                    <img :src="this.$store.state.logo">
                </router-link>
                <!-- success or error msg -->
                <b-alert show :variant="classVariant" dismissible v-model="showDismissibleAlert"> {{ message }}</b-alert>
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
                        @keypress.enter.prevent="handleSubmit"></b-form-input>
                        <div v-if="submitted && !$v.login.password.required" class="invalid-feedback">
                        {{ $t("errors.password_required") }}</div>
                        <div v-if="submitted && !$v.login.password.minLength" class="invalid-feedback">
                        {{ $t("errors.invalid_password") }}</div>
                    </b-form-group>
                    <b-button type="button" @click="handleSubmit" class=" btn-bordersecondary">
                    {{ $t("label.login") }}</b-button>
                </b-form>
                <!-- link to forgot-password -->
                <div class="form-link">
                    <b-link to="/forgot-password">{{ $t("label.lost_password") }}</b-link>
                </div>
            </div>
            <PrimaryFooter ref="PrimaryFooter" v-if="isShowComponent"/>
        </div>
    </div>
</template>

<script>
import Slider from '../../components/Slider';
import PrimaryFooter from "../../components/Layouts/PrimaryFooter";
import CustomDropdown from '../../components/CustomDropdown';
import { required, email, minLength, between } from 'vuelidate/lib/validators';
import store from '../../store';
import axios from "axios";
import {storeTenantOption,loadLocaleMessages} from '../../services/service';
import { mapActions } from 'vuex';

export default {
    components: {       
        PrimaryFooter,
        CustomDropdown,
        Slider,
    },
    data() {
        return {
            flag: false,
            myValue: '',
            defautLang: 'EN',
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
            password: {required, minLength: minLength(8)}
        }
    },
    methods: {
        async createConnection(){
            await axios.get(process.env.VUE_APP_API_ENDPOINT+"connect")
                .then((response) => {
                    if (response.data.data) {
                        //store tenant option to Local Storage
                        storeTenantOption(response.data.data,this.langList,this.defautLang);                           
                        //Get langauage list from Local Storage
                        this.langList = JSON.parse(store.state.listOfLanguage)
                        this.defautLang = store.state.defaultLanguage
                        this.isShowComponent = true                          
                    }else{
                        var dataList = [];
                        storeTenantOption(dataList,this.langList,this.defautLang);
                    } 
                   
                })
                .catch(error => {
                    // this.createConnection();
                })
               
        },
        
        async setLanguage(language){
            var _this = this;
            this.defautLang = language.selectedVal;
            store.commit('setDefaultLanguage',language);
            this.$i18n.locale = language.selectedVal.toLowerCase()
            await loadLocaleMessages(this.$i18n.locale);   
            _this.$forceUpdate();
            _this.$refs.Footer.$forceUpdate()
        },

        handleSubmit(e) {
            this.submitted = true;
            this.$v.$touch();
            // stop here if form is invalid
            if (this.$v.$invalid) {
                return;
            }
            // login api call with params email address and password
            axios.post(process.env.VUE_APP_API_ENDPOINT+"login", this.login,
               ).then((response) => {
                        //Store token in local storage                            
                        store.commit('loginUser',response.data.data)
                        //redirect to landing page
                        this.$router.replace({ name: "home" });
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
    mounted() {
        //Autofocus
        this.$refs.email.focus();        
    },        
    created() {
        //Database connection and fetching tenant options api
        this.createConnection()
    },
};
</script>