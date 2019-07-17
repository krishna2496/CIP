import store from '../../store'
import axios from 'axios'

export default async(data) => {
    // Mission add to favorite or remove
    let responseData = {}
    responseData.error = false;
    var defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "app/mission/favourite",
            data,
            method: 'post',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        }).then((response) => {
        })
    return responseData;
}