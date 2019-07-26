import axios from 'axios';
import router from './router'
import store from './store'

export default function setup() {
    // Add a request interceptor
    axios.interceptors.request.use(function(config) {
        var getRequestEndPoint = ''
        var addLoader = "true";
        var url = config.url;
        var domain =url.split('/');
        var lastPosition =  domain.length -1;
        var getRequest = domain[lastPosition].split('?');
        var getRequestEndPoint = getRequest[0];
        // console.log(domain[lastPosition]);
        // Do something before request is sent
        if(domain[lastPosition] == "favourite" || domain[lastPosition] == "rating"){
            addLoader = "false";
        }

        if(getRequestEndPoint == "missions" || getRequestEndPoint == "volunteers") {
            if(config.headers.addLoader == "removeLoader"){
                addLoader = "false";
            }
        }

        if(getRequestEndPoint == "volunteers") {
                addLoader = "false";
        }

        if(addLoader == "true") {
            document.body.classList.add("loader-enable");
        }
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
            (error.response.data.errors[0].code == '210009' || error.response.data.errors[0].code == '210012' || error.response.data.errors[0].code == '210010')) {
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