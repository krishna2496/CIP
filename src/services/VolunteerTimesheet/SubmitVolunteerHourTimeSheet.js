import axios from 'axios'
import store from '../../store'
import moment from 'moment';

export default async(data) => {
    let responseData = {
        'error' : true
    };

    var defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    var url =process.env.VUE_APP_API_ENDPOINT + "app/timesheet/submit";
   
    await axios({
            url: url,
            method: 'PATCH',
            data,
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        })
        .then((response) => {          
            if(response.data.message) { 
                response.error = false
                responseData.message =  response.data.message
            } 
        })
        .catch(function(error) {
            if (error.response.data.errors[0].message) {
                responseData.error = true;
                responseData.message = error.response.data.errors[0].message;
            }  
        });
    return responseData;
}