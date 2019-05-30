<template>
  <div id="app">
   
    <div id="nav">
      <ul class="nav justify-content-center">
        <li class="nav-item">
          <router-link class="nav-link" to="/" v-on:click.native="logout()" replace v-if="this.$store.state.isLoggedIn">Logout</router-link>
        </li>
    </ul>    
    </div>
     <router-view/>
  </div>
</template>


<script>
export default {
  
  data() {
    return {};
  },

  mounted() {
    //ios browser detection
    if (navigator.userAgent.match(/iP(hone|od|ad)/i)) {
      document.querySelector("body").classList.add("browser-ios");
    }
  },

  methods:{
    logout(){
      // console.log(this.$store.);
      // alert( this.$store.state.isLoggedIn );
      // this.$store.state.token = null
      // this.$store.state.isLoggedIn = false
      this.$store.commit('logoutUser');
    }
  }

};
</script>

