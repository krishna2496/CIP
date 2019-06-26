import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    var defaultLanguage = '';

    await axios({
            url: process.env.VUE_APP_API_ENDPOINT + "user_filter",
            method: 'get',
            headers: {
               'token': store.state.token,
            }
        })
        .then((response) => {
            if (response.data && response.data.data.filters) {
                store.commit('userFilter',response.data.data.filters)
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