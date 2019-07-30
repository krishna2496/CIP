import store from '../../store'
import axios from 'axios'

export default async(missionId) => {
    let responseData = {};
    var defaultLanguage = '';
    
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }

    var url = process.env.VUE_APP_API_ENDPOINT + "app/mission/"+missionId+"/comments"
    await axios({
            url: url,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        }).then((response) => { 
                responseData.error = false;
                if(response.data.data){
                    responseData.data = response.data.data;
                }
            })
        .catch(function(error) {
            responseData.error = true;
        });
    return responseData;
}