<template>
    <div class="top-header">
        <b-navbar toggleable="lg">
            <b-container>
			    <b-navbar-toggle target="nav-collapse" @click="openMenu" v-if="this.$store.state.isLoggedIn">
                </b-navbar-toggle>
				<b-navbar-brand :to="{ name: 'home' }" :style="{backgroundImage: 'url('+this.$store.state.logo+')'}"
                 v-if="this.$store.state.isLoggedIn"
                 @click.native="clearFilter"
                 ></b-navbar-brand>
                <b-navbar-brand 
                    :to="{ name: 'login' }"
                    :style="{backgroundImage: 'url('+this.$store.state.logo+')'}"
                    v-else>
                </b-navbar-brand>
				
                <div class="menu-wrap" @touchend.stop>
                    <b-button class="btn-cross" @click="closeMenu">                        
                        <img :src="`${this.$store.state.imagePath}/assets/images/cross-ic.svg`" alt>                        
                    </b-button>
					<ul v-if="this.$store.state.isLoggedIn">
                        <li class="has-menu">
                          <a href="Javascript:void(0)" :title='$t("label.explore")'>{{ $t("label.explore")}}</a>

                          <ul class="dropdown-menu sub-dropdown">
                            <li 
                            v-bind:class="topThemeClass"
                            >
                              <a href="Javascript:void(0)">{{ $t("label.top_themes")}}</a>
                              <ul class="subdropdown-menu" v-if="topTheme != null && topTheme.length > 0">
                                <li v-for = "items in topTheme">
                                    <router-link 
                                    :to="{ path: '/home/themes/'+items.id}" @click.native="menuBarclickHandler"
                                    >
                                    {{ items.title}}
                                    </router-link>
                                </li>
                              </ul>
                            </li>
                            <li
                            v-bind:class="topCountryClass"
                            >
                            <a href="Javascript:void(0)">{{$t("label.top_country")}}</a>
                                <ul class="subdropdown-menu" v-if="topCountry != null && topCountry.length > 0">
                                    <li v-for = "items in topCountry">
                                    <router-link 
                                    :to="{ path: '/home/country/'+items.title.toLowerCase().trim()}"
                                    @click.native="menuBarclickHandler"
                                    >
                                    {{ items.title}}
                                    </router-link>
                                    </li>
                                </ul>
                            </li>
                            <li v-bind:class="topOrganizationClass">
                                <a href="Javascript:void(0)">{{ $t("label.top_organisation")}}</a>
                                <ul class="subdropdown-menu" v-if="topOrganization != null && topOrganization.length > 0">
                                <li v-for = "items in topOrganization">
                                    <router-link 
                                    :to="{ path: '/home/organization/'+items.title}" @click.native="menuBarclickHandler"
                                    >
                                    {{ items.title}}
                                    </router-link>
                                </li>
                              </ul>
                            </li>
                            <li class="no-dropdown">
                                <router-link 
                                    :to="{ path: '/home/most_ranked_missions'}" @click.native="menuBarclickHandler"
                                    >
                                    {{$t("label.most_ranked")}}
                                </router-link>
                            </li>
                            <li class="no-dropdown">
                                <router-link 
                                    :to="{ path: '/home/favourite_missions'}" @click.native="menuBarclickHandler"
                                    >
                                    {{$t("label.top_favourite")}}
                                </router-link>
                            </li>
                            <li class="no-dropdown">
                                <router-link 
                                    :to="{ path: '/home/recommended_missions'}" @click.native="menuBarclickHandler"
                                    >
                                    {{$t("label.recommended")}}
                                </router-link>
                            </li>
                            <li class="no-dropdown">
                                <router-link 
                                    :to="{ path: '/home/random_missions'}" @click.native="menuBarclickHandler"
                                    >
                                    {{$t("label.random")}}
                                </router-link>
                            </li>
                          </ul>
                        </li>
                        <li class="has-menu no-dropdown">
                          <a href="#" :title='$t("label.stories")'>{{ $t("label.stories")}}</a>
                        </li>
                        <li class="has-menu no-dropdown">
                          <a href="#" :title='$t("label.news")'>{{ $t("label.news")}}</a>
                        </li>
                        <li class="has-menu">
                          <a href="#" :title='$t("label.policy")'>{{ $t("label.policy")}}</a>
                          <ul class="dropdown-menu">
                            <li><a href="#">{{ $t("label.volunteering")}}</a></li>
                            <li><a href="#">{{ $t("label.sponsored")}}</a></li>
                          </ul>
                        </li>
                    </ul>
                </div>
                <b-nav class="ml-auto">
                    <b-nav-item right class="search-menu" @click="searchMenu">
                        <i>
                            <img :src="`${this.$store.state.imagePath}/assets/images/search-ic.svg`" alt>
                        </i>
                    </b-nav-item>
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
                    :show="popoverShow">
                    <template slot="title">
                        <b-button class="btn-setting" title="Setting" @click="showsetting">
                            <img :src="`${this.$store.state.imagePath}/assets/images/settings-ic.svg`" alt="Setting icon">
                            
                        </b-button>
                        <span class="title">Notification</span>
                        <b-button class="btn-clear"  @click="showclearitem">{{$t("label.clear_all")}}</b-button>
                    </template>
                    <div class="notification-details" data-simplebar>
                        <b-list-group>
                            <b-list-group-item href="#" class="unread-item">
                                <i>
                                    <img :src="`${this.$store.state.imagePath}/assets/images/user.png`" alt>                                    
                                </i>
                                <p>
                                    John Doe: Recommend this mission -<b>Grow Trees</b>
                                </p>
                            <span class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="read-item">
                                <i>
                                    <img :src="`${this.$store.state.imagePath}/assets/images/circle-plus.png`" alt>
                                    
                                </i>
                                <p>
                                    John Doe: Recommend this mission -
                                    <b>Save the Children</b>
                                </p>
                                <span class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="read-item">
                                <i>
                                    <img :src="`${this.$store.state.imagePath}/assets/images/circle-plus.png`" alt>
                                </i>
                                <p>New Mission -<b>Save the world</b></p>
                                <span class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="unread-item">
                                <i>
                                    <img :src="`${this.$store.state.imagePath}/assets/images/warning.pngg`" alt>
                                    
                                </i>
                                <p>New Message -<b>Message title goes here</b></p>
                                <span class="status"></span>
                            </b-list-group-item>
                            </b-list-group>
                                <div class="slot-title">
                                    <span>Yesterday</span>
                                </div>
                            <b-list-group>
                            <b-list-group-item href="#" class="unread-item">
                                <i>
                                    <img :src="`${this.$store.state.imagePath}/assets/images/warning.pngg`" alt>
                                </i>
                                <p>
                                    Volunteering hours<b>submitted the 17/05/2019 approved</b>
                                </p>
                                <span class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="unread-item">
                                <i>
                                    <img :src="`${this.$store.state.imagePath}/assets/images/warning.pngg`" alt>
                                </i>
                                <p>Volunteering hours<b>submitted the 17/05/2019 approved</b></p>
                                <span class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="unread-item">
                                <i>
                                    <img :src="`${this.$store.state.imagePath}/assets/images/warning.pngg`" alt>
                                </i>
                                <p>Volunteering hours<b>submitted the 17/05/2019 approved</b></p>
                                <span class="status"></span>
                            </b-list-group-item>
                            </b-list-group>
                    </div>
                    <div class="notification-clear">
                        <div class="clear-content">
                            <i>
                                <img :src="`${this.$store.state.imagePath}/assets/images/gray-bell-ic.svg`" alt>
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
import {exploreMission} from '../../services/service';
import {eventBus} from "../../main";
export default {
    components: {},
    name: "PrimaryHeader",
    data() {
        return {
            popoverShow: false,
            topTheme :[],
            topCountry :[],
            topCountryClass: 'no-dropdown',
            topThemeClass : 'no-dropdown',
            topOrganizationClass : 'no-dropdown',
            filterData : [],
            topOrganization:[],
        };
    },
    mounted() {
        // var mob_nav_list = document
        var hasmenu_li = document.querySelectorAll(".menu-wrap li"); //array of parentchlid
        
        for (var i = 0; i < hasmenu_li.length; ++i) {
            var anchor_val = hasmenu_li[i].firstChild; // anchor tag variable
            //Anchor tag click function
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
        },
        menuBarclickHandler($event) {
           
            if(this.$route.params.searchParamsType) {
                this.filterData['parmasType'] = this.$route.params.searchParamsType;
            }
            if(this.$route.params.searchParams) {
                this.filterData['parmas'] = this.$route.params.searchParams;
            }
           
            const doSomething = async () => {
               await eventBus.$emit('clearAllFilters');
            }
            eventBus.$emit('setDefaultText');
            this.$emit('exploreMisison',this.filterData);
        },
        
        async exploreMissions(){
            await exploreMission().then( response => {
                let menuBar = JSON.parse(store.state.menubar);
                this.topTheme =  menuBar.top_theme;
                this.topCountry =  menuBar.top_country;
                this.topOrganization =  menuBar.top_organization;
                if (this.topTheme != null && this.topTheme.length > 0 ) {
                    this.topThemeClass = 'has-submenu';
                }
                if (this.topCountry != null && this.topCountry.length > 0 ) {
                    this.topCountryClass = 'has-submenu';
                }
                if (this.topOrganization != null && this.topOrganization.length > 0 ) {
                    this.topOrganizationClass = 'has-submenu';
                }  
            }); 
        },   
        clearFilter($event) {
            if(store.state.isLoggedIn) {
                let filters = {};
                filters.exploreMissionType = '';
                filters.exploreMissionParams = '';
                store.commit("exploreFilter",filters);
                this.$emit('getMissions');
            }
        },
    },
    created() {
        document.addEventListener("scroll", this.handscroller);
        if(store.state.isLoggedIn) {
            this.exploreMissions();
        }   
    }
    };
</script>
