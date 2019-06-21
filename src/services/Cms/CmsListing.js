import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "cms/listing",
            method: 'get',
            headers: {
                'X-localization': (store.state.defaultLanguage).toLowerCase()
            }
        })
        .then((response) => {
            responseData = response.data.data;
        })
        .catch(function(error) {});
    return responseData;
}
