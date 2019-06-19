import store from '../../store'
import axios from 'axios'

export default async(data) => {
    // login api call with params email address and password
    let responseData = {}
    responseData.error = false;
    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "login",
            data,
            method: 'post',
            headers: {
                'X-localization': (store.state.defaultLanguage).toLowerCase()
            }
        }).then((response) => {
            //Store login data in local storage
            store.commit('loginUser', response.data.data)  
        })
        .catch(error => {
            if (error.response.data.errors[0].message) {
                responseData.error = true;
                responseData.message = error.response.data.errors[0].message;
            }
        })
    return responseData;
}
