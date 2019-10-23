<template>
    <div id="app">
        <router-view />
    </div>
</template>


<script>
    import {
        setTimeout
    } from "timers";
    export default {
        data() {
            return {};
        },
        mounted() {
            document.addEventListener("click", this.onClick);
        },
        methods: {
            onClick() {
                let dropdownList = document.querySelectorAll(".dropdown-open");
                let body = document.querySelectorAll("body, html");
                if (dropdownList.length > 0) {
                    for (let i = 0; i < dropdownList.length; ++i) {
                        dropdownList[i].classList.remove("dropdown-open");
                    }
                }
                if (screen.width < 992) {
                    body.forEach(function (e) {
                        e.classList.remove("open-nav");
                        e.classList.remove("open-filter");
                    });
                }
                if (screen.width < 992) {
                    body.forEach(function () {
                        let breadcrumbDropdown = document.querySelector(
                            ".breadcrumb-dropdown-wrap"
                        );
                        if (document.querySelector(".breadcrumb") != null) {
                            breadcrumbDropdown.classList.remove("open");
                        }
                    });
                }
            },
            signinAdj() {
                setTimeout(function () {
                    if (document.querySelector(".signin-form-wrapper") != null) {
                        let contentH = document.body.clientHeight;
                        document.querySelector(".signin-form-wrapper").style.minHeight = contentH + "px";
                    }
                }, 1000);
            },
            handleScroll() {
                if (document.querySelector(".inner-pages > header") != null) {
                    let body = document.querySelector("body");
                    let bheader = document.querySelector("header");
                    let bheaderTop = bheader.offsetHeight;
                    if (window.scrollY > bheaderTop) {
                        body.classList.add("small-header");
                    } else {
                        body.classList.remove("small-header");
                    }
                }
            },
        },
        beforeMount() {
            this.signinAdj();
        },
        created() {
            let ua = navigator.userAgent.toLowerCase();
            if (ua.indexOf("safari") != -1) {
                if (ua.indexOf("chrome") > -1) {
                    document.querySelector("body , html").classList.add("browser-chrome"); // Chrome
                } else {
                    document.querySelector("body , html").classList.add("browser-safari"); // Safari
                }
            }
            //ios browser detection

            let isIOS =
                /iPad|iPhone|iPod/.test(navigator.platform) ||
                (navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1);
            if (isIOS) {
                document.querySelector("body").classList.add("browser-ios");
            }
            window.addEventListener("resize", this.signinAdj);
            window.addEventListener("scroll", this.handleScroll);
            window.scrollTo(0, 0);
        },
        updated() {
            window.scrollTo(0, 0);
            this.signinAdj();
            setTimeout(function () {
                let selectorList = document.querySelectorAll(".nav-link");
                let menuLinkList = document.querySelectorAll(".menu-wrap a");
                let dropdownList = document.querySelectorAll(".custom-dropdown, .checkbox-select");
                let notificationButton = document.querySelector(
                    ".notification-menu .nav-link .btn-notification");
                let notificationMenu = document.querySelector(".notification-menu .nav-link");
                for (let i = 0; i < selectorList.length; i++) {
                    if (notificationMenu != selectorList[i]) {
                        let selectorClick = selectorList[i];
                        selectorClick.addEventListener("click", function () {
                            let notificationPopover = document.querySelector(".notification-popover");
                            if (notificationPopover != null) {
                                notificationButton.click();
                            }
                        });
                    }
                }
                selectorList.forEach(function (event) {
                    event.addEventListener("mouseover", function () {
                        event.removeAttribute("href");
                    });
                    event.addEventListener("click", function () {
                        dropdownList.forEach(function (removeDropdown) {
                            removeDropdown.classList.remove("dropdown-open");
                        });
                    });
                });
                menuLinkList.forEach(function (linkEvent) {
                    linkEvent.addEventListener("click", function () {
                        dropdownList.forEach(function (removeDropdown) {
                            removeDropdown.classList.remove("dropdown-open");
                        });
                    });
                });
                if (notificationButton != null) {
                    notificationButton.addEventListener("click", function () {
                        dropdownList.forEach(function (removeDropdown) {
                            removeDropdown.classList.remove("dropdown-open");
                        });
                    });
                }

                let paginationItem = document.querySelectorAll(".pagination-block .page-item .page-link");
                paginationItem.forEach(function (pageLink) {
                    pageLink.addEventListener("mouseover", function () {
                        pageLink.removeAttribute("href");
                    });
                });

                // favourite-icon clickable
                let buttonActive = document.querySelectorAll(".favourite-icon");
                buttonActive.forEach(function (event) {
                    event.addEventListener("click", function () {
                        event.classList.toggle("active");
                    });
                });
                let dataInput = document.querySelectorAll(".mx-input");
                dataInput.forEach(function (inputEvent) {
                    inputEvent.addEventListener("click", function () {
                        dropdownList.forEach(function (removeDropdown) {
                            removeDropdown.classList.remove("dropdown-open");
                        });
                    });
                });
            }, 1000);
            
        },
        destroyed() {
            window.removeEventListener("scroll", this.handleScroll);
        }
    };

</script>
