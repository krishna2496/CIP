import store from '../../store'
import axios from 'axios'

export default async(missionId) => {
    let responseData = {};
    let defaultLanguage = '';

    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    document.body.classList.add("loader-enable");
    let url = process.env.VUE_APP_API_ENDPOINT + "app/mission/" + missionId
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
        } else {
            responseData.data = [];
        }
        document.body.classList.remove("loader-enable");
    })
      .catch(function() {
          responseData.error = true;
          document.body.classList.remove("loader-enable");
      });
    return responseData;
}