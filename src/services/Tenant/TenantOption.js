import store from '../../store'

export default (data,langList,defautLang) =>{

    //Store slider in Local Storage
    if (data.slider) {
        //Convert slider object to array
        let listOfSliderObjects = Object.keys(data.slider).map((key) => {
        return data.slider[key]
        })
        
        store.commit('setSlider',JSON.stringify(data.slider))
    }

    //Store language in Local Storage
    if (data.language) {

        let defaultLanguageDataChange = true

        let listOfObjects = Object.entries(data.language);

        listOfObjects.forEach(function(listOfLangauge) {
            if (listOfLangauge[0] == localStorage.getItem('defaultLanguageId') && listOfLangauge[1] == localStorage.getItem('defaultLanguage')){
                defaultLanguageDataChange = false;
            }
            
        });

        //If options exist
        if (listOfObjects) {
                localStorage.setItem('listOfLanguage',JSON.stringify(listOfObjects))
            
            if (defaultLanguageDataChange === true) {
                localStorage.removeItem('defaultLanguage');
                localStorage.removeItem('defaultLanguageId');
                localStorage.setItem('defaultLanguage',listOfObjects[0][1])
                localStorage.setItem('defaultLanguageId',listOfObjects[0][0])
            }

        } else {
            localStorage.setItem('listOfLanguage',langList)
            localStorage.setItem('defaultLanguage',defautLang)
        }

    } else {
        localStorage.setItem('listOfLanguage',JSON.stringify(langList))
        localStorage.setItem('defaultLanguage',defautLang)
    }
}

