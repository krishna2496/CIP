import Vue from "vue";
import VueScrollTo from "vue-scrollto";
import BootstrapVue from "bootstrap-vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import custom from "./assets/scss/custom.scss";
import SimpleBar from "simplebar";
import "simplebar/dist/simplebar.css";
import axios from "axios";
import VueAxios from "vue-axios";
import Vuelidate from "vuelidate";
import interceptorsSetup from "./interceptors";
import toast from "./toast";
import i18n from "./i18n";
import AOS from "aos";
import "aos/dist/aos.css";
import BackToTop from "vue-backtotop";
import moment from 'moment'
import customCss from './services/CustomCss'

Vue.use(Vuelidate, VueAxios, axios);
Vue.config.devtools = true
Vue.config.productionTip = false;
Vue.use(BootstrapVue);
Vue.use(VueScrollTo);
Vue.use(BackToTop);
Vue.use(toast);

AOS.init({
    once: true,
    easing: "ease-in-out",
    duration: 700,
    offset: 0
});
export const eventBus = new Vue();
// call vue axios interceptors
interceptorsSetup();
let entryUrl = null;



// check requirment of authentication for path
router.beforeEach(async(to, from, next) => {
    // if from path is (/) then we need to call custom css call and wait for its reponse    
    if ((from.path == '/' && to.path == '/') || from.path == '/') {
        document.body.classList.add("loader-enable");
        await customCss().then(() => {
            document.body.classList.remove("loader-enable");
        });
    }
    if (store.state.isLoggedIn) {
        if (entryUrl) {
            const url = entryUrl;
            entryUrl = null;
            return next(url); // goto stored url
        }
    }
    if (to.meta.requiresAuth && !store.state.isLoggedIn) {
        entryUrl = to.path;
        next({
            name: "login"
        });
        return;
    }
    if ((to.path === "/" || to.path === "/forgot-password" || to.path === "/reset-password") &&
        store.state.isLoggedIn) {
        next({
            name: "home"
        });
        return;
    }
    next();
});

Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('DD/MM/YYYY')
    }
})

Vue.filter('filterGoal', function(value) {
    return parseInt(value)
})

Vue.filter('formatTime', function(value) {
    if (value) {
        let splitArray = value.split(":");

        return splitArray[0] + ':' + splitArray[1]
    }
})

Vue.filter('firstLetterCapital', function(value) {
    if (value) {
        value = value.toLowerCase()
        return value.charAt(0).toUpperCase() + value.slice(1)
    }
})

Vue.filter('substring', function(value, data) {
    if (value.length <= data) {
        return value
    } else {
        return value.substring(0, data) + "...";
    }
});

Vue.mixin({
    methods: {
        settingEnabled(key) {
            let settingArray = JSON.parse(store.state.tenantSetting)

            if (settingArray != null) {
                if (settingArray.indexOf(key) !== -1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
})

new Vue({
    router,
    store,
    BootstrapVue,
    custom,
    SimpleBar,
    VueScrollTo,
    i18n,
    AOS,
    toast,
    BackToTop,
    render: h => h(App)
}).$mount("#app");