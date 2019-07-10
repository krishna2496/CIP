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
                let filterData = {};
                filterData.search = response.data.data.filters.search;
                filterData.countryId = response.data.data.filters.country_id;
                filterData.cityId = response.data.data.filters.city_id;
                filterData.themeId = response.data.data.filters.theme_id;
                filterData.skillId = response.data.data.filters.skill_id;
                filterData.tags = response.data.data.filters.tags;
                store.commit('userFilter',filterData)
            } else {
                let filterData = {};
                filterData.search = '';
                filterData.countryId = '';
                filterData.cityId = '';
                filterData.themeId = '';
                filterData.skillId = '';
                filterData.tags = '';
                store.commit('userFilter',filterData)
            }
        })
        .catch(function(error) {});
    return responseData;
}