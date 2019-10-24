import axios from 'axios'
import store from '../store'

export default async() => {
    let responseData;
    let defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    let url = process.env.VUE_APP_API_ENDPOINT + "app/user";

    await axios({
            url: url,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        })
        .then((response) => {
            if (response.data.data) {
                responseData = response.data.data;
            }
        })
        .catch(function() {});
    return responseData;
}