import store from '../../store'
import axios from 'axios'

export default async(data) => {
    // Forgot Password API call with params email address
    let responseData = {}
    responseData.error = false;

    await axios({
            url: process.env.VUE_APP_API_ENDPOINT+"password_reset",
            data,   
            method: 'put',
            headers: {
                'X-localization': (store.state.defaultLanguage).toLowerCase()
            }
        }).then(response => {
            responseData.message = response.data.message;
        })
        .catch(error => {
            if (error.response.data.errors[0].message) {
                responseData.error = true;
                responseData.message = error.response.data.errors[0].message;
            }
        })
    return responseData;
}