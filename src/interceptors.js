import axios from 'axios';
import router from './router'
import store from './store'

export default function setup() {
    // Add a request interceptor
    axios.interceptors.request.use(function(config) {
        // Do something before request is sent
        document.body.classList.add("loader-enable");
        return config;
    }, function(error) {

        // Do something with request error
        document.body.classList.remove("loader-enable");
        return Promise.reject(error);
    });

    // Add a response interceptor
    axios.interceptors.response.use(function(response) {
        // Do something with response data
        document.body.classList.remove("loader-enable");
        return response;
    }, function(error) {

        if (error.response.status == '403' && error.response.data.errors[0].code == '40008') {}

        //if token expired
        if ((error.response.data.errors[0].status == '401' || error.response.data.errors[0].status == '400') &&
            (error.response.data.errors[0].code == '40016' || error.response.data.errors[0].code == '40014' || error.response.data.errors[0].code == '40012')) {
            store.commit('logoutUser')
            router.push({
                name: 'login'
            })
        }
        // Do something with response error
        document.body.classList.remove("loader-enable");
        return Promise.reject(error);
    });
}