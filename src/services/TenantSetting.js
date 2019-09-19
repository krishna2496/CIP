import axios from 'axios'
import store from '../store'

export default async(data) => {
    let responseData;
    var url = process.env.VUE_APP_API_ENDPOINT + "app/tenant-settings";

    await axios({
            url: url,
            method: 'get',
        })
        .then((response) => {
            let settingArray = [];
            if (response.data.data) {
                $.each(response.data.data, function(index, module) {
                    var key = module.key;
                    settingArray[index] = module.key
                });
                responseData = response.data.data;

            } else {
                settingArray = null
            }
            store.commit("setTenantSetting", settingArray);

        })
        .catch(function(error) {});
    return responseData;
}