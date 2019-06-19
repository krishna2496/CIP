import Vue from 'vue'
import VueI18n from 'vue-i18n'
import axios from 'axios'
import store from './store'
import {loadLocaleMessages} from './services/service';
Vue.use(VueI18n)

export default new VueI18n({
    locale: store.state.defaultLanguage,
    fallbackLocale: store.state.defaultLanguage,
    messages: []
})