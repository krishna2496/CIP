import axios from 'axios'
import store from '../../store'
import moment from 'moment';

export default async() => {
    let responseData = [];
    let timeDataArray = []
    let goalDataArray = []
    var defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    var url = process.env.VUE_APP_API_ENDPOINT + "app/timesheet";

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
                    timeData.filter(function(toItem, toIndex) {
                        let timeSheet = timeData[toIndex].timesheet;
                        timeDataArray = timeData[toIndex];

                        timeSheet.filter(function(timeSheetItem, timeSheetIndex) {
                            response.data.data['TIME'][toIndex].timesheet[timeSheetIndex]['date'] = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered).format('D')
                            response.data.data['TIME'][toIndex].timesheet[timeSheetIndex]['year'] = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered).format('YYYY')
                            response.data.data['TIME'][toIndex].timesheet[timeSheetIndex]['month'] = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered).format('M')
                        });


                    });
                }
                if (response.data.data['GOAL']) {
                    let timeData = response.data.data['GOAL']
                    timeData.filter(function(toItem, toIndex) {
                        let goalSheet = timeData[toIndex].timesheet;
                        goalDataArray = timeData[toIndex];

                        goalSheet.filter(function(timeSheetItem, timeSheetIndex) {
                            response.data.data['GOAL'][toIndex].timesheet[timeSheetIndex]['date'] = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered).format('D')
                            response.data.data['GOAL'][toIndex].timesheet[timeSheetIndex]['year'] = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered).format('YYYY')
                            response.data.data['GOAL'][toIndex].timesheet[timeSheetIndex]['month'] = moment(timeData[toIndex].timesheet[timeSheetIndex].date_volunteered).format('M')
                        });


                    });
                }
            }
            responseData = response.data.data
        })
        .catch(function() {});
    return responseData;
}