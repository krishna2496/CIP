<template>
    <div class="signin-page-wrapper">
        <SigninSlider/>
        <div class="signin-form-wrapper">

            <div class="lang-drodown-wrap">
                <customDropdown :optionList="langList" :default_text="defaut_lang" />
            </div>

            <div class="signin-form-block">

                <i class="logo-wrap">
                    <img src="../assets/images/optimy-logo.png">
                </i>

                <!-- success or error msg -->
                <b-alert show :variant="classVariant" dismissible v-model="showDismissibleAlert"> {{ message }}</b-alert>

                <!-- login form start -->
                <b-form class="signin-form">
                    <b-form-group>
                        <label for="">Email Address</label>
                        <b-form-input id="" type="email" v-model="login.email" placeholder="Enter email" :class="{ 'is-invalid': $v.login.email.$error }" ref='email' autofocus></b-form-input>
                        <div v-if="submitted && !$v.login.email.required" class="invalid-feedback">Email address is required</div>
                        <div v-if="submitted && !$v.login.email.email" class="invalid-feedback">Enter valid email address</div>
                    </b-form-group>
                    <b-form-group>
                        <label for="">Password</label>
                        <b-form-input id="" type="password" v-model="login.password" required placeholder="Enter Password" :class="{ 'is-invalid': $v.login.password.$error }"></b-form-input>
                        <div v-if="submitted && !$v.login.password.required" class="invalid-feedback">Password is required</div>
                        <div v-if="submitted && !$v.login.password.minLength" class="invalid-feedback">Password lenght should be minimum 8 character</div>
                    </b-form-group>
                    <b-button type="button" @click="handleSubmit" class=" btn-bordersecondary">Login</b-button>
                </b-form>

                <!-- link to forgot-password -->
                <div class="form-link">
                    <b-link to="/forgot-password">Lost your password?</b-link>
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
    import { required, email, minLength, between } from 'vuelidate/lib/validators';
    import store from '../store';
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
                defaut_lang: 'EN',
                langList: [],
                login: {
                    email: '',
                    password: '',
                },
                submitted: false,
                classVariant: 'danger',
                message: null,
                showDismissibleAlert: false,
                
            };
        },

        validations: {
            login: {
                email: {required, email},
                password: {required, minLength: minLength(8)}
            }
        },

        computed: {

        },

        methods: {
            handleSubmit(e) {
                this.submitted = true;
                this.$v.$touch();
                // stop here if form is invalid
                if (this.$v.$invalid) {
                    return;
                }

                // login api call with params email address and password
                axios.post(process.env.VUE_APP_API_ENDPOINT+"login", this.login,
                   )
                        .then((response) => {
                            //store token in local storage
                            localStorage.setItem('isLoggedIn', response.data.data.token)
                            localStorage.setItem('token', response.data.data.token)
                            store.commit('loginUser', response.data.data.token)
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
            //database connection and fetching tenant options api
            axios.get(process.env.VUE_APP_API_ENDPOINT+"/connect")
                    .then((response) => {

                        if (response.data.data.language) {

                            //convert language object to array
                            let listOfObjects = Object.keys(response.data.data.language).map((key) => {
                                return response.data.data.language[key]
                            })

                            //if options exist
                            if(listOfObjects){
                                localStorage.setItem('listOfLanguage',JSON.stringify(listOfObjects))
                                localStorage.setItem('defaultLanguage',listOfObjects[0])
                                this.langList = JSON.parse(localStorage.getItem('listOfLanguage'))
                                this.defaut_lang = localStorage.getItem('defaultLanguage') 
                            }else{
                                localStorage.setItem('listOfLanguage',this.langList)
                                localStorage.setItem('defaultLanguage',this.defaut_lang)
                            }
                            
                        }else{
                            localStorage.removeItem('listOfLanguage');
                            localStorage.removeItem('defaultLanguage');
                            localStorage.setItem('listOfLanguage',JSON.stringify(this.langList))
                            localStorage.setItem('defaultLanguage',this.defaut_lang)
                        }

                    })
                    .catch(error => {
                        console.log(error)
                    })
        },

        updated(){
            
        }
    };

</script>
<style lang="scss">
</style>