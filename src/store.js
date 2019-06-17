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
        userId : localStorage.getItem('userId'),
        firstName : localStorage.getItem('firstName'),
        lastName : localStorage.getItem('lastName'),
        avatar : localStorage.getItem('avatar'),
        isloaderSet : true
        isloaderSet:true,
        logo : localStorage.getItem('logo'),
    },
    mutations: {        
        loginUser(state, data){
            localStorage.setItem('isLoggedIn',data.token)
            localStorage.setItem('token',data.token)
            localStorage.setItem('userId',data.user_id)
            localStorage.setItem('firstName',data.first_name)
            localStorage.setItem('lastName',data.last_name)
            localStorage.setItem('avatar',data.avatar)                           
            state.isLoggedIn = true;
            state.token = data.token;            
            state.userId = data.user_id;
            state.firstName = data.first_name;
            state.lastName = data.last_name;
            state.avatar = data.avatar;
        },
        logoutUser(state){
            localStorage.removeItem('token')
            localStorage.removeItem('userId')
            localStorage.removeItem('firstName')
            localStorage.removeItem('lastName')
            localStorage.removeItem('avatar')
            state.isLoggedIn = false;
            state.token = null;
            state.userId = null;
            state.firstName = null;
            state.lastName = null;
            state.avatar = null;
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
            localStorage.setItem('slider',slider);
            state.slider = slider;
        },
        setLanguageList(state, languageList,){   
            localStorage.removeItem('listOfLanguage');
            localStorage.setItem('listOfLanguage',languageList);
            state.listOfLanguage = languageList;
        },
        setLogo(state,logo){   
            localStorage.removeItem('logo');
            localStorage.setItem('logo',logo)
            state.logo = logo;
        },
    },
    getters: {
    // list: state => state.list
    },
    actions: {
        
    }
});
