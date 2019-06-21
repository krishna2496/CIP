import axios from 'axios'
import store from '../store'

export default async() => {
    let responseData;
    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "country",
            method: 'get',
            headers: {
                'X-localization': (store.state.defaultLanguage).toLowerCase(),
                'token': store.state.token,
            }
        })
        .then((response) => {
            if(response.data.data) { 
                responseData = Object.entries(response.data.data);
            }
        })
        .catch(function(error) {});
    return responseData;
}