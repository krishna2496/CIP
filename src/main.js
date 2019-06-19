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
import i18n from "./i18n";
import AOS from "aos";
import "aos/dist/aos.css";
import BackToTop from "vue-backtotop";

Vue.use(Vuelidate, VueAxios, axios);

Vue.config.productionTip = false;

Vue.use(BootstrapVue);
Vue.use(VueScrollTo);
Vue.use(BackToTop);
AOS.init({
    once: true,
    easing: "ease-in-out",
    duration: 700,
    offset: 0
});
// call vue axios interceptors
interceptorsSetup();
// check requirment of authentication for path
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.state.isLoggedIn) {
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

new Vue({
    router,
    store,
    BootstrapVue,
    custom,
    SimpleBar,
    VueScrollTo,
    i18n,
    AOS,
    BackToTop,
    render: h => h(App)
}).$mount("#app");