import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import App from './App.vue'
import router from './router'
import store from './store'
import custom from './assets/scss/custom.scss'
import SimpleBar from 'simplebar'
import 'simplebar/dist/simplebar.css'

Vue.config.productionTip = false

Vue.use(BootstrapVue)

new Vue({
	router,
	store,
	BootstrapVue,
	custom,
	SimpleBar,
	render: h => h(App)
}).$mount('#app')
