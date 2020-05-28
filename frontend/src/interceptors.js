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
        // if token/account has expired
        if (router.app._route.path !== '/'
            && [401, 400, 403].includes(error.response.data.errors[0].status)
            && [210009, 210010, 210012, 400043, 210014, 210015].includes(error.response.data.errors[0].code)) {
            store.commit('logoutUser')
            router.push({name: 'login'}, () => {})
        }
        return Promise.reject(error);
    });
}
