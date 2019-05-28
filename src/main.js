import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import App from './App.vue'
import router from './router'
import store from './store'
import custom from './assets/scss/custom.scss'
import SimpleBar from 'simplebar'
import 'simplebar/dist/simplebar.css'
import axios from "axios";
import VueAxios from "vue-axios";
import Vuelidate from 'vuelidate'
import interceptorsSetup from './interceptors'

Vue.use(Vuelidate,VueAxios,axios)

Vue.config.productionTip = false

Vue.use(BootstrapVue)

// call vue axios interceptors
interceptorsSetup();

new Vue({
	router,
	store,
	BootstrapVue,
	custom,
	SimpleBar,
	render: h => h(App)
}).$mount('#app')

