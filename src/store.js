import Vue from 'vue'
import Vuex from 'vuex'
import axios from "axios";
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
      state.isLoggedIn = false;
      state.token = null;
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
