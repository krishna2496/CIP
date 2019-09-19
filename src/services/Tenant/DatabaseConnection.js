import store from '../../store'
import axios from 'axios'

export default async(langList, defautLang) => {
    let responseData = {}
    responseData.error = false;
    defautLang = "en";
    await axios.get(process.env.VUE_APP_API_ENDPOINT + "app/connect")
        .then((response) => {
            if (response.data.data) {

                let data = response.data.data;
                // Store slider in Local Storage
                if (data.sliders) {
                    // Convert slider object to array
                    let listOfSliderObjects = Object.keys(data.sliders).map((key) => {
                        return data.sliders[key]
                    })

                    store.commit('setSlider', JSON.stringify(data.sliders))
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
                        defaultLanguageData["selectedId"] = (data.defaultLanguageId) ? data.defaultLanguageId : "";
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
                localStorage.removeItem('slider');
                localStorage.removeItem('listOfLanguage');
                localStorage.removeItem('defaultLanguage');
                localStorage.removeItem('defaultLanguageId');
                localStorage.removeItem('logo');
                let listOfObjects = {};
                store.commit('setLanguageList', JSON.stringify(listOfObjects))
                defaultLanguageData["selectedVal"] = defautLang;
                defaultLanguageData["selectedId"] = "";
                store.commit('setDefaultLanguage', defaultLanguageData)
                var logo = '';
                store.commit('setLogo', logo)
                var sliderData = [];
                store.commit('setSlider', JSON.stringify(sliderData))
            }

            // Set no mission found message
            if (response.data.data.no_mission_custom_text) {
                store.commit('missionNotFound', response.data.data.no_mission_custom_text.translations);
            } else {
                store.commit('missionNotFound', '');
            }

        })
        .catch(error => {})
    return responseData;
}