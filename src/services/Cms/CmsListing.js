import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "cms/listing",
            method: 'get',
            headers: {
                'X-localization': defaultLanguage
            }
        })
        .then((response) => {
            responseData = response.data.data;
        })
        .catch(function(error) {});
    return responseData;
}
