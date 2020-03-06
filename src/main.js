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
import 'moment-timezone';
import customCss from './services/CustomCss'
import 'vue-search-select/dist/VueSearchSelect.css'

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
    if (store.state.isLoggedIn) {
        if(store.state.isProfileComplete != 1) {
           if(to.path != '/my-account') {
                next({
                    name: "myAccount"
                });
                return;
           }
        }
    }
    // if from path is (/) then we need to call custom css call and wait for its reponse 
    if (to.path == '/') {
        document.body.classList.add("loader-enable");
        setTimeout(() => {
            document.body.classList.remove("loader-enable");
        }, 700)
    }
    if ((from.path == '/' && to.path == '/') || from.path == '/') {
        // document.body.classList.add("loader-enable");
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
router.afterEach((to) => {
    if (to.path == '/') {
        setTimeout(() => {
            document.body.classList.remove("loader-enable");
        }, 500)
    }
})
Vue.filter('formatDate', (value) => {
    if (value) {
        return moment(String(value)).format('DD/MM/YYYY')
    }
})

Vue.filter('formatStoryDate', (value) => {
    return moment(value, 'DD/MM/YYYY HH:mm:ss').format('DD/MM/YYYY');
})

Vue.filter('formatDateTime', (value) => {
    return moment(String(value)).format('DD/MM/YYYY, LT')
})



Vue.filter('filterGoal', (value) => {
    return parseInt(value)
})

Vue.filter('formatTime', (value) => {
    return moment(String(value)).format('LT')
})

Vue.filter('firstLetterCapital', (value) => {
    if (value) {
        value = value.toLowerCase()
        return value.charAt(0).toUpperCase() + value.slice(1)
    }
})

Vue.filter('firstLetterSmall', (value) => {
    if (value) {
        return value.toLowerCase()
    }
})


Vue.filter('substring', (value, data) => {
    if (value.length <= data) {
        return value
    } else {
        return value.substring(0, data) + "...";
    }
});

window.addEventListener('storage', function (e) {
    if (event.key == 'logout-event') { 
        location.reload();
    }
},false);

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