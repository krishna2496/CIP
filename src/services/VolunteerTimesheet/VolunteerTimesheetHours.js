import axios from 'axios'
import store from '../../store'
import moment from 'moment';

export default async() => {
    let responseData = [];

    let defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    let url = process.env.VUE_APP_API_ENDPOINT + "app/timesheet";

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
                if (response.data.data['TIME']) {
                    let timeData = response.data.data['TIME']
                    timeData.filter((toItem, toIndex) => {
                        let timeSheet = timeData[toIndex].timesheet;

                        timeSheet.filter((timeSheetItem, timeSheetIndex) => {
                            let momentObj = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered, 'MM-DD-YYYY');
                            let dateVolunteered = momentObj.format('YYYY-MM-DD');
                            response.data.data['TIME'][toIndex].timesheet[timeSheetIndex]['date'] = moment(dateVolunteered).format('D')
                            response.data.data['TIME'][toIndex].timesheet[timeSheetIndex]['year'] = moment(dateVolunteered).format('YYYY')
                            response.data.data['TIME'][toIndex].timesheet[timeSheetIndex]['month'] = moment(dateVolunteered).format('M')
                        });


                    });
                }
                if (response.data.data['GOAL']) {
                    let timeData = response.data.data['GOAL']
                    timeData.filter((toItem, toIndex) => {
                        let goalSheet = timeData[toIndex].timesheet;

                        goalSheet.filter((timeSheetItem, timeSheetIndex) => {
                            let momentObj = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered, 'MM-DD-YYYY');
                            let dateVolunteered = momentObj.format('YYYY-MM-DD');
                            response.data.data['GOAL'][toIndex].timesheet[timeSheetIndex]['date'] = moment(dateVolunteered).format('D')
                            response.data.data['GOAL'][toIndex].timesheet[timeSheetIndex]['year'] = moment(dateVolunteered).format('YYYY')
                            response.data.data['GOAL'][toIndex].timesheet[timeSheetIndex]['month'] = moment(dateVolunteered).format('M')
                        });


                    });
                }
            }

            responseData = response.data.data
        })
        .catch(function() {});
    return responseData;
}