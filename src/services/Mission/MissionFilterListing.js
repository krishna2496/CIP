import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    var defaultLanguage = '';

    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "mission_filters,
            method: 'get',
            headers: {
               'token': store.state.token,
            }
        })
        .then((response) => {
            responseData = response.data;
        })
        .catch(function(error) {});
    return responseData;
}