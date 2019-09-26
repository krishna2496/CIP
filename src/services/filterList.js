import axios from 'axios'
import store from '../store'

export default async(data) => {

    let responseData;
    var defaultLanguage = '';
    if (store.state.defaultLanguage !== null) {
        defaultLanguage = (store.state.defaultLanguage).toLowerCase();
    }

    var url = process.env.VUE_APP_API_ENDPOINT + "app/filter-data";

    if (data.countryId != '') {
        url = url + "?country_id=" + data.countryId
    }

    if (data.cityId != '') {
        if (data.countryId != '') {
            url = url + "&city_id=" + data.cityId
        } else {
            url = url + "?city_id=" + data.cityId
        }
    }

    if (data.themeId != '') {
        if (data.countryId != '' || data.cityId != '') {
            url = url + "&theme_id=" + data.themeId
        } else {
            url = url + "?theme_id=" + data.themeId
        }
    }

    if (data.search != '') {
        if (data.countryId != '' || data.cityId != '' || data.themeId != '') {
            url = url + "&search=" + data.search
        } else {
            url = url + "?search=" + data.search
        }
    }
    // document.body.classList.add("loader-enable");
    // console.log("in");
    await axios({
            url: url,
            method: 'get',
            headers: {
                'X-localization': defaultLanguage,
                'token': store.state.token,
                'loader': 'true'
            },

        })
        .then((response) => {
            if (response.data.data) {
                responseData = response.data.data;
            } else {
                responseData = ''
            }
            // document.body.classList.remove("loader-enable");
        })
        .catch(function(error) {
            // document.body.classList.remove("loader-enable");
        });
    return responseData;
}