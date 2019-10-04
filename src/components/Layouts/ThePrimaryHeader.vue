    <template>
        <div class="top-header">
            <b-navbar toggleable="lg">
                <b-container>
                    <div class="navbar-toggler" @click.stop v-if="this.$store.state.isLoggedIn">
                        <b-link title="Menu" @click="openMenu" class="toggler-icon">
                            <img :src="$store.state.imagePath+'/assets/images/menu-ic.svg'" alt />
                        </b-link>
                    </div>
                    <b-navbar-brand :to="{ name: 'home' }" :style="{backgroundImage: 'url('+this.$store.state.logo+')'}"
                        v-if="this.$store.state.isLoggedIn" @click.native="clearFilter"></b-navbar-brand>
                    <b-navbar-brand :to="{ name: 'login' }"
                        :style="{backgroundImage: 'url('+this.$store.state.logo+')'}" v-else>
                    </b-navbar-brand>

                    <div class="menu-wrap" @click.stop>
                        <b-button class="btn-cross" @click="closeMenu">
                            <img :src="$store.state.imagePath+'/assets/images/cross-ic.svg'" alt>
                        </b-button>
                        <ul v-if="this.$store.state.isLoggedIn">
                            <li class="has-menu">
                                <a href="Javascript:void(0)"
                                    :title='languageData.label.explore'>{{ languageData.label.explore}}</a>

                                <ul class="dropdown-menu sub-dropdown">
                                    <li v-if="isThemeDisplay" v-bind:class="topThemeClass">
                                        <a href="Javascript:void(0)">{{ languageData.label.top_themes}}</a>
                                        <ul class="subdropdown-menu" v-if="topTheme != null && topTheme.length > 0">
                                            <li v-for="(items, key) in topTheme" v-bind:key=key>
                                                <router-link :to="{ path: '/home/themes/'+items.id}"
                                                    @click.native="menuBarclickHandler">
                                                    {{ items.title}}
                                                </router-link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li v-bind:class="topCountryClass">
                                        <a href="Javascript:void(0)">{{languageData.label.top_country}}</a>
                                        <ul class="subdropdown-menu" v-if="topCountry != null && topCountry.length > 0">
                                            <li v-for="(items, key) in topCountry" v-bind:key=key>
                                                <router-link
                                                    :to="{ path: '/home/country/'+items.title.toLowerCase().trim()}"
                                                    @click.native="menuBarclickHandler">
                                                    {{ items.title}}
                                                </router-link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li v-bind:class="topOrganizationClass">
                                        <a href="Javascript:void(0)">{{ languageData.label.top_organisation}}</a>
                                        <ul class="subdropdown-menu"
                                            v-if="topOrganization != null && topOrganization.length > 0">
                                            <li v-for="(items, key) in topOrganization" v-bind:key=key>
                                                <router-link :to="{ path: '/home/organization/'+items.title}"
                                                    @click.native="menuBarclickHandler">
                                                    {{ items.title}}
                                                </router-link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="no-dropdown">
                                        <router-link :to="{ path: '/home/most-ranked-missions'}"
                                            @click.native="menuBarclickHandler">
                                            {{languageData.label.most_ranked}}
                                        </router-link>
                                    </li>
                                    <li class="no-dropdown">
                                        <router-link :to="{ path: '/home/favourite-missions'}"
                                            @click.native="menuBarclickHandler">
                                            {{languageData.label.top_favourite}}
                                        </router-link>
                                    </li>
                                    <li class="no-dropdown">
                                        <router-link :to="{ path: '/home/recommended-missions'}"
                                            @click.native="menuBarclickHandler">
                                            {{languageData.label.recommended}}
                                        </router-link>
                                    </li>
                                    <li class="no-dropdown">
                                        <router-link :to="{ path: '/home/random-missions'}"
                                            @click.native="menuBarclickHandler">
                                            {{languageData.label.random}}
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-menu no-dropdown" v-if="isStoryDisplay">
                                <router-link :to="{ path: '/stories'}"
                                    >
                                    {{languageData.label.stories}}
                                </router-link>
                            </li>
                            <li class="has-menu no-dropdown" v-if="isNewsDisplay">
                                <router-link :to="{ path: '/news'}"
                                    >
                                    {{languageData.label.news}}
                                </router-link>
                            </li>

                            <li class="has-menu" v-if="isPolicyDisplay && policyPage.length > 0">
                                <a href="Javascript:void(0)"
                                    :title='languageData.label.policy'>{{ languageData.label.policy}}
                                </a>
                                <ul class="dropdown-menu" v-if="policyPage.length > 0">
                                    <li v-for="(item, key) in policyPage" v-bind:key=key>
                                        <router-link :to="{ path: '/policy/'+item.slug}"
                                            @click.native="menuBarclickHandler">
                                            {{item.pages[0].title}}
                                        </router-link>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </div>
                    <b-nav class="ml-auto">
                        <b-nav-item right class="search-menu" @click="searchMenu">
                            <i>
                                <img :src="$store.state.imagePath+'/assets/images/search-ic.svg'" alt>
                            </i>
                        </b-nav-item>
                        <b-nav-item-dropdown right class="profile-menu" v-if="this.$store.state.isLoggedIn">
                            <template slot="button-content">
                                <i :style="{backgroundImage: 'url('+this.$store.state.avatar+')'}"></i>
                                <em>{{this.$store.state.firstName+' '+this.$store.state.lastName}}</em>
                            </template>
                            <b-dropdown-item :to="{ name: 'dashboard' }">{{ languageData.label.dashboard}}
                            </b-dropdown-item>
                            <b-dropdown-item :to="{ name: 'myAccount' }">{{ languageData.label.my_account}}
                            </b-dropdown-item>
                            <b-dropdown-item v-on:click.native="logout()" replace v-if="this.$store.state.isLoggedIn">
                                {{ languageData.label.logout}}
                            </b-dropdown-item>
                        </b-nav-item-dropdown>
                    </b-nav>
                    <b-popover target="notificationPopover" placement="topleft" container="notifyPopoverWrap"
                        @show="onPopoverShow" ref="notficationPopover" triggers="click blur" :show="popoverShow">
                        <template slot="title">
                            <b-button class="btn-setting" title="Setting" @click="showsetting">
                                <img :src="$store.state.imagePath+'/assets/images/settings-ic.svg'" alt="Setting icon">

                            </b-button>
                            <span class="title">Notification</span>
                            <b-button class="btn-clear" @click="showclearitem">{{languageData.label.clear_all}}
                            </b-button>
                        </template>
                        <div class="notification-details" data-simplebar>
                            <b-list-group>
                                <b-list-group-item href="#" class="unread-item">
                                    <i>
                                        <img :src="$store.state.imagePath+'/assets/images/user.png'" alt>
                                    </i>
                                    <p>
                                        John Doe: Recommend this mission -<b>Grow Trees</b>
                                    </p>
                                    <span class="status"></span>
                                </b-list-group-item>
                                <b-list-group-item href="#" class="read-item">
                                    <i>
                                        <img :src="$store.state.imagePath+'/assets/images/circle-plus.png'" alt>

                                    </i>
                                    <p>
                                        John Doe: Recommend this mission -
                                        <b>Save the Children</b>
                                    </p>
                                    <span class="status"></span>
                                </b-list-group-item>
                                <b-list-group-item href="#" class="read-item">
                                    <i>
                                        <img :src="$store.state.imagePath+'/assets/images/circle-plus.png'" alt>
                                    </i>
                                    <p>New Mission -<b>Save the world</b></p>
                                    <span class="status"></span>
                                </b-list-group-item>
                                <b-list-group-item href="#" class="unread-item">
                                    <i>
                                        <img :src="$store.state.imagePath+'/assets/images/warning.png'" alt>

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
                                        <img :src="$store.state.imagePath+'/assets/images/warning.png'" alt>
                                    </i>
                                    <p>
                                        Volunteering hours<b>submitted the 17/05/2019 approved</b>
                                    </p>
                                    <span class="status"></span>
                                </b-list-group-item>
                                <b-list-group-item href="#" class="unread-item">
                                    <i>
                                        <img :src="$store.state.imagePath+'/assets/images/warning.png'" alt>
                                    </i>
                                    <p>Volunteering hours<b>submitted the 17/05/2019 approved</b></p>
                                    <span class="status"></span>
                                </b-list-group-item>
                                <b-list-group-item href="#" class="unread-item">
                                    <i>
                                        <img :src="$store.state.imagePath+'/assets/images/warning.png'" alt>
                                    </i>
                                    <p>Volunteering hours<b>submitted the 17/05/2019 approved</b></p>
                                    <span class="status"></span>
                                </b-list-group-item>
                            </b-list-group>
                        </div>
                        <div class="notification-clear">
                            <div class="clear-content">
                                <i>
                                    <img :src="$store.state.imagePath+'/assets/images/gray-bell-ic.svg'" alt>
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
                                <b-button class="btn-borderprimary" title="Cancel" @click="cancelsetting">Cancel
                                </b-button>
                            </div>
                        </div>
                    </b-popover>
                </b-container>
            </b-navbar>
        </div>
    </template>

    <script>
        import store from '../../store';
        import {
            exploreMission,
            policy
        } from '../../services/service';
        import {
            eventBus
        } from "../../main";
        import constants from '../../constant';
        import { setTimeout } from 'timers';
        export default {
            components: {},
            name: "PrimaryHeader",
            data() {
                return {
                    popoverShow: false,
                    topTheme: [],
                    topCountry: [],
                    topCountryClass: 'no-dropdown',
                    topThemeClass: 'no-dropdown',
                    topOrganizationClass: 'no-dropdown',
                    filterData: [],
                    topOrganization: [],
                    languageData: [],
                    policyPage: [],
                    isThemeDisplay: true,
                    isStoryDisplay: true,
                    isNewsDisplay: true,
                    isPolicyDisplay: true
                };
            },
            mounted() {
                let hasmenu_li = document.querySelectorAll(".menu-wrap li"); //array of parentchlid
                for (let i = 0; i < hasmenu_li.length; ++i) {
                    let anchorVal = hasmenu_li[i].firstChild; // anchor tag letiable
                    //Anchor tag click function
                    anchorVal.addEventListener("click", function (e) {
                        if (screen.width < 992) {
                            e.stopPropagation();
                            let parentLi = e.target.parentNode;
                            let parentUl = parentLi.parentNode;
                            let siblingLi = parentUl.childNodes;
                            if (parentLi.classList.contains("active")) {
                                parentLi.classList.remove("active");
                            } else {
                                parentLi.classList.add("active");
                            }
                            for (let j = 0; j < siblingLi.length; ++j) {
                                if (siblingLi[j] != parentLi) {
                                    siblingLi[j].classList.remove("active");
                                } else {
                                    let childLi = parentLi.getElementsByClassName("has-submenu");
                                    for (let k = 0; k < childLi.length; ++k) {
                                        childLi[k].classList.remove("active");
                                    }
                                }
                            }
                        }
                    });
                }

                let backBtn = document.querySelectorAll(".btn-back");
                backBtn.forEach(function (e) {
                    e.addEventListener("click", function () {
                        if (screen.width < 992) {
                            let activeItem = e.parentNode.parentNode;
                            activeItem.classList.remove("active");
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
                    let popoverBody = document.querySelector(".popover-body");
                    popoverBody.classList.add("clear-item");
                },
                showsetting() {
                    let notifySetting = document.querySelector(".notification-setting");
                    notifySetting.classList.toggle("show-setting");
                },
                cancelsetting() {
                    let cancelSetting = document.querySelector(".notification-setting");
                    cancelSetting.classList.remove("show-setting");
                    this.$root.$emit("bv::show::popover", "notificationPopover");
                },
                openMenu() {
                    let body = document.querySelectorAll("body, html");
                    body.forEach(function (e) {
                        e.classList.add("open-nav");
                    });
                },
                closeMenu() {
                    let body = document.querySelectorAll("body, html");
                    body.forEach(function (e) {
                        e.classList.remove("open-nav");
                    });
                },
                searchMenu() {
                    let body = document.querySelectorAll("body, html");
                    body.forEach(function (e) {
                        e.classList.toggle("open-search");
                    });
                },
                handscroller() {
                    if (screen.width > 768) {
                        this.$root.$emit("bv::hide::popover", "notificationPopover");
                    }
                },
                logout() {
                    document.querySelector('body').classList.remove('small-header');
                    this.$store.commit('logoutUser');
                },
                menuBarclickHandler($event) {

                    if (this.$route.params.searchParamsType) {
                        this.filterData['parmasType'] = this.$route.params.searchParamsType;
                    }
                    if (this.$route.params.searchParams) {
                        this.filterData['parmas'] = this.$route.params.searchParams;
                    }
                    const doSomething = async () => {
                        await eventBus.$emit('clearAllFilters');
                    }
                    eventBus.$emit('setDefaultText');
                    this.$emit('exploreMisison', this.filterData);
                },

                async exploreMissions() {
                    await exploreMission().then(response => {
                        let menuBar = JSON.parse(store.state.menubar);
                        this.topTheme = menuBar.top_theme;
                        this.topCountry = menuBar.top_country;
                        this.topOrganization = menuBar.top_organization;
                        if (this.topTheme != null && this.topTheme.length > 0) {
                            this.topThemeClass = 'has-submenu';
                        }
                        if (this.topCountry != null && this.topCountry.length > 0) {
                            this.topCountryClass = 'has-submenu';
                        }
                        if (this.topOrganization != null && this.topOrganization.length > 0) {
                            this.topOrganizationClass = 'has-submenu';
                        }
                        // Call get policy service 
                        this.getPolicyPage();
                    });
                },

                async clearFilter($event) {
                    if (store.state.isLoggedIn) {
                        this.$router.push({
                           name: 'home'
                        })
                        setTimeout(() => {
                            location.reload()
                        },15)
                    }
                },

                async getPolicyPage() {
                    await policy().then(response => {
                        if (response.error == false) {
                            this.policyPage = response.data;
                        }
                    });
                },
            },
            created() {
                this.languageData = JSON.parse(store.state.languageLabel);
                document.addEventListener("scroll", this.handscroller);
                this.isThemeDisplay = this.settingEnabled(constants.THEMES_ENABLED);
                this.isStoryDisplay = this.settingEnabled(constants.STORIES_ENABLED);
                this.isNewsDisplay = this.settingEnabled(constants.NEWS_ENABLED);
                this.isPolicyDisplay = this.settingEnabled(constants.POLICIES_ENABLED);

                if (store.state.isLoggedIn) {
                    this.exploreMissions();
                }
            }
        };
    </script>