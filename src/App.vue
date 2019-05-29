<template>
  <div id="app">
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
  }
};
</script>

