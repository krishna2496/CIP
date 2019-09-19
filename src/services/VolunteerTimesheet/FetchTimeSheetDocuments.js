import axios from 'axios'
import store from '../../store'
import moment from 'moment';

export default async(timeSheetId) => {
    let responseData = [];
    let timeDataArray = []
    let goalDataArray = []
    var defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    var url = process.env.VUE_APP_API_ENDPOINT + "app/timesheet/" + timeSheetId;

    await axios({
            url: url,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        })
        .then((response) => {
            let settingArray = [];
            if (response.data.data) {
                responseData = response.data.data
            }
        })
        .catch(function(error) {});
    return responseData;
}