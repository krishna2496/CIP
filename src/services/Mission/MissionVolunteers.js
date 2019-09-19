import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData = {};
    var defaultLanguage = '';
    var missionId = data.mission_id;

    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }

    var url = process.env.VUE_APP_API_ENDPOINT + "app/mission/" + missionId + "/volunteers?page=" + data.page + "&perPage=" + 12
    await axios({
            url: url,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        }).then((response) => {
            responseData.error = false;
            if (response.data.data) {
                responseData.data = response.data.data;
                responseData.pagination = response.data.pagination;
            } else {
                responseData.data = [];
                responseData.pagination = [];
            }

        })
        .catch(function(error) {
            responseData.error = true;
        });
    return responseData;
}