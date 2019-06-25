import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    var defaultLanguage = '';

    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }

    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "app/missions?page=" + data.page+"&search=" + data.search,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        })
        .then((response) => {
            responseData = response.data;
            store.commit('userFilter', JSON.stringify(sliderData))
        })
        .catch(function(error) {});
    return responseData;
}