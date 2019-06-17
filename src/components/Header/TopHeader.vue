<template>
  <div class="top-header">
    <b-navbar toggleable="lg">
      <b-container>
        <div class="navbar-toggler" @touchend.stop>
          <a href="#" title="Menu" @click="openMenu" class="toggler-icon">
            <img src="../../assets/images/header/menu-ic.svg" alt>
          </a>
        </div>
        <b-navbar-brand href="/" :style="{backgroundImage: 'url('+this.$store.state.logo+')'}"></b-navbar-brand>
        <div class="menu-wrap" @touchend.stop>
          <b-button class="btn-cross" @click="closeMenu">
            <img src="../../assets/images/cross-ic.svg" alt>
          </b-button>
       <!--    <ul>
            <li class="has-menu">
              <a href="#" title="Explore">Explore</a>
              <ul class="dropdown-menu sub-dropdown">
                <li class="has-submenu">
                  <a href="#">Top Themes</a>
                  <ul class="subdropdown-menu">
                    <li>
                      <a href="#">Education</a>
                    </li>
                    <li>
                      <a href="#">Children</a>
                    </li>
                    <li>
                      <a href="#">Health</a>
                    </li>
                    <li>
                      <a href="#">Animals</a>
                    </li>
                    <li>
                      <a href="#">Nutritions</a>
                    </li>
                  </ul>
                </li>
                <li class="has-submenu">
                  <a href="#">Top Countries</a>
                  <ul class="subdropdown-menu">
                    <li>
                      <a href="#">Education</a>
                    </li>
                    <li>
                      <a href="#">Children</a>
                    </li>
                    <li>
                      <a href="#">Health</a>
                    </li>
                    <li>
                      <a href="#">Animals</a>
                    </li>
                    <li>
                      <a href="#">Nutritions</a>
                    </li>
                  </ul>
                </li>
                <li class="no-dropdown">
                  <a href="#">Top Organisation</a>
                </li>
                <li class="no-dropdown">
                  <a href="#">Most Ranked</a>
                </li>
                <li class="no-dropdown">
                  <a href="#">Top Favourite</a>
                </li>
                <li class="no-dropdown">
                  <a href="#">Recommended</a>
                </li>
                <li class="no-dropdown">
                  <a href="#">Random</a>
                </li>
              </ul>
            </li>
            <li class="has-menu no-dropdown">
              <a href="#" title="Stories">Stories</a>
            </li>
            <li class="has-menu no-dropdown">
              <a href="#" title="News">News</a>
            </li>
            <li class="has-menu">
              <a href="#" title="Policy">Policy</a>
              <ul class="dropdown-menu">
                <li>
                  <a href="#">Volunteering</a>
                </li>
                <li>
                  <a href="#">Sponsored</a>
                </li>
              </ul>
            </li> -->
          <!-- </ul> -->
        </div>
        <b-nav class="ml-auto">
          <b-nav-item right class="search-menu" @click="searchMenu">
            <i>
              <img src="../../assets/images/search-ic.svg" alt>
            </i>
          </b-nav-item>
          <!-- <b-nav-item right class="notification-menu" id="notifyPopoverWrap">
            <button id="notificationPopover" class="btn-notification">
              <i>
                <img src="../../assets/images/bell-ic.svg" alt="Notification Icon">
              </i>
              <b-badge>2</b-badge>
            </button>
          </b-nav-item> -->
          <b-nav-item-dropdown right class="profile-menu" v-if="this.$store.state.isLoggedIn">
            <template slot="button-content">
              <i :style="{backgroundImage: 'url('+this.$store.state.avatar+')'}"></i>
              <em>{{this.$store.state.firstName+' '+this.$store.state.lastName}}</em>
            </template>
            <b-dropdown-item 
             v-on:click.native="logout()" 
             replace 
             v-if="this.$store.state.isLoggedIn"
             >{{ $t("label.logout")}}
            </b-dropdown-item>
          </b-nav-item-dropdown>
        </b-nav>
        <b-popover
          target="notificationPopover"
          placement="topleft"
          container="notifyPopoverWrap"
          @show="onPopoverShow"
          ref="notficationPopover"
          triggers="click blur"
          :show="popoverShow"
        >
          <template slot="title">
            <b-button class="btn-setting" title="Setting" @click="showsetting">
              <img src="../../assets/images/settings-ic.svg" alt="Setting icon">
            </b-button>
            <span class="title">Notification</span>
            <b-button class="btn-clear" title="Clear All" @click="showclearitem">Clear All</b-button>
          </template>
          <div class="notification-details" data-simplebar>
            <b-list-group>
              <b-list-group-item href="#" class="unread-item">
                <i>
                  <img src="../../assets/images/notification/user.png" alt>
                </i>
                <p>
                  John Doe: Recommend this mission -
                  <b>Grow Trees</b>
                </p>
                <span class="status"></span>
              </b-list-group-item>
              <b-list-group-item href="#" class="read-item">
                <i>
                  <img src="../../assets/images/notification/circle-plus.png" alt>
                </i>
                <p>
                  John Doe: Recommend this mission -
                  <b>Save the Children</b>
                </p>
                <span class="status"></span>
              </b-list-group-item>
              <b-list-group-item href="#" class="read-item">
                <i>
                  <img src="../../assets/images/notification/circle-plus.png" alt>
                </i>
                <p>
                  New Mission -
                  <b>Save the world</b>
                </p>
                <span class="status"></span>
              </b-list-group-item>
              <b-list-group-item href="#" class="unread-item">
                <i>
                  <img src="../../assets/images/notification/warning.png" alt>
                </i>
                <p>
                  New Message -
                  <b>Message title goes here</b>
                </p>
                <span class="status"></span>
              </b-list-group-item>
            </b-list-group>
            <div class="slot-title">
              <span>Yesterday</span>
            </div>
            <b-list-group>
              <b-list-group-item href="#" class="unread-item">
                <i>
                  <img src="../../assets/images/notification/warning.png" alt>
                </i>
                <p>
                  Volunteering hours
                  <b>submitted the 17/05/2019 approved</b>
                </p>
                <span class="status"></span>
              </b-list-group-item>
              <b-list-group-item href="#" class="unread-item">
                <i>
                  <img src="../../assets/images/notification/warning.png" alt>
                </i>
                <p>
                  Volunteering hours
                  <b>submitted the 17/05/2019 approved</b>
                </p>
                <span class="status"></span>
              </b-list-group-item>
              <b-list-group-item href="#" class="unread-item">
                <i>
                  <img src="../../assets/images/notification/warning.png" alt>
                </i>
                <p>
                  Volunteering hours
                  <b>submitted the 17/05/2019 approved</b>
                </p>
                <span class="status"></span>
              </b-list-group-item>
            </b-list-group>
          </div>
          <div class="notification-clear">
            <div class="clear-content">
              <i>
                <img src="../../assets/images/notification/gray-bell-ic.svg" alt>
              </i>
              <p>You do not have any new notifications</p>
            </div>
          </div>
          <div class="notification-setting">
            <h3 class="setting-header">Notification Settings</h3>
            <div class="setting-body">
              <div class="setting-bar">
                <span>Get a notification for</span>
              </div>
              <b-list-group data-simplebar>
                <b-list-group-item>
                  <b-form-checkbox id="" value="accepted">Recommended Missions</b-form-checkbox>
                </b-list-group-item>
                <b-list-group-item>
                  <b-form-checkbox id="" value="accepted">Volunteering Hours</b-form-checkbox>
                </b-list-group-item>
                <b-list-group-item>
                  <b-form-checkbox id="" value="accepted">Volunteering Goals</b-form-checkbox>
                </b-list-group-item>
                <b-list-group-item>
                  <b-form-checkbox id="" value="accepted">My Comments</b-form-checkbox>
                </b-list-group-item>
                <b-list-group-item>
                  <b-form-checkbox id="" value="accepted">My Stories</b-form-checkbox>
                </b-list-group-item>
                <b-list-group-item>
                  <b-form-checkbox id="" value="accepted">New Stories</b-form-checkbox>
                </b-list-group-item>
                <b-list-group-item>
                  <b-form-checkbox id="" value="accepted">New Missions</b-form-checkbox>
                </b-list-group-item>
                <b-list-group-item>
                  <b-form-checkbox id="" value="accepted">New Messages</b-form-checkbox>
                </b-list-group-item>
              </b-list-group>
            </div>
            <div class="setting-footer">
              <b-button class="btn-bordersecondary" title="Save">Save</b-button>
              <b-button class="btn-borderprimary" title="Cancel" @click="cancelsetting">Cancel</b-button>
            </div>
          </div>
        </b-popover>
      </b-container>
    </b-navbar>
  </div>
</template>
<script>
import store from '../../store';
export default {
  components: {},
  name: "topheader",
  data() {
    return {
        bgImages: [
            require("@/assets/images/logo.png"),
            require("@/assets/images/optimy-logo.png")
        ],
        profileImages: require("@/assets/images/user-img.png"),
        popoverShow: false,
    };
  },
  mounted() {
    // var mob_nav_list = document
    var hasmenu_li = document.querySelectorAll(".menu-wrap li"); //array of parentchlid
    for (var i = 0; i < hasmenu_li.length; ++i) {
      var anchor_val = hasmenu_li[i].firstChild; // anchor tag variable
      //anchor tag click function
      anchor_val.addEventListener("click", function(e) {
        if (screen.width < 992) {
          e.stopPropagation();
          var parent_li = e.target.parentNode;
          var parent_ul = parent_li.parentNode;
          var sibling_li = parent_ul.childNodes;
          if (parent_li.classList.contains("active")) {
            parent_li.classList.remove("active");
          } else {
            parent_li.classList.add("active");
          }
          for (var j = 0; j < sibling_li.length; ++j) {
            if (sibling_li[j] != parent_li) {
              sibling_li[j].classList.remove("active");
            } else {
              var child_li = parent_li.getElementsByClassName("has-submenu");
              for (var k = 0; k < child_li.length; ++k) {
                child_li[k].classList.remove("active");
              }
            }
          }
        }
      });
    }
    var remove_active = document.querySelector(".navbar-toggler");
    remove_active.addEventListener("click", function() {
      if (screen.width < 992) {
        for (var i = 0; i < hasmenu_li.length; ++i) {
          hasmenu_li[i].classList.remove("active");
        }
      }
    });
    var back_btn = document.querySelectorAll(".btn-back");
    back_btn.forEach(function(e) {
      e.addEventListener("click", function() {
        if (screen.width < 992) {
          var active_item = e.parentNode.parentNode;
          active_item.classList.remove("active");
        }
      });
    });
    document.addEventListener("click", this.onClick);
  },
  methods: {
    onPopoverShow() {
      this.$refs.notficationPopover._toolpop
        .getTipElement()
        .classList.add("notification-popover");
    },
    showclearitem() {
      var popover_body = document.querySelector(".popover-body");
      popover_body.classList.add("clear-item");
    },
    showsetting() {
      var notify_setting = document.querySelector(".notification-setting");
      notify_setting.classList.toggle("show-setting");
    },
    cancelsetting() {
      var cancel_setting = document.querySelector(".notification-setting");
      cancel_setting.classList.remove("show-setting");
      this.$root.$emit("bv::show::popover", "notificationPopover");
    },
    openMenu() {
      var body = document.querySelectorAll("body, html");
      body.forEach(function(e) {
        e.classList.add("open-nav");
      });
    },
    closeMenu() {
      var body = document.querySelectorAll("body, html");
      body.forEach(function(e) {
        e.classList.remove("open-nav");
      });
    },
    searchMenu() {
      var body = document.querySelectorAll("body, html");
      body.forEach(function(e) {
        e.classList.toggle("open-search");
      });
    },
    handscroller() {
        if(screen.width > 768){
        this.$root.$emit("bv::hide::popover", "notificationPopover");
      }
    },
    logout(){
        this.$store.commit('logoutUser');
    }   
  },
  created() {
    document.addEventListener("scroll", this.handscroller);
  }
};
</script>
