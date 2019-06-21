import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "missions?page=" + data[0].page,
            method: 'get',
            headers: {
                'X-localization': (store.state.defaultLanguage).toLowerCase(),
                'token': store.state.token,
            }
        })
        .then((response) => {
            responseData = response.data;
        })
        .catch(function(error) {});
    return responseData;
}