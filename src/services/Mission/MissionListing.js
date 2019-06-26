import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    var defaultLanguage = '';

    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    var url =process.env.VUE_APP_API_ENDPOINT + "app/missions?page=" + data.page

    if(data.search != ''){
        url = url+"&search=" + data.search
    }
    await axios({
            url: url,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
            }
        })
        .then((response) => {
            responseData = response.data;

            if (response.data.meta_data) {
                store.commit('userFilter',response.data.meta_data)
            } else {
                let filterData = {};
                filterData.search = '';
                filterData.country = '';
                filterData.city = '';
                filterData.theme = '';
                filterData.skill = '';
                store.commit('userFilter',filterData)
            }
        })
        .catch(function(error) {});
    return responseData;
}