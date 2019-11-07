import Vue from 'vue'
import Vuex from 'vuex'
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
        search: localStorage.getItem('search'),
        exploreMissionType: '',
        exploreMissionParams: '',
        menubar: localStorage.getItem('menubar'),
        imagePath: localStorage.getItem('imagePath'),
        countryId: localStorage.getItem('countryId'),
        cityId: localStorage.getItem('cityId'),
        themeId: localStorage.getItem('themeId'),
        skillId: localStorage.getItem('skillId'),
        tags: localStorage.getItem('tags'),
        sortBy: localStorage.getItem('sortBy'),
        tenantSetting: localStorage.getItem('tenantSetting'),
        missionNotFoundText: localStorage.getItem('missionNotFoundText'),
        languageLabel: localStorage.getItem('languageLabel'),
        currentSkill: null,
        currentFromSkill: null,
        isTwitterDisplay: false,
        isFacebookDisplay: false,
        missionId: localStorage.getItem('missionId'),
        missionType: localStorage.getItem('missionType'),
        defaultCountryId: localStorage.getItem('defaultCountryId'),
        newsBanner: localStorage.getItem('newsBanner'),
        newsBannerText: localStorage.getItem('newsBannerText'),
        storyBanner: localStorage.getItem('storyBanner'),
        storyBannerText: localStorage.getItem('storyBannerText'),
        clearFilterSet: '',
        storyDashboardText: localStorage.getItem('storyDashboardText'),
        slideInterval: localStorage.getItem('slideInterval'),
        slideEffect: localStorage.getItem('slideEffect'),
        cookieAgreementDate: localStorage.getItem('cookieAgreementDate'),
        cookiePolicyText: localStorage.getItem('cookiePolicyText'),
        email: localStorage.getItem('email'),
        currentView: localStorage.getItem('currentView'),
        timesheetInitialYear: localStorage.getItem('timesheetInitialYear')
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
            localStorage.setItem('defaultCountryId', data.country_id)
            localStorage.setItem('cookieAgreementDate', data.cookie_agreement_date)
            localStorage.setItem('email', data.email)
            state.isLoggedIn = true;
            state.token = data.token;
            state.userId = data.user_id;
            state.firstName = data.first_name;
            state.lastName = data.last_name;
            state.avatar = data.avatar;
            state.defaultCountryId = data.country_id;
            state.cookieAgreementDate = data.cookie_agreement_date;
            state.email = data.email;
        },
        // Remove login data in state and local storage
        logoutUser(state) {
            localStorage.removeItem('token')
            localStorage.removeItem('userId')
            localStorage.removeItem('firstName')
            localStorage.removeItem('lastName')
            localStorage.removeItem('avatar')
            localStorage.removeItem('cookieAgreementDate')

            state.isLoggedIn = false;
            state.token = null;
            state.userId = null;
            state.firstName = null;
            state.lastName = null;
            state.avatar = null;
            state.cookieAgreementDate = null;
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
        userFilter(state, filters) {
            localStorage.setItem('search', filters.search)
            localStorage.setItem('countryId', filters.countryId)
            localStorage.setItem('cityId', filters.cityId)
            localStorage.setItem('themeId', filters.themeId)
            localStorage.setItem('skillId', filters.skillId)
            localStorage.setItem('tags', JSON.stringify(filters.tags))
            localStorage.setItem('sortBy', filters.sortBy)
            if (filters.currentView) {
                localStorage.setItem('currentView', filters.currentView)
                state.currentView = filters.currentView
            } else {
                localStorage.setItem('currentView', 0)
                state.currentView = 0
            }
            state.search = filters.search
            state.countryId = filters.countryId
            state.cityId = filters.cityId
            state.themeId = filters.themeId
            state.skillId = filters.skillId
            state.tags = JSON.stringify(filters.tags)
            state.sortBy = filters.sortBy

        },

        // Explore data
        exploreFilter(state, filters) {
            localStorage.setItem('exploreMissionType', filters.exploreMissionType)
            localStorage.setItem('exploreMissionParams', filters.exploreMissionParams)
            state.exploreMissionType = filters.exploreMissionType
            state.exploreMissionParams = filters.exploreMissionParams
        },
        // User filter data
        headerMenu(state, headerMenuData) {
            localStorage.setItem('menubar', JSON.stringify(headerMenuData))
            state.menubar = JSON.stringify(headerMenuData)
        },
        setImagePath(state, path) {
            localStorage.setItem('imagePath', path);
            state.imagePath = path;
        },
        // Set Sort by
        sortByFilter(state, data) {
            localStorage.setItem("sortBy", data);
            state.sortBy = data;
        },
        // Set tenant option
        setTenantSetting(state, data) {
            if (data != null) {
                localStorage.setItem("tenantSetting", JSON.stringify(data));
                state.tenantSetting = JSON.stringify(data);
            } else {
                localStorage.setItem("tenantSetting", data);
                state.tenantSetting = data;
            }
        },
        // Set mission not found text
        missionNotFound(state, data) {
            localStorage.setItem("missionNotFoundText", JSON.stringify(data));
            state.missionNotFoundText = JSON.stringify(data);
        },
        // Set language label
        setlanguageLabel(state, data) {
            localStorage.setItem("languageLabel", JSON.stringify(data));
            state.languageLabel = JSON.stringify(data);
        },
        changeToken(state, data) {
            localStorage.setItem('token', data)
            state.token = data;
        },
        changeAvatar(state, data) {
            localStorage.setItem('avatar', data.avatar)
            state.avatar = data.avatar;
        },
        changeUserDetail(state, data) {
            let langaugeCode = data.languageCode;
            localStorage.setItem('firstName', data.firstName)
            localStorage.setItem('lastName', data.lastName)
            localStorage.setItem('defaultLanguage', langaugeCode.toUpperCase())
            localStorage.setItem('defaultLanguageId', data.language);
            localStorage.setItem('countryId', data.country)
            localStorage.setItem('cityId', data.city)
            state.defaultLanguage = langaugeCode.toUpperCase()
            state.defaultLanguageId = data.language;
            state.firstName = data.firstName;
            state.lastName = data.lastName;
            state.countryId = data.country
            state.cityId = data.city
        },
        saveCurrentSkill(state, data) {
            // state.currentSkill = data;
            localStorage.setItem('currentSkill', JSON.stringify(data))
        },
        saveCurrentFromSkill(state, data) {
            // state.currentSkill = data;
            localStorage.setItem('currentFromSkill', JSON.stringify(data))
        },
        clearFilter(state) {
            let tag = []
            localStorage.setItem('search', '')
            localStorage.setItem('countryId', '')
            localStorage.setItem('cityId', '')
            localStorage.setItem('themeId', '')
            localStorage.setItem('skillId', '')
            localStorage.setItem('tags', JSON.stringify(tag))
            localStorage.setItem('sortBy', ''),
                state.search = ''
            state.countryId = ''
            state.cityId = ''
            state.themeId = ''
            state.skillId = ''
            state.tags = JSON.stringify(tag)
            state.sortBy = ''
        },
        // Set default language code and id data in state and local storage
        setDefaultLanguageCode(state, language) {
            localStorage.setItem('defaultLanguage', language);
            state.defaultLanguage = language;
        },

        timeSheetEntryDetail(state, data) {
            localStorage.setItem('missionId', data.missionId)
            localStorage.setItem('missionType', data.missionType)
            state.missionId = data.missionId
            state.missionType = data.missionType
        },
        removeTimeSheetDetail(state) {
            localStorage.removeItem('missionId');
            localStorage.removeItem('missionType');
            state.missionId = null
            state.missionType = null
        },
        newsBanner(state, data) {
            localStorage.setItem('newsBanner', data)
            state.newsBanner = data
        },
        newsBannerText(state, data) {
            localStorage.setItem('newsBannerText', JSON.stringify(data))
            state.newsBannerText = JSON.stringify(data)
        },
        storyBanner(state, data) {
            localStorage.setItem('storyBanner', data)
            state.storyBanner = data
        },
        storyBannerText(state, data) {
            localStorage.setItem('storyBannerText', JSON.stringify(data))
            state.storyBannerText = JSON.stringify(data)
        },
        clearFilterClick(state, data) {
            state.clearFilterSet = data
        },
        storyDashboardText(state, data) {
            localStorage.setItem('storyDashboardText', JSON.stringify(data))
            state.storyDashboardText = JSON.stringify(data)
        },
        slideInterval(state, data) {
            localStorage.setItem('slideInterval', data)
            state.slideInterval = data
        },
        slideEffect(state, data) {
            localStorage.setItem('slideEffect', data)
            state.slideEffect = data
        },
        removeCookieBlock(state) {
            localStorage.setItem('cookieAgreementDate', 1)
            state.cookieAgreementDate = 1;
        },
        cookiePolicyText(state, data) {
            localStorage.setItem('cookiePolicyText', JSON.stringify(data))
            state.cookiePolicyText = JSON.stringify(data)
        },
        timesheetInitialYear(state, data) {
            localStorage.setItem('timesheetInitialYear', data)
            state.timesheetInitialYear = data
        }
    },
    getters: {},
    actions: {}
})