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
                                <i class="collapse-toggle"></i>
                                <ul class="dropdown-menu sub-dropdown">
                                    <li v-if="isThemeDisplay" v-bind:class="topThemeClass">
                                        <a href="Javascript:void(0)">{{ languageData.label.top_themes}}</a>
                                        <i class="collapse-toggle"></i>
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
                                        <i class="collapse-toggle"></i>
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
                                        <i class="collapse-toggle"></i>
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
                                <router-link :to="{ path: '/stories'}">
                                    {{languageData.label.stories}}
                                </router-link>
                            </li>
                            <li class="has-menu no-dropdown" v-if="isNewsDisplay">
                                <router-link :to="{ path: '/news'}">
                                    {{languageData.label.news}}
                                </router-link>
                            </li>

                            <li class="has-menu" v-if="isPolicyDisplay && policyPage.length > 0">
                                <a href="Javascript:void(0)"
                                    :title='languageData.label.policy'>{{ languageData.label.policy}}
                                </a>
                                <i class="collapse-toggle"></i>
                                <ul class="dropdown-menu" v-if="policyPage.length > 0">
                                    <li v-for="(item, key) in policyPage" v-bind:key=key>
                                        <router-link :to="{ path: '/policy/'+item.slug}" v-if="item.pages[0]"
                                            @click.native="menuBarclickHandler">
                                            {{item.pages[0].title}}
                                        </router-link>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </div>
                    <div class="header-right ml-auto">
                        <b-nav>
                            <b-nav-item right class="search-menu" @click="searchMenu">
                                <i>
                                    <img :src="$store.state.imagePath+'/assets/images/search-ic.svg'" alt>
                                </i>
                            </b-nav-item>
                            <b-nav-item right class="notification-menu" id="notifyPopoverWrap"
                                v-if="this.$store.state.isLoggedIn">
                                <button id="notificationPopover" class="btn-notification"
                                    @click="getNotificationListing">
                                    <i>
                                        <img :src="$store.state.imagePath+'/assets/images/bell-ic.svg'"
                                            alt="Notification Icon" />
                                    </i>
                                    <b-badge>2</b-badge>
                                </button>
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
                                <b-dropdown-item v-on:click.native="logout()" replace
                                    v-if="this.$store.state.isLoggedIn">
                                    {{ languageData.label.logout}}
                                </b-dropdown-item>
                            </b-nav-item-dropdown>
                        </b-nav>
                        <b-popover target="notificationPopover" placement="topleft" container="notifyPopoverWrap"
                            @show="onPopoverShow" ref="notficationPopover" triggers="click">
                            <template slot="title">
                                <b-button class="btn-setting" title="Setting" @click="showsetting">
                                    <img :src="$store.state.imagePath+'/assets/images/settings-ic.svg'"
                                        alt="Setting icon">

                                </b-button>
                                <span class="title">{{languageData.label.notification}}</span>
                                <b-button class="btn-clear" @click="showclearitem">{{languageData.label.clear_all}}
                                </b-button>
                            </template>
                            <div class="notification-details" data-simplebar>
                                <b-list-group>
                                    <b-list-group-item href="#" class="unread-item">
                                        <i>
                                            <img :src="$store.state.imagePath+'/assets/images/user.png'" alt />
                                        </i>
                                        <p>
                                            John Doe: Recommend this mission -
                                            <b>Grow Trees</b>
                                        </p>
                                        <span class="status"></span>
                                    </b-list-group-item>
                                    <b-list-group-item href="#" class="read-item">
                                        <i>
                                            <img :src="$store.state.imagePath+'/assets/images/circle-plus.png'" alt />
                                        </i>
                                        <p>
                                            John Doe: Recommend this mission -
                                            <b>Save the Children</b>
                                        </p>
                                        <span class="status"></span>
                                    </b-list-group-item>
                                    <b-list-group-item href="#" class="read-item">
                                        <i>
                                            <img :src="$store.state.imagePath+'/assets/images/circle-plus.png'" alt />
                                        </i>
                                        <p>
                                            New Mission -
                                            <b>Save the world</b>
                                        </p>
                                        <span class="status"></span>
                                    </b-list-group-item>
                                    <b-list-group-item href="#" class="unread-item">
                                        <i>
                                            <img :src="$store.state.imagePath+'/assets/images/warning.png'" alt />
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
                                            <img :src="$store.state.imagePath+'/assets/images/warning.png'" alt />
                                        </i>
                                        <p>
                                            Volunteering hours
                                            <b>submitted the 17/05/2019 approved</b>
                                        </p>
                                        <span class="status"></span>
                                    </b-list-group-item>
                                    <b-list-group-item href="#" class="unread-item">
                                        <i>
                                            <img :src="$store.state.imagePath+'/assets/images/warning.png'" alt />
                                        </i>
                                        <p>
                                            Volunteering hours
                                            <b>submitted the 17/05/2019 approved</b>
                                        </p>
                                        <span class="status"></span>
                                    </b-list-group-item>
                                    <b-list-group-item href="#" class="unread-item">
                                        <i>
                                            <img :src="$store.state.imagePath+'/assets/images/warning.png'" alt />
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
                                        <img :src="$store.state.imagePath+'/assets/images/gray-bell-ic.svg'" alt />
                                    </i>
                                    <p>You do not have any new notifications</p>
                                </div>
                            </div>
                            <div class="notification-setting">
                                <h3 class="setting-header">Notification Settings</h3>
                                <div class="setting-body" v-if="notificationSettingList.length > 0">
                                    <div class="setting-bar">
                                        <span>{{languageData.label.get_notification_for}}</span>
                                    </div>
                                    <b-list-group data-simplebar>
                                        <b-form-checkbox-group id="checkbox-group-2" v-model="selectedNotification"
                                            name="flavour-2">
                                            <b-list-group-item v-for="(data, index) in notificationSettingList"
                                                :key="index">
                                                <b-form-checkbox :value="data.notification_type_id">
                                                    {{data.notification_type}} </b-form-checkbox>
                                            </b-list-group-item>
                                        </b-form-checkbox-group>
                                    </b-list-group>
                                </div>
                                <div class="setting-footer">
                                    <b-button class="btn-bordersecondary" @click="saveNotificationSetting">
                                        {{languageData.label.save}}</b-button>
                                    <b-button class="btn-borderprimary" @click="cancelsetting">
                                        {{languageData.label.cancel}}
                                    </b-button>
                                </div>
                            </div>
                        </b-popover>
                        <!-- <b-popover target="notificationPopover" placement="topleft" container="notifyPopoverWrap"
                            @show="onPopoverShow" ref="notficationPopover" triggers="click blur" :show="popoverShow">
                            <template slot="title">
                                <b-button class="btn-setting" title="Setting" @click="showsetting">
                                    <img :src="$store.state.imagePath+'/assets/images/settings-ic.svg'"
                                        alt="Setting icon">

                                </b-button>
                                <span class="title">{{languageData.label.notification}}</span>
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
                                    <p>{{languageData.label.no_new_notifications}}</p>
                                </div>
                            </div>
                            <div class="notification-setting">
                                <h3 class="setting-header">{{languageData.label.notification_settings}}</h3>
                                <div class="setting-body" v-if="notificationSettingList.length > 0">
                                    <div class="setting-bar">
                                        <span>{{languageData.label.get_notification_for}}</span>
                                    </div>
                                    <b-list-group data-simplebar >

                                            <b-form-checkbox-group id="checkbox-group-2" v-model="selectedNotification" name="flavour-2">
                                                <b-list-group-item v-for="(data, index) in notificationSettingList">
                                                    <b-form-checkbox :value="data.notification_type_id">{{data.notification_type}}</b-form-checkbox>
                                                </b-list-group-item>
                                            </b-form-checkbox-group>

                                    </b-list-group>
                                </div>

                                <div class="setting-footer">
                                    <b-button class="btn-bordersecondary" @click="saveNotificationSetting">{{languageData.label.save}}</b-button>
                                    <b-button class="btn-borderprimary" @click="cancelsetting">
                                        {{languageData.label.cancel}}
                                    </b-button>
                                </div>
                            </div>
                        </b-popover> -->

                    </div>
                </b-container>
            </b-navbar>
        </div>
    </template>

    <script>
        import store from '../../store';
        import {
            exploreMission,
            policy,        
            notificationSettingListing,
            updateNotificationSetting
        } from '../../services/service';
        import {
            eventBus
        } from "../../main";
        import constants from '../../constant';
        import {
            setTimeout
        } from 'timers';
        export default {
            components: {
                
            },
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
                    isPolicyDisplay: true,
                    isNotificationAjaxCall: false,
                    notificationSettingList: [],
                    selectedNotification: [],
                    notificationSettingId: []
                };
            },
            mounted() {
                let hasmenuIcon = document.querySelectorAll(
                    ".menu-wrap li .collapse-toggle"
                );
                for (let i = 0; i < hasmenuIcon.length; ++i) {
                    let iconValue = hasmenuIcon[i];
                    iconValue.addEventListener("click", function (e) {
                        if (screen.width < 992) {
                            e.stopPropagation();
                            let parentList = e.target.parentNode;
                            let parentUl = parentList.parentNode;
                            let siblingList = parentUl.childNodes;
                            if (parentList.classList.contains("active")) {
                                parentList.classList.remove("active");
                            } else {
                                parentList.classList.add("active");
                            }
                            for (let j = 0; j < siblingList.length; ++j) {
                                if (siblingList[j] != parentList) {
                                    siblingList[j].classList.remove("active");
                                } else {
                                    let childList = parentList.getElementsByClassName("has-submenu");
                                    for (let k = 0; k < childList.length; ++k) {
                                        childList[k].classList.remove("active");
                                    }
                                }
                            }
                        }
                    });
                }
                let hasmenuList = document.querySelectorAll(".menu-wrap li");
                let removeActive = document.querySelector(".navbar-toggler");
                let breadcrumbDropdown = document.querySelector(
                    ".breadcrumb-dropdown-wrap"
                );
                for (let i = 0; i < hasmenuList.length; i++) {
                    let anchor_val = hasmenuList[i].firstChild;
                    anchor_val.addEventListener("click", function (e) {
                        if (screen.width < 992) {
                            let body = document.querySelectorAll("body, html");
                            body.forEach(function (e) {
                                e.classList.remove("open-nav");
                            });
                        }
                    });
                }
                removeActive.addEventListener("click", function () {
                    if (screen.width < 992) {
                        for (let i = 0; i < hasmenuList.length; ++i) {
                            hasmenuList[i].classList.remove("active");
                        }
                    }
                    if (screen.width < 768) {
                        if (breadcrumbDropdown != null) {
                            breadcrumbDropdown.classList.remove("open");
                        }
                    }
                });
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
                    var popover_body = document.querySelector(".popover-body");
                    popover_body.classList.add("clear-item");
                },
                showsetting() {
                    var popover_body = document.querySelector(".popover-body");
                    popover_body.classList.toggle("show-setting");
                },
                cancelsetting() {
                    this.selectedNotification = []
                    var popover_body = document.querySelector(".popover-body");
                    popover_body.classList.remove("show-setting");
                    this.$root.$emit("bv::show::popover", "notificationPopover");
                    this.notificationSettingList.filter((data, index) => {
                        if (data.is_active == 1) {
                            this.selectedNotification.push(data.notification_type_id)
                        }
                    })
                },
                openMenu() {
                    var body = document.querySelectorAll("body, html");
                    body.forEach(function (e) {
                        e.classList.add("open-nav");
                    });
                },
                closeMenu() {
                    var body = document.querySelectorAll("body, html");
                    body.forEach(function (e) {
                        e.classList.remove("open-nav");
                    });
                },
                searchMenu() {
                    var body = document.querySelectorAll("body, html");
                    body.forEach(function (e) {
                        e.classList.toggle("open-search");
                    });
                },
                logout() {
                    document.querySelector('body').classList.remove('small-header');
                    this.$store.commit('logoutUser');
                },
                menuBarclickHandler() {

                    if (this.$route.params.searchParamsType) {
                        this.filterData['parmasType'] = this.$route.params.searchParamsType;
                    }
                    if (this.$route.params.searchParams) {
                        this.filterData['parmas'] = this.$route.params.searchParams;
                    }
                    async () => {
                        await eventBus.$emit('clearAllFilters');
                    }
                    eventBus.$emit('setDefaultText');
                    this.$emit('exploreMisison', this.filterData);
                     var body = document.querySelectorAll("body, html");
                    body.forEach(function (e) {
                        e.classList.remove("open-nav");
                    });
                },

                async exploreMissions() {
                    await exploreMission().then(() => {
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

                async clearFilter() {
                    if (store.state.isLoggedIn) {
                        this.$router.push({
                            name: 'home'
                        })
                        setTimeout(() => {
                            location.reload()
                        }, 15)
                    }
                },

                async getPolicyPage() {
                    await policy().then(response => {
                        if (response.error == false) {
                            this.policyPage = response.data;
                        }
                    });
                },

                getNotificationListing() {
                    this.isNotificationAjaxCall = true;
                    notificationSettingListing().then(response => {
                        this.isNotificationAjaxCall = false;
                        if (response.error == false) {
                            if (response.data) {
                                this.notificationSettingList = response.data
                                this.notificationSettingList.filter((data, index) => {
                                    // console.log(data.notification_type)
                                    data.notification_type = this.languageData.label[data
                                        .notification_type]
                                    this.notificationSettingId.push(data.notification_type_id);
                                    if (data.is_active == 1) {
                                        this.selectedNotification.push(data.notification_type_id)
                                    }
                                })
                            }
                        }
                    })
                },
                saveNotificationSetting() {
                    let data = {
                        'settings': []
                    }
                    let settingArray = []

                    this.notificationSettingId.filter((data, index) => {
                        let values = 0;
                        if (this.selectedNotification.includes(data)) {
                            values = 1;
                        }
                        settingArray.push({
                            'notification_type_id': data,
                            'value': values
                        })

                    })
                    data.settings = settingArray
                    updateNotificationSetting(data).then(response => {
                        let classVariant = 'success'
                        if (response.error == true) {
                            classVariant = 'danger'
                        }
                        this.makeToast(classVariant, response.message)
                    })
                },
                makeToast(variant = null, message) {
                    this.$bvToast.toast(message, {
                        variant: variant,
                        solid: true,
                        autoHideDelay: 3000
                    })
                },
            },
            created() {
                this.languageData = JSON.parse(store.state.languageLabel);
                setTimeout(function () {
                    var body = document.querySelector("body");
                    var notification_btn = document.querySelector(".btn-notification");
                    body.addEventListener("click", function () {
                        var notification_popover = document.querySelector(
                            ".notification-popover"
                        );
                        if (notification_popover != null) {
                            notification_btn.click();
                        }
                    });

                    var notificationMenu = document.querySelector(".notification-menu");
                    if (notificationMenu != null) {
                        notificationMenu.addEventListener("click", function (e) {
                            e.stopPropagation();
                        });
                    }
                }, 1000);
                document.addEventListener("scroll", this.handscroller);
                this.isThemeDisplay = this.settingEnabled(constants.THEMES_ENABLED);
                this.isStoryDisplay = this.settingEnabled(constants.STORIES_ENABLED);
                this.isNewsDisplay = this.settingEnabled(constants.NEWS_ENABLED);
                this.isPolicyDisplay = this.settingEnabled(constants.POLICIES_ENABLED);
                if (store.state.isLoggedIn) {
                    this.exploreMissions();
                }

                window.addEventListener("resize", function () {
                    let body = document.querySelectorAll("body, html");
                    if (screen.width > 991) {
                        body.forEach(function (e) {
                            e.classList.remove("open-nav");
                            e.classList.remove("open-filter");
                        });
                    }
                });
            }
        };
    </script>
