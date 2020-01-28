import axios from 'axios';
import router from './router'
import store from './store'

export default function setup() {
    // Add a request interceptor
    axios.interceptors.request.use(function(config) {
        return config;
    }, function(error) {
        return Promise.reject(error);
    });

    // Add a response interceptor
    axios.interceptors.response.use(function(response) {
        if (response.headers.token) {
          store.commit('setToken', response.headers.token);
        }
        return response;
    }, function(error) {

        //if token expired
        if ((error.response.data.errors[0].status == '401' || error.response.data.errors[0].status == '400') &&
            (error.response.data.errors[0].code == '210009' || error.response.data.errors[0].code == '210012' || error.response.data.errors[0].code == '210010') || error.response.data.errors[0].code == '400043') {
            store.commit('logoutUser')
            router.push({
                name: 'login'
            })
        }
        return Promise.reject(error);
    });
}
