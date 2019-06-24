import store from '../../store'
import axios from 'axios'

export default async(langList) => {
    let responseData = {}
    responseData.error = false;
    defautLang = 'en';
    defautLangId =1;
    await axios.get(process.env.VUE_APP_API_ENDPOINT + "connect")
        .then((response) => {
            if (response.data.data) {
                let data = response.data.data;
                // Store slider in Local Storage
                if (data.slider) {
                    // Convert slider object to array
                    let listOfSliderObjects = Object.keys(data.slider).map((key) => {
                        return data.slider[key]
                    })

                    store.commit('setSlider', JSON.stringify(data.slider))
                } else {
                    var sliderData = [];
                    store.commit('setSlider', JSON.stringify(sliderData))
                }

                // Store language in Local Storage
                if (data.language) {

                    let defaultLanguageDataChange = true

                    let listOfObjects = Object.entries(data.language);
                    
                    listOfObjects.forEach(function(listOfLangauge) {
                        if (listOfLangauge[0] == store.state.defaultLanguageId && listOfLangauge[1] == store.state.defaultLanguage) {
                            defaultLanguageDataChange = false;
                        }
                    });

                    // If options exist
                    if (listOfObjects) {

                        store.commit('setLanguageList', JSON.stringify(listOfObjects))

                        if (defaultLanguageDataChange == true) {
                            var defaultLanguageData = []
                            defaultLanguageData["selectedVal"] = (data.defaultLanguage) ? data.defaultLanguage : listOfObjects[0][1];
                            defaultLanguageData["selectedId"] = (data.defaultLanguageId) ? data.defaultLanguageId : listOfObjects[0][0];
                            store.commit('setDefaultLanguage', defaultLanguageData)
                        }

                    } else {
                        store.commit('setLanguageList', JSON.stringify(langList))
                        var defaultLanguageData = []
                        defaultLanguageData["selectedVal"] = (data.defaultLanguage) ? data.defaultLanguage : defautLang;
                        defaultLanguageData["selectedId"] = (data.defaultLanguageId) ? data.defaultLanguageId :defautLangId;
                        store.commit('setDefaultLanguage', defaultLanguageData)
                    }

                } else {
                    store.commit('setLanguageList', JSON.stringify(langList))
                    var defaultLanguageData = []
                    defaultLanguageData["selectedVal"] = defautLang;
                    defaultLanguageData["selectedId"] = "";
                    store.commit('setDefaultLanguage', defaultLanguageData)
                }

                //Set logo in local storage
                var logo = '';
                if (data.custom_logo) {
                    var logo = data.custom_logo;
                } 
                store.commit('setLogo', logo)

            } else {
                defaultLanguageData["selectedVal"] = defautLang;
                defaultLanguageData["selectedId"] = defautLangId;
                store.commit('setDefaultLanguage', defaultLanguageData)
                localStorage.removeItem('slider');
                localStorage.removeItem('listOfLanguage');
                localStorage.removeItem('defaultLanguage');
                localStorage.removeItem('defaultLanguageId');
                localStorage.removeItem('logo');
                var sliderData = [];
                store.commit('setSlider', JSON.stringify(sliderData))
            }

        })
        .catch(error => {})
    return responseData;
}