import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    var defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "app/missions?page=" + data[0].page,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        })
        .then((response) => {
            responseData = response.data;
        })
        .catch(function(error) {});
    return responseData;
}