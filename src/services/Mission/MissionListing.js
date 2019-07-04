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

    if(data.search != ''){
        url = url+"&search=" + data.search
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
                filterData.search = data.meta_data.search;
                filterData.country = data.meta_data.country;
                filterData.city = data.meta_data.city;
                filterData.theme = data.meta_data.theme;
                filterData.skill = data.meta_data.skill;
                store.commit('userFilter',filterData)
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