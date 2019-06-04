import Vue from 'vue'
import Vuex from 'vuex'
import axios from "axios";
import router from './router'
Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        isLoggedIn: !!localStorage.getItem('token'),
        token: localStorage.getItem('token'),
        listOfLanguage : localStorage.getItem('listOfLanguage'),
        defaultLanguage : localStorage.getItem('defaultLanguage'),
        defaultLanguageId : localStorage.getItem('defaultLanguageId'),
        slider : localStorage.getItem('slider'),
        isloaderSet:true
    },
    mutations: {        
        loginUser(state, token){
            state.isLoggedIn = true;
            state.token = token;
        },
        logoutUser(state){
            localStorage.removeItem('token')
            state.isLoggedIn = false;
            state.token = null;
            router.push({name: 'login'})
        },
        setDefaultLanguage(state, language){   
            localStorage.removeItem('defaultLanguage');
            localStorage.removeItem('defaultLanguageId');
            localStorage.setItem('defaultLanguage', language.selectedVal);
            localStorage.setItem('defaultLanguageId', language.selectedId);
            state.defaultLanguage = language.selectedVal;
            state.defaultLanguageId = language.selectedId;
        },
        setSlider(state, slider){
            localStorage.removeItem('slider');
            localStorage.setItem('slider', slider);
            state.slider = slider;
        }
    },
    getters: {
    // list: state => state.list
    },
    actions: {
        
    }
});
