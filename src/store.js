import Vue from 'vue'
import Vuex from 'vuex'
import axios from "axios";
import router from './router'
Vue.use(Vuex)

export default new Vuex.Store({
  state: {
   	isLoggedIn: !!localStorage.getItem('token'),
    token: localStorage.getItem('token'),
    listOfLanguage : localStorage.getItem('listOfLanguage'),
    defaultLanguage : localStorage.getItem('defaultLanguage'),
    isloaderSet:true
  },
  mutations: {
  	loginUser(state, token){
      state.isLoggedIn = true;
      state.token = token;
    },
    logoutUser(state){
      localStorage.removeItem('token')
      state.isLoggedIn = false;
      state.token = null;
      router.push({name: 'login'})
    },
    checkState(state){
    	console.log(state)
    }
  },
  getters: {
  	// list: state => state.list
  },
  actions: {

  }
});
