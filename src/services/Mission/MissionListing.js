import store from '../../store'
import axios from 'axios'

export default async(data) => {
    let responseData;
    var defaultLanguage = '';
    let headerMenuData = {}
    
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }
    var url =process.env.VUE_APP_API_ENDPOINT + "app/missions?page=" + data.page

    if(data.search != '' && data.search != null){
        url = url+"&search=" + data.search
    }

    if(data.countryId != '' && data.countryId != null){
        url = url+"&country_id=" + data.countryId
    }
    if(data.cityId != '' && data.cityId != null){
        url = url+"&city_id=" + data.cityId
    }
    if(data.themeId != '' && data.themeId != null){
        url = url+"&theme_id=" + data.themeId
    }
    if(data.skillId != '' && data.skillId != null){
        url = url+"&skill_id=" + data.skillId
    }

    if(data.exploreMissionType != ''){
        url = url+"&explore_mission_type=" + data.exploreMissionType
    }

    if(data.exploreMissionParams != ''){
        url = url+"&explore_mission_params=" + data.exploreMissionParams
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
            // Set filter data
            if (response.data.meta_data.filters) {
                let filterData = {};
                filterData.search = response.data.meta_data.filters.search;
                filterData.countryId = response.data.meta_data.filters.country_id;
                filterData.cityId = response.data.meta_data.filters.city_id;
                filterData.themeId = response.data.meta_data.filters.theme_id;
                filterData.skillId = response.data.meta_data.filters.skill_id;
                filterData.tags = response.data.meta_data.filters.tags;
                filterData.sortBy = response.data.meta_data.filters.sort_by;
                store.commit('userFilter',filterData)
            } else {
                let filterData = {};
                filterData.search = '';
                filterData.countryId = '';
                filterData.cityId = '';
                filterData.themeId = '';
                filterData.skillId = '';
                filterData.tags = '';
                filterData.sortBy = '';
                store.commit('userFilter',filterData)
            }         

        })
        .catch(function(error) {});
    return responseData;
}