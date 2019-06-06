import axios from 'axios'
import i18n from "../../i18n"

const loadedLanguages = []
var _this = this

function setI18nLanguage (lang) {
    i18n.locale = lang
    axios.defaults.headers.common['X-localization'] = lang
    document.querySelector('html').setAttribute('lang', lang)
    return lang;
}

export default async (lang) => {
    
    if (lang) { 
        lang = lang.toLowerCase();
    } 
    
    if (!loadedLanguages.includes(lang) && (lang != '')) {
            
        await axios.get(`${process.env.VUE_APP_LANGUAGE_API_ENDPOINT+lang}`, {
            method: "get",
            headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*"
        }}).then(function(res) {
            if(res.data){
                i18n.setLocaleMessage(
                    res.data.locale,
                    res.data.data
                );
                loadedLanguages.push(res.data.locale)
                return Promise.resolve(setI18nLanguage(res.data.locale)) 
            }      
        }).catch(error => {
            alert("Something went wrong! please try again.");
            return false;
        });

    }
}






