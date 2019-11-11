<template>
    <div class="cards-wrapper" v-if="items.length > 0">
        <div class="card-listing">
            <div class="card-outer" v-for="(mission, index) in items" :key=index>
                <b-card no-body>
                    <b-card-header>
                        <div class="header-img-block">
                            <b-alert show class="alert card-alert alert-success" v-if="getAppliedStatus(mission)">{{languageData.label.applied}}</b-alert>
                            <b-alert show class="alert card-alert alert-warning"  v-if="getClosedStatus(mission)">{{languageData.label.closed}}</b-alert>
                            <div v-if="checkDefaultMediaFormat(mission.default_media_type)" class="group-img"
                                :style="{backgroundImage: 'url('+getMediaPath(mission.default_media_path)+')'}">
                                <img src="mission.default_media_path" alt="mission.default_media_path">
                            </div>
                            <div v-else class="group-img"
                                :style="{backgroundImage: 'url('+youtubeThumbImage(mission.default_media_path)+')'}">
                            </div>
                            <div class="location">
                                <i>
                                    <img :src="$store.state.imagePath+'/assets/images/location.svg'"
                                        :alt="languageData.label.location">
                                </i>{{mission.city_name}}
                            </div>
                            <div class="btn-ic-wrap">
                                <b-button v-bind:class="{ 
                                        'favourite-icon' : true,
                                        active : mission.is_favourite == 1
                                    }" v-b-tooltip.hover
                                    :title="mission.is_favourite == 1 ?  languageData.label.remove_from_favourite :languageData.label.add_to_favourite"
                                    @click="favoriteMission(mission.mission_id)">
                                    <i class="normal-img">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 21" width="24" height="21">
                                            <g id="Main Content">
                                                <g id="1">
                                                    <g id="Image content">
                                                        <path id="Forma 1"
                                                            d="M22.1 2.86C20.9 1.66 19.3 1 17.59 1C15.89 1 14.29 1.66 13.08 2.86L12.49 3.45L11.89 2.86C10.69 1.66 9.08 1 7.38 1C5.67 1 4.07 1.66 2.87 2.86C0.38 5.34 0.38 9.36 2.87 11.84L11.78 20.71C11.93 20.86 12.11 20.95 12.3 20.98C12.36 20.99 12.43 21 12.49 21C12.74 21 13 20.9 13.19 20.71L22.1 11.84C24.59 9.36 24.59 5.34 22.1 2.86ZM20.71 10.45L12.49 18.64L4.26 10.45C2.54 8.74 2.54 5.96 4.26 4.25C5.09 3.42 6.2 2.96 7.38 2.96C8.56 2.96 9.66 3.42 10.5 4.25L11.79 5.53C12.16 5.9 12.81 5.9 13.18 5.53L14.47 4.25C15.31 3.42 16.41 2.96 17.59 2.96C18.77 2.96 19.88 3.42 20.71 4.25C22.43 5.96 22.43 8.74 20.71 10.45Z" />
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="hover-img">
                                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 492.7 426.8" style="enable-background:new 0 0 492.7 426.8;"
                                            xml:space="preserve">
                                            <g>
                                                <g id="Icons_18_">
                                                    <path d="M492.7,133.1C492.7,59.6,433.1,0,359.7,0c-48,0-89.9,25.5-113.3,63.6C222.9,25.5,181,0,133,0
                                                    C59.6,0,0,59.6,0,133.1c0,40,17.7,75.8,45.7,100.2l188.5,188.6c3.2,3.2,7.6,5,12.1,5s8.9-1.8,12.1-5L447,233.2
                                                    C475,208.9,492.7,173.1,492.7,133.1z" />
                                                </g>
                                            </g>
                                        </svg>
                                    </i>
                                </b-button>
                                <b-button class="add-icon" v-if="isInviteCollegueDisplay"
                                    @click="handleModal(mission.mission_id)" v-b-tooltip.hover
                                    :title="languageData.label.invite_colleague">
                                    <img :src="$store.state.imagePath+'/assets/images/add-group-ic.svg'"
                                        :alt="languageData.label.invite_colleague">
                                </b-button>
                            </div>
                        </div>
                        <div class="group-category" v-if="mission.mission_theme != null && isThemeSet"><span
                                class="category-text">{{getThemeTitle(mission.mission_theme.translations)}}</span>
                        </div>
                    </b-card-header>

                    <b-card-body>
                        <div class="content-block">
                            <b-link target="_blank" :to="'/mission-detail/' + mission.mission_id"
                                class="card-title mb-2">
                                {{mission.title | substring(40)}}
                            </b-link>
                            <b-card-text>
                                {{mission.short_description | substring(150)}}
                            </b-card-text>
                        </div>
                        <div class="group-wrap">
                            <div class="rating-with-button">
                                <div class="group-ratings">
                                    <span class="group-name">{{mission.organisation_name}}</span>
                                    <span class="ratings" v-if="isStarRatingDisplay">
                                        <star-rating v-bind:increment="0.5" v-bind:max-rating="5"
                                            inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="23"
                                            :rating="mission.mission_rating_count" :read-only="true">
                                        </star-rating>
                                    </span>
                                </div>
                                <div class="bottom-block">
                                    <b-link v-if="mission.set_view_detail == 0"
                                        @click="applyForMission(mission.mission_id)">
                                        <b-button class="btn-bordersecondary icon-btn">
                                            <span>{{ languageData.label.apply }}</span>
                                            <i>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16" width="19"
                                                    height="15">
                                                    <g id="Main Content">
                                                        <g id="1">
                                                            <g id="Button">
                                                                <path id="Forma 1 copy 12" class="shp0"
                                                                    d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z" />
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </i>
                                        </b-button>
                                    </b-link>
                                    <b-link v-if="mission.set_view_detail == 1"
                                        :to="'/mission-detail/' + mission.mission_id">
                                        <b-button class="btn-bordersecondary icon-btn">
                                            <span>{{ languageData.label.view_detail }}</span>
                                            <i>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16" width="19"
                                                    height="15">
                                                    <g id="Main Content">
                                                        <g id="1">
                                                            <g id="Button">
                                                                <path id="Forma 1 copy 12" class="shp0"
                                                                    d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z" />
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </i>
                                        </b-button>
                                    </b-link>
                                </div>
                            </div>
                            <div class="group-details">
                                <div class="top-strip-wrap">
                                    <div class="top-strip">
                                        <span>
                                            <!-- Mission type time -->
                                            <template v-if="checkMissionTypeTime(mission.mission_type)">
                                                <template v-if="mission.end_date !== null">
                                                    {{ languageData.label.from }}
                                                    {{mission.start_date | formatDate }}
                                                    {{ languageData.label.until}}
                                                    {{ mission.end_date | formatDate }}
                                                </template>
                                                <template v-else>
                                                    {{ languageData.label.on_going_opportunities }}
                                                </template>
                                            </template>
                                            <!-- Mission type goal -->
                                            <template v-else>
                                                {{mission.objective}}
                                            </template>
                                        </span>
                                    </div>
                                </div>
                                <div class="group-details-inner">
                                    <template v-if="mission.total_seats != 0 && mission.total_seats !== null">
                                        <div class="detail-column info-block">
                                            <i class="icon-wrap">
                                                <img :src="$store.state.imagePath+'/assets/images/user-icon.svg'"
                                                    alt="user">
                                            </i>
                                            <div class="text-wrap">
                                                <span class="title-text mb-1">{{mission.seats_left}}</span>
                                                <span class="subtitle-text">{{ languageData.label.seats_left }}</span>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="detail-column info-block">
                                            <i class="icon-wrap">
                                                <img :src="$store.state.imagePath+'/assets/images/user-icon1.svg'"
                                                    alt="user">
                                            </i>
                                            <div class="text-wrap">
                                                <span
                                                    class="title-text mb-1">{{mission.mission_application_count}}</span>
                                                <span
                                                    class="subtitle-text">{{ languageData.label.already_volunteered }}</span>
                                            </div>
                                        </div>
                                    </template>

                                    <template v-if="mission.application_deadline != null ||
                                        checkMissionTypeTime(mission.mission_type)
                                        ">
                                        <div class="detail-column info-block"
                                            v-if="mission.application_deadline != null">
                                            <i class="icon-wrap">
                                                <img :src="$store.state.imagePath+'/assets/images/clock.svg'"
                                                    alt="user">
                                            </i>
                                            <div class="text-wrap">
                                                <span
                                                    class="title-text mb-1">{{mission.application_deadline | formatDate}}</span>
                                                <span class="subtitle-text">{{ languageData.label.deadline }}</span>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="detail-column progress-block">
                                            <i class="icon-wrap">
                                                <img :src="$store.state.imagePath+'/assets/images/target-ic.svg'"
                                                    alt="user">
                                            </i>
                                            <div class="text-wrap">
                                                <b-progress :value="mission.achieved_goal | filterGoal" :max="mission.goal_objective"
                                                    class="mb-2"></b-progress>
                                                <span class="subtitle-text">
                                                    {{mission.achieved_goal}} {{ languageData.label.achieved}}
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </b-card-body>
                </b-card>
            </div>
        </div>
        <b-modal  @hidden="hideModal" ref="userDetailModal" :modal-class="myclass" size="lg" hide-footer>
            <template slot="modal-header" slot-scope="{ close }">
                <i class="close" @click="close()" v-b-tooltip.hover :title="languageData.label.close"></i>
                <h5 class="modal-title">{{languageData.label.search_user}}</h5>
            </template>
            <b-alert show :variant="classVariant" dismissible v-model="showErrorDiv">{{ message }}</b-alert>
            <div class="autocomplete-control">
                <div class="autosuggest-container">
                    <VueAutosuggest ref="autosuggest" name="user" v-model="query" :suggestions="filteredOptions"
                        @input="onInputChange" @selected="onSelected"
                        :get-suggestion-value="getSuggestionValue" :input-props="{
                        id:'autosuggest__input', 
                        placeholder:autoSuggestPlaceholder,
                        ref:'inputAutoSuggest'
                        }">
                        <div slot-scope="{suggestion}">
                            <img :src="suggestion.item.avatar" />
                            <div>
                                {{suggestion.item.first_name}} {{suggestion.item.last_name}} 
                            </div>
                        </div>
                    </VueAutosuggest>
                </div>
            </div>
            <b-form>
                <div class="btn-wrap">
                    <b-button @click="$refs.userDetailModal.hide()" class="btn-borderprimary">
                        {{ languageData.label.close }}</b-button>
                    <b-button class="btn-bordersecondary" @click="inviteColleagues" ref="autosuggestSubmit"
                        v-bind:disabled="submitDisable">
                        {{ languageData.label.submit }}</b-button>
                </div>
            </b-form>
        </b-modal>
    </div>
    <div class="no-data-found" v-else>
        <h2 class="text-center">{{noRecordFound()}}</h2>
        <div class="btn-wrap" v-if="isSubmitNewMissionSet" @click="submitNewMission">
            <b-button  class="btn-bordersecondary icon-btn">
                <span>{{ languageData.label.submit_new_mission }}</span>
                <i>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16" width="19" height="15">
                        <g id="Main Content">
                            <g id="1">
                                <g id="Button">
                                    <path id="Forma 1 copy 12" class="shp0"
                                        d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z" />
                                </g>
                            </g>
                        </g>
                    </svg>
                </i>
            </b-button>
        </div>
    </div>
</template>
<script>
    import store from '../store';
    import constants from '../constant';
    import StarRating from 'vue-star-rating';
    import moment from 'moment';
    import {
        favoriteMission,
        inviteColleague,
        applyMission
    } from "../services/service";
    import {
        VueAutosuggest
    } from 'vue-autosuggest';

    export default {
        name: "MissionListView",
        props: {
            items: Array,
            userList: Array
        },
        components: {
            StarRating,
            VueAutosuggest
        },
        data() {
            return {
                query: "",
                selected: "",
                myclass: ["userdetail-modal"],
                currentMissionId: 0,
                invitedUserId: 0,
                showErrorDiv: false,
                message: null,
                classVariant: "success",
                autoSuggestPlaceholder: '',
                submitDisable: true,
                languageData: [],
                isInviteCollegueDisplay: true,
                isStarRatingDisplay: true,
                isQuickAccessSet: true,
                isSubmitNewMissionSet: true,
                isThemeSet: true,
                submitNewMissionUrl:''
            };
        },
        computed: {
            filteredOptions() {
                if (this.userList) {
                    return [{
                        data: this.userList.filter(option => {
                            let firstName = option.first_name.toLowerCase();
                            let lastName = option.last_name.toLowerCase();
                            let email = option.email.toLowerCase();
                            let searchString = firstName + '' + lastName + '' + email;
                            return searchString.indexOf(this.query.toLowerCase()) > -1;
                        })
                    }];
                }
            }
        },
        methods: {
            hideModal() {
				this.autoSuggestPlaceholder = ""
				this.submitDisable  = true
				this.invitedUserId  = ""
				this.query = ""
				this.selected = ""
			},
            noRecordFound() {
                let defaultLang = (store.state.defaultLanguage).toLowerCase();
                if (JSON.parse(store.state.missionNotFoundText) != "") {
                    let missionNotFoundArray = JSON.parse(store.state.missionNotFoundText);
                    let data = missionNotFoundArray.filter((item) => {
                        if (item.lang == defaultLang) {
                            return item
                        }
                    })

                    if (data[0] && data[0].message) {
                        return data[0].message;
                    } else {
                        return this.languageData.label.no_record_found;
                    }

                } else {
                    return this.languageData.label.no_record_found;
                }
            },
            // Get theme title
            getThemeTitle(translations) {
                if (translations) {
                    let filteredObj = translations.filter( (item, i) => {
                        if (item.lang === store.state.defaultLanguage.toLowerCase()) {
                            return translations[i].title;
                        }
                    });
                    if (filteredObj[0]) {
                        return filteredObj[0].title;
                    }
                }
            },
            getMediaPath(mediaPath) {
				if(mediaPath != '') {
					return mediaPath;
				} else {
					return store.state.imagePath+'/assets/images/'+constants.MISSION_DEFAULT_PLACEHOLDER;
				}
			},
            // Is default media is video or not
            checkDefaultMediaFormat(mediaType) {
                return mediaType != constants.YOUTUBE_VIDEO_FORMAT
            },
            // Check mission type
            checkMissionTypeTime(missionType) {
                return missionType == constants.MISSION_TYPE_TIME
            },
            // Get Youtube Thumb images
            youtubeThumbImage(videoPath) {
                let data = videoPath.split("=");
                return "https://img.youtube.com/vi/" + data.slice(-1)[0] + "/mqdefault.jpg";
            },
            // Add mission to favorite
            favoriteMission(missionId) {
                let missionData = {
                    mission_id: ''
                };
                missionData.mission_id = missionId;
                favoriteMission(missionData).then(response => {
                    if (response.error == true) {
                        this.makeToast("danger", response.message);
                    } else {
                        this.makeToast("success", response.message);
                        this.$emit("getMissions", "removeLoader");
                    }
                });

            },
            onInputChange() {
                this.submitDisable = true;
            },
            // For selected user id.
            onSelected(item) {
                if(item) {
                    this.selected = item.item;
                    this.submitDisable = false;
                    this.invitedUserId = item.item.user_id;
                }
            },
            //This is what the <input/> value is set to when you are selecting a suggestion.
            getSuggestionValue(suggestion) {
                let firstName = suggestion.item.first_name;
                let lastName = suggestion.item.last_name;
                return firstName + ' ' + lastName;
            },
            // Open auto suggest modal
            handleModal(missionId) {
                this.autoSuggestPlaceholder = this.languageData.label.search_user
                this.showErrorDiv = false;
                this.message = null;
                this.$refs.userDetailModal.show();
                this.currentMission = missionId;
                setTimeout(() => {
					this.$refs.autosuggest.$refs.inputAutoSuggest.focus();
					var input = document.getElementById("autosuggest__input");
					input.addEventListener("keyup", (event) => {
						if (event.keyCode === 13 && !this.submitDisable) {
							event.preventDefault();
							this.inviteColleagues()
						}
					});
				}, 100);
            },
            // invite collegues api call
            inviteColleagues() {
                let inviteData = {};
                inviteData.mission_id = this.currentMission;
                inviteData.to_user_id = this.invitedUserId;
                inviteColleague(inviteData).then(response => {
                    this.submitDisable = true;
                    if (response.error == true) {
                        this.classVariant = "danger";
                        this.message = response.message;
                        this.$refs.autosuggest.$data.currentIndex = null;
                        this.$refs.autosuggest.$data.internalValue = '';
                        this.showErrorDiv = true;
                    } else {
                        this.query = "";
                        this.selected = "";
                        this.currentMissionId = 0;
                        this.invitedUserId = 0;
                        this.$refs.autosuggest.$data.currentIndex = null;
                        this.$refs.autosuggest.$data.internalValue = '';
                        this.classVariant = "success";
                        this.message = response.message;
                        this.showErrorDiv = true;
                    }
                })
            },
            // Apply for mission
            applyForMission(missionId) {
                let missionData = {};
                missionData.mission_id = missionId;
                missionData.availability_id = 1;
                applyMission(missionData).then(response => {
                    if (response.error == true) {
                        this.makeToast("danger", response.message);
                    } else {
                        this.makeToast("success", response.message);
                        this.$emit("getMissions");
                    }
                })
            },
            makeToast(variant = null, message) {
                this.$bvToast.toast(message, {
                    variant: variant,
                    solid: true,
                    autoHideDelay: 1000
                })
            },
            getAppliedStatus(missionDetail) {
                let currentDate = moment().format("YYYY-MM-DD HH::mm:ss");
                let missionEndDate = moment(missionDetail.end_date).format("YYYY-MM-DD HH::mm:ss");
                let checkEndDateExist = true;
                if(missionDetail.end_date != '' && missionDetail.end_date != null) {
                    if(currentDate > missionEndDate) {
                        checkEndDateExist = false
                    }
                }
                if(missionDetail.user_application_count == 1 && checkEndDateExist) {
                    return true;
                }
            },
            getClosedStatus(missionDetail) {
                let currentDate = moment().format("YYYY-MM-DD HH::mm:ss");
                let missionEndDate = moment(missionDetail.end_date).format("YYYY-MM-DD HH::mm:ss");
                if(missionDetail.end_date != '' && missionDetail.end_date != null) {
                    if(currentDate > missionEndDate) {
                        return true;
                    }
                }
            },
            submitNewMission() {
				if(this.submitNewMissionUrl != '') {
					 window.open(this.submitNewMissionUrl, '_self');
				}
			}
        },
        created() {
            this.languageData = JSON.parse(store.state.languageLabel);
            this.isInviteCollegueDisplay = this.settingEnabled(constants.INVITE_COLLEAGUE);
            this.isStarRatingDisplay = this.settingEnabled(constants.MISSION_RATINGS);
            this.isQuickAccessSet = this.settingEnabled(constants.QUICK_ACCESS_FILTERS);
            this.isSubmitNewMissionSet = this.settingEnabled(constants.USER_CAN_SUBMIT_MISSION);
            this.isThemeSet = this.settingEnabled(constants.THEMES_ENABLED);
            this.submitNewMissionUrl = store.state.submitNewMissionUrl
        }
    };
</script>