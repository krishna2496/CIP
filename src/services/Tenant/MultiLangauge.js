import axios from 'axios'
import i18n from "../../i18n"

function setI18nLanguage (lang) {
  i18n.locale = lang
  axios.defaults.headers.common['X-localization'] = lang
  document.querySelector('html').setAttribute('lang', lang)
  return lang;
}

export default async (lang) => {
      if (lang) { 
        lang = lang.toLowerCase();
      }else{
        lang = process.env.VUE_APP_I18N_LOCALE;
      }
      await axios
        .get(`http://localhost/locales/${lang}.json`, {
          method: "get",
          headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*"
          }
        })
        .then(function(res) {            
            i18n.setLocaleMessage(
                i18n.locale,
                res.data
            );            
            return Promise.resolve(setI18nLanguage(lang))
        });
}

