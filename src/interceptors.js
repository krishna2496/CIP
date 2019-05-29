import axios from 'axios';
import router from './router'

export default function setup() {
  // Add a request interceptor
axios.interceptors.request.use(function (config) {
    // Do something before request is sent
    document.body.classList.add("loader-enable");
    return config;
  }, function (error) {
    // Do something with request error
    document.body.classList.remove("loader-enable");
    return Promise.reject(error);
  });

// Add a response interceptor
axios.interceptors.response.use(function (response) {
    // Do something with response data
    document.body.classList.remove("loader-enable");

    return response;
  }, function (error) {

   if(error.response.status == '401' && error.response.data.code == '120'){
     router.push({name: 'login'})
   }

    // Do something with response error
    document.body.classList.remove("loader-enable");
    return Promise.reject(error);
  });
}