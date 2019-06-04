import Vue from 'vue'
import VueI18n from 'vue-i18n'
import axios from 'axios'
import store from './store'

Vue.use(VueI18n)

/*function loadLocaleMessages () {
  const locales = require.context('./locales', true, /[A-Za-z0-9-_,\s]+\.json$/i)
  const messages = {}
  locales.keys().forEach(key => {
    const matched = key.match(/([A-Za-z0-9-_]+)\./i)
    if (matched && matched.length > 1) {
      const locale = matched[1]
      messages[locale] = locales(key)
    }
  })
  console.log(messages);
  return messages
}*/

function loadLocaleMessages (lang = 'en') {
  var messages = {};
  axios.get(`http://localhost/locales/${lang}.json`, {
      method: 'get',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*'
      }
    }).then(function (res) {      
      messages[`${lang}`] = res.data;     
    });  
    return messages;
}
export default new VueI18n({
  locale: process.env.VUE_APP_I18N_LOCALE || 'en',
  fallbackLocale: process.env.VUE_APP_I18N_FALLBACK_LOCALE || 'en',
  messages: loadLocaleMessages()
})
