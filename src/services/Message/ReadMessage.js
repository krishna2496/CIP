import axios from 'axios'
import store from '../../store'

export default async(messageId) => {
    let responseData = {};
    var defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    var url = process.env.VUE_APP_API_ENDPOINT + "app/message/read/" + messageId;

    await axios({
            url: url,
            method: 'POST',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token
            }
        })
        .then((response) => {
            responseData.error = false;
            responseData.message = response.data.message;
        })
        .catch(function(error) {
            if (error.response.data.errors[0].message) {
                responseData.error = true;
                responseData.message = error.response.data.errors[0].message;
            }
        });
    return responseData;
}