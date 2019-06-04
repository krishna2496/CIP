<template>
    <div class="top-header">
        <b-navbar toggleable="lg">
            <b-container>
                <b-navbar-toggle target="nav-collapse" @click="openMenu"></b-navbar-toggle>
                <b-navbar-brand href="#" :style="{backgroundImage: 'url('+bgImages[1]+')'}"></b-navbar-brand>
                <!-- <b-collapse id="nav-collapse" is-nav> -->
                    <div class="menu-wrap">
                        <ul>
                            <li class="has-menu"><a href="#" title="Explore">Explore</a>
							<div class="dropdown-menu-outer">
								<a href="#" class="btn-back">Back</a>
                                <ul class="dropdown-menu sub-dropdown">
                                    <li class="has-submenu"><a href="#">Top Themes</a>
                                        <ul class="subdropdown-menu">
                                            <li><a href="#">Education</a></li>
                                            <li><a href="#">Children</a></li>
                                            <li><a href="#">Health</a></li>
                                            <li><a href="#">Animals</a></li>
                                            <li><a href="#">Nutritions</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-submenu"><a href="#">Top Countries</a>
                                        <ul class="subdropdown-menu">
                                            <li><a href="#">Education</a></li>
                                            <li><a href="#">Children</a></li>
                                            <li><a href="#">Health</a></li>
                                            <li><a href="#">Animals</a></li>
                                            <li><a href="#">Nutritions</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#">Top Organisation</a></li>
                                    <li><a href="#">Most Ranked</a></li>
                                    <li><a href="#">Top Favourite</a></li>
                                    <li><a href="#">Recommended</a></li>
                                    <li><a href="#">Random</a></li>
                                </ul>
								</div>
                            </li>
                            <li class="has-menu no-dropdown"><a href="#" title="Stories">Stories</a></li>
                            <li class="has-menu no-dropdown"><a href="#" title="News">News</a></li>
                            <li class="has-menu"><a href="#" title="Policy">Policy</a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Volunteering</a></li>
                                    <li><a href="#">Sponsored</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                <!-- </b-collapse> -->
                <b-nav class="ml-auto">
                    <b-nav-item right class="no-arrow notification-menu" id="notifyPopoverWrap">
                        <div class="" id="notificationPopover"><i><img src="../../assets/images/bell-ic.svg" alt="Notification Icon"></i>
                            <b-badge>2</b-badge>
                        </div>
                    </b-nav-item>
                    <b-nav-item-dropdown right class="profile-menu">
						<template slot="button-content">
							<i><img src="../../assets/images/user-img.png" alt="User Image"></i><em>Evan Donohue</em>
						</template>
                        <b-dropdown-item href="#">Dashboard</b-dropdown-item>
                        <b-dropdown-item href="#">My Account</b-dropdown-item>
                        <b-dropdown-item href="#">Help Center</b-dropdown-item>
                        <b-dropdown-item href="#">Logout</b-dropdown-item>
                    </b-nav-item-dropdown>
                </b-nav>
                <b-popover target="notificationPopover" triggers="click" placement="topleft"
                    container="notifyPopoverWrap" @show="onPopoverShow" ref="notficationPopover">
					<template slot="title">
                        <b-button class="btn-setting" title="Setting" @click="showsetting">
							<img src="../../assets/images/settings-ic.svg" alt="Setting icon">
						</b-button>
						<span class="title">Notification</span>
                        <b-button class="btn-clear" title="Clear All" @click="showclearitem">Clear All</b-button>
                    </template>
                    <div class="notification-details" data-simplebar>
                        <b-list-group>
                            <b-list-group-item href="#" class="unread-item"><i><img
                                        src="../../assets/images/notification/user.png" alt></i>
                                <p>John Doe: Recommend this mission - <b>Grow Trees</b></p><span class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="read-item"><i><img
                                        src="../../assets/images/notification/circle-plus.png" alt></i>
                                <p>John Doe: Recommend this mission - <b>Save the Children</b></p><span
                                    class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="read-item"><i><img
                                        src="../../assets/images/notification/circle-plus.png" alt></i>
                                <p>New Mission - <b>Save the world</b></p><span class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="unread-item"><i><img
                                        src="../../assets/images/notification/warning.png" alt></i>
                                <p>New Message - <b>Message title goes here</b></p><span class="status"></span>
                            </b-list-group-item>
                        </b-list-group>
                        <div class="slot-title"><span>Yesterday</span></div>
                        <b-list-group>
                            <b-list-group-item href="#" class="unread-item"><i><img
                                        src="../../assets/images/notification/warning.png" alt></i>
                                <p>Volunteering hours <b>submitted the 17/05/2019 approved</b></p><span
                                    class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="unread-item"><i><img
                                        src="../../assets/images/notification/warning.png" alt></i>
                                <p>Volunteering hours <b>submitted the 17/05/2019 approved</b></p><span
                                    class="status"></span>
                            </b-list-group-item>
                            <b-list-group-item href="#" class="unread-item">
								<i><img src="../../assets/images/notification/warning.png" alt></i>
                                <p>Volunteering hours <b>submitted the 17/05/2019 approved</b></p><span
                                    class="status"></span>
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
                            <div class="setting-bar"><span>Get a notification for</span></div>
                            <b-list-group data-simplebar>
                                <b-list-group-item>
                                    <b-form-checkbox id="checkbox-1" value="accepted">Recommended Missions
                                    </b-form-checkbox>
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b-form-checkbox id="checkbox-2" value="accepted">Volunteering Hours
                                    </b-form-checkbox>
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b-form-checkbox id="checkbox-3" value="accepted">Volunteering Goals
                                    </b-form-checkbox>
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b-form-checkbox id="checkbox-4" value="accepted">My Comments</b-form-checkbox>
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b-form-checkbox id="checkbox-5" value="accepted">My Stories</b-form-checkbox>
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b-form-checkbox id="checkbox-6" value="accepted">New Stories</b-form-checkbox>
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b-form-checkbox id="checkbox-7" value="accepted">New Missions</b-form-checkbox>
                                </b-list-group-item>
                                <b-list-group-item>
                                    <b-form-checkbox id="checkbox-8" value="accepted">New Messages</b-form-checkbox>
                                </b-list-group-item>
                            </b-list-group>
                        </div>
                        <div class="setting-footer">
                            <b-button class="btn-bordersecondary" title="Save">Save</b-button>
                            <b-button class="btn-borderprimary" title="Cancel" @click="cancelsetting()">Cancel
                            </b-button>
                        </div>
                    </div>
                </b-popover>
            </b-container>
        </b-navbar>
    </div>
</template>
<script>
    export default {
        components: {},
        name: "topheader",
        data() {
            return {
                bgImages: [require("@/assets/images/logo.png"),
                    require("@/assets/images/optimy-logo.png")
                ]
            };
		},
		mounted(){
				// var mob_nav_list = document
			var hasmenu_li = document.querySelectorAll(".menu-wrap li"); //array of parentchlid
			for (var i = 0; i < hasmenu_li.length; ++i) {
			var anchor_val = hasmenu_li[i].firstChild; // anchor tag variable

			//anchor tag click function
			anchor_val.addEventListener("click", function(e) {
				if (window.screen.width < 992) {
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
			var remove_active = document.querySelectorAll(".btn-back, .navbar-toggler");
			remove_active.forEach(function(e){
			e.addEventListener("click", function() {
				if (window.screen.width < 992) {
					for (var i = 0; i < hasmenu_li.length; ++i) {
						hasmenu_li[i].classList.remove("active");
					}
				}
			});
			});
		},
        methods: {
            onPopoverShow() {
                this.$refs.notficationPopover._toolpop.getTipElement().classList.add("notification-popover");
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
			},
			openMenu(){
				var body = document.querySelectorAll("body, html");
				body.forEach(function(e) {
					e.classList.toggle("open-nav");
				});

			}
        }
    };
</script>
<style lang="scss">
    .top-header {
        padding: 6px 0;
        border-bottom: 1px solid $border-color;
        .navbar {
            padding: 0;
            .container {
                padding: 0 15px;
            }
            .navbar-toggler {
				z-index:999;
                @include md-max {
                    border: none;
                    padding: 0;
				}
				.navbar-toggler-icon{
					background:none;
					height:2px;
					width:30px;	
					position:relative;
					background:$black-primary;
					&:after{
						position:absolute;
						content:'';
						width:100%;
						height:100%;
						top:-10px;
						left:0;
						background:$black-primary;
						transition:all 0.3s;
					}
					&:before{
						position:absolute;
						content:'';
						width:100%;
						height:100%;
						bottom:-10px;
						left:0;
						background:$black-primary;
						transition:all 0.3s;
					}
				}
            }
            .nav {
                @include md-max {
                    margin: 0 !important;
                }
            }
        }

        .navbar-brand {
            display: inline-block;
            width: 100px;
            height: 50px;
            margin: 0;
            background-repeat: no-repeat;
            background-position: center center;
            background-size: contain;
            padding: 0 !important;

            @include md-max {
                margin: 0 auto;
            }

            img {
                width: 100%;
                max-width: 100%;
                height: 100%;
                object-fit: contain;
            }
        }

        ul {
            li {
                margin: 0;
                display: flex;
                align-items: center;
                position: relative;

                a {
                    font-size: 16px;
                    line-height: 20px;
                    position: relative;

                    &:after {
                        position: absolute;
                        content: "";
                        background: url("../../assets/images/down-arrow.svg") no-repeat;
                        border: none !important;
                        background-size: 11px;
                        width: 11px;
                        height: 9px;
                        right: -17px;
                        top: 50%;
                        @include transformY(-50%);
                        margin: 0;
                    }
                }

                &.no-dropdown {
                    a {
                        &:after {
                            display: none;
                        }
                    }
                }

                ul {
                    &:not(.sub-dropdown) {
                        li {
                            a {
                                &:after {
                                    display: none;
                                }
                            }
                        }
                    }
                }
            }

            .dropdown-menu {
                a {
                    font-size: 14px;
                    display: block;
                    padding: 8px 15px;
                    background: transparent;
                    width: 100%;

                    &:active {
                        color: $black-primary;
                    }
                }

                li {
                    &:hover {
                        >a {
                            background: $gray-bg;
                        }
                    }
                }
            }

            &.sub-dropdown {
                .has-submenu {
                    >a {
                        padding-right: 30px;
                    }
                }
            }
        }

        .notification-menu {
            margin-right: 44px;

            @include sm-max {
                margin-right: 16px;
            }

            .nav-link {
                padding: 0 !important;

                i {
                    display: inline-block;
                    width: 29px;
                    height: 25px;
                    position: relative;
                    top: 3px;
                }

                .badge-secondary {
                    position: absolute;
                    top: -6px;
                    right: -4px;
                    padding: 3px 6px;
                    color: $white;
                    font-weight: 300;
                    background-color: $primary;
                    border-radius: 50%;
                }
            }

            >a {
                &:after {
                    display: none;
                }
            }
        }

        .profile-menu {
            padding-right: 18px;
            margin-right: 0;

            >.nav-link {
                padding: 0 !important;
            }

            &.show {
                .nav-link {
                    &:after {
                        border-color: transparent $black $black transparent;
                    }
                }
            }

            ul {
                li {
                    margin: 0;
                }
            }

            .nav-link {
                &:hover {
                    &:after {
                        border-color: transparent $black $black transparent;
                    }
                }

                i {
                    display: inline-block;
                    overflow: hidden;
                    width: 40px;
                    height: 40px;
                    margin: 0 10px 0 0;
                    vertical-align: middle;
                    border-radius: 50%;

                    @include sm-max {
                        width: 30px;
                        height: 30px;
					}
					img{
						object-fit: cover;
					}
                }

                em {
                    display: inline-block;
                    color: $black-primary;
                    vertical-align: middle;
                    font-size: 15px;

                    @include md-max {
                        display: none;
                    }
                }
            }

            .dropdown-menu {
                min-width: 137px;
                padding: 5px 0;
                margin: 4px 0 0;
                border: 1px solid $border-color;
                @extend .navdropdown_shadow;
                border-radius: 3px;
            }
        }
    }

    .menu-wrap {
        padding-left: 60px;

        >ul {
            display: flex;
			list-style: none;

            >li {
                margin: 0;
                padding: 20px 24px;
                position: relative;
                cursor: pointer;

                &:hover {
                    .dropdown-menu {
                        display: block;
                    }
                }

                .dropdown-menu {
                    top: 90%;
                    border: 1px solid $border-color;
                    width: 195px;
                    padding: 5px 0;
                    margin-top: 0;
                    left: 25px;
                    @extend .navdropdown_shadow;
                    border-radius: 3px;

                    li {
                        margin: 0;

                        a {
                            &:after {
                                @include transform(translateY(-50%) rotate(-90deg));
                                right: 14px;
                            }
                        }

                        &:hover {
                            .subdropdown-menu {
                                display: block;
                            }
                        }
                    }
                }

                .subdropdown-menu {
                    position: absolute;
                    background: #fff;
                    z-index: 1;
                    left: 100%;
                    border: 1px solid $border-color;
                    display: none;
                    top: -6px;
                    width: 165px;
                    padding: 5px 0;
                    border-radius: 3px;
                    @extend .navdropdown_shadow;

                    li {
                        a {
                            &::after {
                                display: none;
                            }
                        }
                    }
                }
            }
		}
		.btn-back{
			position: absolute;
			top: 50px;
			left: 0;
			right: 0;
			border-bottom: 1px solid $border-color;
			background:$black-primary;
			z-index: 4;
			display:none;
			@include md-max{
				display:block;
			}
		}
    }

    .popover {
        overflow: hidden;

        &.notification-popover {
            margin: 0;
            border-radius: 3px;
            border-color: $border-color;
            @extend .navdropdown_shadow;
            width: 100%;
            max-width: 100%;
            min-width: 450px;
            top: 10px !important;
            left: -4px !important;

            @include sm-max {
                min-width: 290px;
            }

            .arrow {
                display: none;
            }

            .popover-header {
                background: transparent;
                padding: 12px 15px;

                >div {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }

                .btn {
                    background: transparent;
                    color: $black-primary;
                    border: none;
                    height: auto;
                    padding: 0;

                    &:hover,
                    &:focus {
                        background: transparent;
                        color: $black-primary;
                    }

                    &.btn-setting {
                        width: 18px;
                        height: 18px;

                        img {
                            width: 100%;
                            height: 100%;
                            vertical-align: top;
                        }
                    }

                    &.btn-clear {
                        font-size: 13px;
                        line-height: 17px;
                        color: $gray-primary;
                    }
                }

                .title {
                    font-size: 18px;
                    line-height: 22px;
                    margin-left: 30px;
                }
            }
        }

        .popover-body {
            padding: 0;
            min-height: 340px;

            >div {
                min-height: inherit;
                position: relative;
            }

            &.clear-item {
                .notification-details {
                    display: none;
                }

                .notification-clear {
                    display: flex;
                }
            }

            .notification-details {
                max-height: 340px;

                .list-group {
                    flex-direction: column;

                    .list-group-item {
                        position: relative;
                        padding: 20px 40px 20px 56px;
                        display: flex;
                        align-items: center;
                        border-bottom: 1px solid $border-color;
                        border-radius: 0;
                        border-left: 3px solid transparent;

                        &:hover,
                        &:focus {
                            background: transparent;
                        }

                        &:last-child {
                            border: none;
                        }

                        i {
                            position: absolute;
                            display: inline-block;
                            width: 30px;
                            height: 30px;
                            left: 15px;
                        }

                        p {
                            margin: 0;
                            color: $gray-primary;
                            font-size: 14px;
                            line-height: 20px;
                            max-width: 355px;
                            text-overflow: ellipsis;
                            overflow: hidden;
                            white-space: nowrap;

                            b {
                                font-weight: 400;
                                color: $black-primary;
                            }
                        }

                        .status {
                            position: absolute;
                            right: 15px;
                            width: 12px;
                            height: 12px;
                            background: $primary;
                            border-radius: 50%;

                        }

                        &.unread-item {
                            border-left: 3px solid $primary;
                        }

                        &.read-item {
                            .status {
                                background: $gray-primary;

                                &:after {
                                    position: absolute;
                                    content: "";
                                    background: url("../../assets/images/white-check-ic.svg") no-repeat center center;
                                    background-size: 8px;
                                    height: 12px;
                                    width: 12px;
                                }
                            }
                        }
                    }
                }

                .slot-title {
                    text-align: center;
                    border-top: 1px solid $control-border;
                    border-bottom: 1px solid $control-border;
                    padding: 5px 15px;
                    color: $gray-primary;
                    background: $border-color;
                }
            }

            .notification-clear {
                text-align: center;
                min-height: inherit;
                display: none;
                align-items: center;
                justify-content: center;

                i {
                    width: 114px;
                    height: 125px;
                    display: block;
                    margin: 0 auto 9px;
                }

                p {
                    margin-bottom: 0;
                    font-size: 18px;
                    line-height: 22px;
                    color: $gray-primary;
                    margin-bottom: 50px;
                }
            }

            .notification-setting {
                position: absolute;
                opacity: 0;
                visibility: hidden;
                right: -100%;
                @include transition(all 0.3s);
                border: 1px solid $border-color;
                top: -48px;
                width: 0;
                background: $white;
                z-index: 5;

                &.show-setting {
                    opacity: 1;
                    visibility: visible;
                    right: 0;
                    width: 400px;
                }

                .setting-header {
                    text-align: center;
                    font-size: 18px;
                    line-height: 22px;
                    padding: 13px 15px;
                    margin: 0;
                }

                .setting-body {
                    .setting-bar {
                        padding: 6px 15px;
                        background: $border-color;
                        font-size: 14px;
                        color: $gray-primary;

                        p {
                            color: $gray-primary;
                        }
                    }

                    .list-group {
                        padding: 6px 0;
                    }
                }

                .setting-footer {
                    display: flex;
                    justify-content: center;
                    padding: 19px 0;
                    border-top: 1px solid $border-color;

                    .btn {
                        min-height: 36px;
                        min-width: 96px;
                        margin: 0 5px;
                        padding: 4px 10px;
                    }
                }

                .list-group {
                    flex-direction: column;
                    max-height: 230px;
                }

                .custom-checkbox {
                    padding: 7px 30px 8px 15px;

                    .custom-control-label {
                        line-height: 18px;
                        &:after {
                            left: auto;
                            right: -15px;
                        }

                        &:before {
                            left: auto;
                            right: -15px;
                        }
                    }
                }
            }
        }
    }
    @include md-max{
        .menu-wrap{
			height: 100%;
			width: 0;
			top: 0;
			left: 0;
			bottom:0;
			background-color: $black-primary;
			position: fixed;
			z-index: 3;
			overflow-x: hidden;
			@include transition(all 0.6s);
			padding:85px 0 20px;
			    > ul{
						flex-direction:column;
						padding:0;
						overflow-y: auto;
						position:relative;
						max-height:100%;
						li{
							display:block;
							padding:0;
							margin:0;
							position:static;
							border-bottom:1px solid $gray-primary;
							&:first-child{
								border-top:1px solid $gray-primary;
							}
							&.active{
								.dropdown-menu-outer{
									transform: translate3d(0, 0, 0);
								}
							}
							.dropdown-menu-outer{
								max-height:100%;
								overflow:hidden;
								position: fixed;
								width: 300px;
								z-index: 5;
								padding: 100px 0 20px;
								overflow: hidden!important;
								bottom: 0;
								top: 0;
								left: 0;
								height: 100%;
								background: $black-primary;
								transform: translate3d(-100%, 0, 0);
								@include transition(all 0.5s);
								padding:101px 0 20px;
								overflow-x:hidden;
							}
							.dropdown-menu{
								border: none;
								box-shadow: none;
								background:transparent;
								width:100%;
								left:auto;
								position:relative;
								top:auto;
								overflow-y: auto;
								max-height: 100%;
								margin: 0;
								bottom: auto;
								height: 100%;
								padding: 0;
								display:block;
								a{
									padding:15px 15px 15px 40px;
								}
							}
							a{
							color:$white;
							display:block;
							padding:15px 15px 15px 40px;
							&:after{
								transform: translateY(-50%) rotate(90deg);
								-webkit-transform: translateY(-50%) rotate(90deg);
								-ms-transform: translateY(-50%) rotate(90deg);
								-moz-transform: translateY(-50%) rotate(90deg);
								left:15px;
								right:auto;
								background: url("../../assets/images/down-arrow-white.svg") no-repeat;
								background-size: 12px;
								width: 12px;
								height: 10px;
							}
						}
						}
						a{
							color:$white;
							display:block;
						}
				}
		}
		.open-nav{
			.menu-wrap{
				width: 300px;
			}
			.navbar-toggler-icon{
				background:transparent!important;
				&:after{
				transform:rotate(50deg) translate(5px , 9px);
				background:$white!important;
				}
				&:before{
				transform:rotate(-50deg) translate(5px , -9px);
				background:$white!important;
				}
			}
		}
    }

</style>