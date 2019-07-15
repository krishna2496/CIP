import Vue from 'vue'
import Vuex from 'vuex'
import axios from "axios";
import router from './router'
Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        isLoggedIn: !!localStorage.getItem('token'),
        token: localStorage.getItem('token'),
        listOfLanguage: localStorage.getItem('listOfLanguage'),
        defaultLanguage: localStorage.getItem('defaultLanguage'),
        defaultLanguageId: localStorage.getItem('defaultLanguageId'),
        slider: localStorage.getItem('slider'),
        userId: localStorage.getItem('userId'),
        firstName: localStorage.getItem('firstName'),
        lastName: localStorage.getItem('lastName'),
        avatar: localStorage.getItem('avatar'),
        isloaderSet: true,
        logo: localStorage.getItem('logo'),
        search : localStorage.getItem('search'),
        exploreMissionType : '',
        exploreMissionParams : '',
        menubar : localStorage.getItem('menubar'),
        imagePath: localStorage.getItem('imagePath'),
        countryId : localStorage.getItem('countryId'),
        cityId : localStorage.getItem('cityId'),
        themeId : localStorage.getItem('themeId'),
        skillId : localStorage.getItem('skillId'),
        tags : localStorage.getItem('tags'),
        sortBy : localStorage.getItem('sortBy'),
    },
    mutations: {
        // Set login data in state and local storage       
        loginUser(state, data) {
            localStorage.setItem('isLoggedIn', data.token)
            localStorage.setItem('token', data.token)
            localStorage.setItem('userId', data.user_id)
            localStorage.setItem('firstName', data.first_name)
            localStorage.setItem('lastName', data.last_name)
            localStorage.setItem('avatar', data.avatar)
            state.isLoggedIn = true;
            state.token = data.token;
            state.userId = data.user_id;
            state.firstName = data.first_name;
            state.lastName = data.last_name;
            state.avatar = data.avatar;
        },
        // Remove login data in state and local storage
        logoutUser(state) {
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
            router.push({
                name: 'login'
            })
        },
        // Set default language code and id data in state and local storage
        setDefaultLanguage(state, language) {
            localStorage.removeItem('defaultLanguage');
            localStorage.removeItem('defaultLanguageId');
            localStorage.setItem('defaultLanguage', language.selectedVal);
            localStorage.setItem('defaultLanguageId', language.selectedId);
            state.defaultLanguage = language.selectedVal;
            state.defaultLanguageId = language.selectedId;
        },
        // Set slider in state and local storage
        setSlider(state, slider) {
            localStorage.removeItem('slider');
            localStorage.setItem('slider', slider);
            state.slider = slider;
        },
        // Set language list in state and local storage
        setLanguageList(state, languageList, ) {
            localStorage.removeItem('listOfLanguage');
            localStorage.setItem('listOfLanguage', languageList);
            state.listOfLanguage = languageList;
        },
        // Set logo in state and local storage
        setLogo(state, logo) {
            localStorage.removeItem('logo');
            localStorage.setItem('logo', logo)
            state.logo = logo;
        },
        // User filter data
        userFilter(state,filters) {
            localStorage.setItem('search',filters.search)
            localStorage.setItem('countryId',filters.countryId)
            localStorage.setItem('cityId',filters.cityId)
            localStorage.setItem('themeId',filters.themeId)
            localStorage.setItem('skillId',filters.skillId)
            localStorage.getItem('tags',JSON.stringify(filters.tags))
            localStorage.getItem('sortBy',JSON.stringify(filters.sortBy)),
            state.search = filters.search
            state.countryId = filters.countryId
            state.cityId = filters.cityId
            state.themeId = filters.themeId
            state.skillId = filters.skillId
            state.tags = JSON.stringify(filters.tags)
            state.sortBy = filters.sortBy
        },
        
        // Explore data
        exploreFilter(state,filters) {
            localStorage.setItem('exploreMissionType',filters.exploreMissionType)
            localStorage.setItem('exploreMissionParams',filters.exploreMissionParams)   
            state.exploreMissionType = filters.exploreMissionType
            state.exploreMissionParams = filters.exploreMissionParams
        },
        // User filter data
        headerMenu(state,headerMenuData)
         {
            localStorage.setItem('menubar',JSON.stringify(headerMenuData)) 
            state.menubar = JSON.stringify(headerMenuData)
        },
        setImagePath(state, path) {
            localStorage.setItem('imagePath', path);
            state.imagePath = path;
        }
    },
    getters: {},
    actions: {}
});