<template>
  <div id="app">
    <router-view/>
  </div>
</template>


<script>
import { setTimeout } from "timers";
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
      var body = document.querySelectorAll("body, html");
      if (dropdownList.length > 0) {
        for (var i = 0; i < dropdownList.length; ++i) {
          dropdownList[i].classList.remove("dropdown-open");
        }
      }
      if (screen.width < 992) {
        body.forEach(function(e) {
          e.classList.remove("open-nav");
          e.classList.remove("open-filter");
        });
      }
    },
    footerAdj() {
      setTimeout(function() {
        if (document.querySelector("footer") != null) {
          var footerH = document.querySelector("footer").offsetHeight;
          document.querySelector("footer").style.marginTop = -footerH + "px";
          document.querySelector(".inner-pages").style.paddingBottom =
            footerH + "px";
        }
      }, 600);
    },
    handleScroll() {
      var body = document.querySelector("body");
      var bheader = document.querySelector("header");
      var bheader_top = bheader.offsetHeight;
      if (window.scrollY > bheader_top) {
        body.classList.add("small-header");
      } else {
        body.classList.remove("small-header");
      }
    },
     handleClick(e) {
        e.stopPropagation();
        var profile_toggle = document.querySelector(
          ".profile-menu .dropdown-toggle"
        );
        var profile_menu = document.querySelector(".profile-menu");
        if (profile_menu != null) {
          if (profile_menu.classList.contains("show")) {
            profile_toggle.click();
          }
        }
        var notification_btn = document.querySelector(
          ".notification-menu .nav-link .btn-notification"
        );
        var notification_popover = document.querySelector(
          ".notification-popover"
        );
        if (notification_popover != null) {
          notification_btn.click();
        }

        e.target.parentNode.classList.toggle("dropdown-open");
        var dropdownList = document.querySelectorAll(".dropdown-open");
        for (var i = 0; i < dropdownList.length; ++i) {
          if (dropdownList[i] != e.target.parentNode) {
            dropdownList[i].classList.remove("dropdown-open");
          }
        }
        var dropdown_list = document.querySelectorAll(".select-dropdown");
        dropdown_list.forEach(function(e) {
          var dropdown_list_width = parseInt(
            window.getComputedStyle(e).getPropertyValue("width")
          );
          var optionlist_wrap = e.querySelector(".dropdown-option-wrap");
          var optionlist_wrap_height = parseInt(
            window
              .getComputedStyle(optionlist_wrap)
              .getPropertyValue("max-height")
          );
          var optionlist = optionlist_wrap.querySelector(
            ".dropdown-option-list"
          );
          var optionlist_height = optionlist.offsetHeight;
          var optionlist_width = parseInt(
            window.getComputedStyle(optionlist).getPropertyValue("width")
          );
          if (optionlist_wrap_height < optionlist_height) {
            var minwidth_style = e.querySelector(".simplebar-offset");
            minwidth_style.setAttribute("style", "left: 0 !important");
            if (
              optionlist_wrap_height < optionlist_height &&
              dropdown_list_width < optionlist_width
            ) {
              minwidth_style.setAttribute("style", "left: auto !important");
            }
          }
        });
      }

  },
  beforeMount() {
    this.footerAdj();
  },
  created() {
    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf("safari") != -1) {
      if (ua.indexOf("chrome") > -1) {
        document.querySelector("body").classList.add("browser-chrome"); // Chrome
      } else {
        document.querySelector("body").classList.add("browser-safari"); // Safari
      }
    }
    window.addEventListener("resize", this.footerAdj);
    window.addEventListener("scroll", this.handleScroll);

     
  },
  updated() {
    this.footerAdj();
    setTimeout(() =>  {
      var selectorList = document.querySelectorAll(".nav-link");
      var dropdownList = document.querySelectorAll(
        ".custom-dropdown, .checkbox-select"
      );
      
      var notification_btn = document.querySelector(
        ".notification-menu .nav-link .btn-notification"
      );
      var notification_menu = document.querySelector(
        ".notification-menu .nav-link"
      );
      for (var i = 0; i < selectorList.length; i++) {
        if (notification_menu != selectorList[i]) {
          var selector_click = selectorList[i];
          selector_click.addEventListener("click", function() {
            var notification_popover = document.querySelector(
              ".notification-popover"
            );
            if (notification_popover != null) {
              notification_btn.click();
            }
          });
        }
      }
      selectorList.forEach(function(event) {
        event.addEventListener("click", function() {
          dropdownList.forEach(function(removeDropdown) {
            removeDropdown.classList.remove("dropdown-open");
          });
        });
      });

      // favourite-icon clickable
      // var btn_active = document.querySelectorAll(".favourite-icon");
      // btn_active.forEach(function(event) {
      //   event.addEventListener("click", function() {
      //     event.classList.toggle("active");
      //   });
      // });
    },500);

    var _this = this;
    setTimeout(function(){

     var dropdwon_toggle = document.querySelectorAll(".select-text");
             for (var i = 0; i < dropdwon_toggle.length; ++i) {
            if (screen.width < 1025) {
              dropdwon_toggle[i].addEventListener("touchend", _this.handleClick);
            } else {
              dropdwon_toggle[i].addEventListener("click", _this.handleClick);          
            }
          }
    },1000);

           
        var btn_active = document.querySelectorAll(".favourite-icon");
        btn_active.forEach(function(event){
            event.addEventListener("click", function(){
                event.classList.toggle("active");
            })
        });
        


  },
  destroyed() {
    window.removeEventListener("scroll", this.handleScroll);
  }
};
</script>



