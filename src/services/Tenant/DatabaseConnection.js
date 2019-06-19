import store from '../../store'
import axios from 'axios'
import storeTenantOption from "./TenantOption";

export default async(langList,defautLang) => {
    await axios.get(process.env.VUE_APP_API_ENDPOINT+"connect")
                    .then((response) => {
                        if (response.data.data) {
                            //store tenant option to Local Storage
                            storeTenantOption(response.data.data,langList,defautLang);                           
                        } else {
                            localStorage.removeItem('slider');  
                            localStorage.removeItem('listOfLanguage');
                            localStorage.removeItem('defaultLanguage');
                            localStorage.removeItem('defaultLanguageId');
                            var sliderData = [];
                            store.commit('setSlider',JSON.stringify(sliderData))
                        } 
                       
                    })
                    .catch(error => {})                 
}
