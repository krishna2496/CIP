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

<style lang="scss">
.loader-enable{overflow: hidden;}
.page-loader{display: block; position: fixed; text-align: center; left: 0; right: 0; top: 0; background: rgba(51, 51, 51, 0.35); height: 100vh; z-index: 1031;}
.page-loader img{width:100px;position: absolute; top: 50%; left: 50%;}
.loader-enable .page-loader{display: block;}
</style>

