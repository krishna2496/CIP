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
    if (screen.width < 1025) {
      document.addEventListener("touchend", this.onClick);
    } else {
      document.addEventListener("click", this.onClick);
    }
  },
  methods: {
    onClick() {
      var dropdownList = document.querySelectorAll(".dropdown-open");
      if (dropdownList.length > 0) {
        for (var i = 0; i < dropdownList.length; ++i) {
          dropdownList[i].classList.remove("dropdown-open");
        }
      } else {
        if (screen.width < 992) {
          var body = document.querySelectorAll("body, html");
          body.forEach(function(e) {
            e.classList.remove("open-nav");
            e.classList.remove("open-filter");
          });
        }
      }
    }
  }
};
</script>



