import store from '../../store'
import axios from 'axios'

export default async(countryId) => {
    // Store mission rating
    let responseData = {}
    responseData.error = false;
    responseData.data = [];
    let defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "app/city/" + countryId,
            method: 'GET',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        }).then((response) => {
            responseData.error = false;
            responseData.message = response.data.message;
            if (response.data.data) {

                responseData.data = Object.keys(response.data.data).map(function(key) {
                    return [Number(key), response.data.data[key]];
                });
            }
        })
        .catch(function() {
            responseData.error = true;
        });
    return responseData;
}