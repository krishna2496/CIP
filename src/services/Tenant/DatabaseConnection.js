import store from '../../store'
import axios from 'axios'

export default async(langList, defautLang) => {
    let responseData = {}
    responseData.error = false;
    let defaultLanguageData = []
    let sliderData = [];
    let logo = '';
    defautLang = "en";
    await axios.get(process.env.VUE_APP_API_ENDPOINT + "app/connect")
        .then((response) => {
            if (response.data.data) {

                let data = response.data.data;
                // Store slider in Local Storage
                if (data.sliders) {
                    // Convert slider object to array
                    Object.keys(data.sliders).map((key) => {
                        return data.sliders[key]
                    })

                    store.commit('setSlider', JSON.stringify(data.sliders))
                } else {
                    sliderData = [];
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
                            defaultLanguageData["selectedVal"] = (data.defaultLanguage) ? data.defaultLanguage : listOfObjects[0][1];
                            defaultLanguageData["selectedId"] = (data.defaultLanguageId) ? data.defaultLanguageId : listOfObjects[0][0];
                            store.commit('setDefaultLanguage', defaultLanguageData)
                        }

                    } else {
                        store.commit('setLanguageList', JSON.stringify(langList))
                        defaultLanguageData["selectedVal"] = (data.defaultLanguage) ? data.defaultLanguage : defautLang;
                        defaultLanguageData["selectedId"] = (data.defaultLanguageId) ? data.defaultLanguageId : "";
                        store.commit('setDefaultLanguage', defaultLanguageData)
                    }

                } else {
                    store.commit('setLanguageList', JSON.stringify(langList))
                    defaultLanguageData["selectedVal"] = defautLang;
                    defaultLanguageData["selectedId"] = "";
                    store.commit('setDefaultLanguage', defaultLanguageData)
                }

                //Set logo in local storage

                if (data.custom_logo) {
                    logo = data.custom_logo;
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
                logo = '';
                store.commit('setLogo', logo)
                sliderData = [];
                store.commit('setSlider', JSON.stringify(sliderData))
            }

            // Set no mission found message
            if (response.data.data.no_mission_custom_text) {
                store.commit('missionNotFound', response.data.data.no_mission_custom_text.translations);
            } else {
                store.commit('missionNotFound', '');
            }

            if (response.data.data.news_banner) {
                store.commit('newsBanner', response.data.data.news_banner);
            } else {
                store.commit('newsBanner', '');
            }
            if (response.data.data.news_banner_text && response.data.data.news_banner_text.translations) {
                store.commit('newsBannerText', response.data.data.news_banner_text);
            } else {
                store.commit('newsBannerText', '');
            }

            if (response.data.data.story_banner) {
                store.commit('storyBanner', response.data.data.story_banner);
            } else {
                store.commit('storyBanner', '');
            }
            if (response.data.data.story_banner_text) {
                store.commit('storyBannerText', response.data.data.story_banner_text);
            } else {
                store.commit('storyBannerText', '');
            }
            if (response.data.data.story_dashboard_text) {
                store.commit('storyDashboardText', response.data.data.story_dashboard_text);
            } else {
                store.commit('storyDashboardText', '');
            }
            if (response.data.data.slide_interval) {
                store.commit('slideInterval', response.data.data.slide_interval);
            } else {
                store.commit('slideInterval', '');
            }
            if (response.data.data.slide_effect) {
                store.commit('slideEffect', response.data.data.slide_effect);
            } else {
                store.commit('slideEffect', '');
            }

        })
        .catch(function() {

        });
    return responseData;
}