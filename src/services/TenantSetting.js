import axios from 'axios'
import store from '../store'

export default async(data) => {
    let responseData;
    var url =process.env.VUE_APP_API_ENDPOINT + "app/tenant-settings";

    await axios({
            url: url,
            method: 'get',
        })
        .then((response) => {
            if(response.data.data) { 
                let settingArray = {};
                $.each(response.data.data, function(index,module){
                    var key = module.key;
                    settingArray[key] = module.value
                });
                console.log(settingArray);
                store.commit("setTenantSetting",settingArray);
                responseData = response.data.data;
            }
        })
        .catch(function(error) {});
    return responseData;
}