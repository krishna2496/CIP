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
        signinAdj() {
            setTimeout(function() {
            if (document.querySelector(".signin-form-wrapper") != null) {
                var contentH = document.body.clientHeight;
                document.querySelector(".signin-form-wrapper").style.minHeight =
                contentH + "px";
            }
            }, 1000);
        },
        handleScroll() {
            if (document.querySelector(".inner-pages > header") != null) {
                var body = document.querySelector("body");
                var bheader = document.querySelector("header");
                var bheader_top = bheader.offsetHeight;
                if (window.scrollY > bheader_top) {
                    body.classList.add("small-header");
                } else {
                    body.classList.remove("small-header");
                }
            }
        }
    },
    beforeMount() {
        this.signinAdj();
    },
    handleScroll() {
		if (document.querySelector(".inner-pages > header") != null) {
		  var body = document.querySelector("body");
		  var bheader = document.querySelector("header");
		  var bheader_top = bheader.offsetHeight;
		  if (window.scrollY > bheader_top) {
			body.classList.add("small-header");
		  } else {
			body.classList.remove("small-header");
		  }
		}
	  },
  beforeMount() {
    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf("safari") != -1) {
    if (ua.indexOf("chrome") > -1) {
        document.querySelector("body").classList.add("browser-chrome"); // Chrome
    } else {
        document.querySelector("body").classList.add("browser-safari"); // Safari
    }
    }
    window.addEventListener("resize", this.signinAdj);
    window.addEventListener("scroll", this.handleScroll);
    window.scrollTo(0, 0);
    },
    updated() {
        window.scrollTo(0, 0);
        this.signinAdj();
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
            // notification_btn.addEventListener("click", function() {
            //   dropdownList.forEach(function(removeDropdown) {
            //     removeDropdown.classList.remove("dropdown-open");
            //   });
            // });
        },500);

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



