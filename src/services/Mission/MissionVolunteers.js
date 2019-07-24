import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData = {};
    var defaultLanguage = '';
    var missionId = data.mission_id;
    let headerMenuData = {}
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }

    var url =process.env.VUE_APP_API_ENDPOINT + "app/mission/"+missionId+"/volunteers?page=" + data.page
    await axios({
            url: url,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        }).then((response) => { 
                responseData.error = false;
                responseData.data = response.data.data;
                responseData.pagination = response.data.pagination;
            })
        .catch(function(error) {
            responseData.error = true;
        });
    return responseData;
}