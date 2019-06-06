import store from '../../store'

export default async(data, langList, defautLang) => {

    // Store slider in Local Storage
    if (data.slider) {
        // Convert slider object to array
        let listOfSliderObjects = Object.keys(data.slider).map((key) => {
            return data.slider[key]
        })
        
        store.commit('setSlider',JSON.stringify(data.slider))
    }else{
        var sliderData = [];
        store.commit('setSlider',JSON.stringify(sliderData))
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

            store.commit('setLanguageList',JSON.stringify(listOfObjects))

            if (defaultLanguageDataChange == true) {
                var defaultLanguageData = []
                defaultLanguageData["selectedVal"] = listOfObjects[0][1];
                defaultLanguageData["selectedId"] = listOfObjects[0][0];
                store.commit('setDefaultLanguage',defaultLanguageData)
            }

        } else {
            store.commit('setLanguageList',JSON.stringify(langList))
            var defaultLanguageData = []
            defaultLanguageData["selectedVal"] = defautLang;
            defaultLanguageData["selectedId"] = "";
            store.commit('setDefaultLanguage',defaultLanguageData)
        }

    } else {
        store.commit('setLanguageList',JSON.stringify(langList))
        var defaultLanguageData = []
        defaultLanguageData["selectedVal"] = defautLang;
        defaultLanguageData["selectedId"] = "";
        store.commit('setDefaultLanguage',defaultLanguageData)
    }
}

