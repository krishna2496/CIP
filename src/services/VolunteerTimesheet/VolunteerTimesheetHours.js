import axios from 'axios'
import store from '../../store'
import moment from 'moment';
import {datesEqual} from "bootstrap-vue/esm/utils/date";

export default async(data) => {
    let responseData = [];
    let defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    let url = process.env.VUE_APP_API_ENDPOINT + "app/timesheet?page=" + data.page + "&type=" + data.type;

    await axios({
        url: url,
        method: 'get',
        headers: {
            'X-localization': defaultLanguage,
            'token': store.state.token,
        }
    })
        .then((response) => {
            if (response.data.data) {

                if (response.data.data) {
                    let timeData = response.data.data
                    timeData.filter((toItem, toIndex) => {
                        let timeSheet = timeData[toIndex].timesheet;
                        timeSheet.filter((timeSheetItem, timeSheetIndex) => {

                            let dateVolunteered = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered, 'YYYY-MM-DD');
                            response.data.data[toIndex].timesheet[timeSheetIndex]['date'] = dateVolunteered.format('D');
                            response.data.data[toIndex].timesheet[timeSheetIndex]['year'] = dateVolunteered.format('YYYY');
                            response.data.data[toIndex].timesheet[timeSheetIndex]['month'] = dateVolunteered.format('M');
                        });


                    });
                }
            }

            responseData = response.data
        })
        .catch(function() {});
    return responseData;
}