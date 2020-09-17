<template>
<div class="cards-wrapper" v-if="items.length > 0">
    <div v-bind:class="{'card-grid' : !relatedMission,

}">
        <b-row>
            <b-col lg="4" sm="6" class="card-outer" :id="`gridview-${key}`" data-aos="fade-up" v-for="(mission ,key) in items" :key=key>
                <div class="card-inner">
                    <b-card no-body>
                        <div class="location">
                            <i>
                                <img :src="$store.state.imagePath+'/assets/images/location.svg'" :alt="languageData.label.location">
                            </i>
                            {{mission.city_name}}
                        </div>
                        <b-card-header>
                            <div class="header-img-block" v-bind:class="{'grayed-out' :getClosedStatus(mission)}">
                                <b-alert show class="alert card-alert alert-success" v-if="getAppliedStatus(mission)">
                                    {{languageData.label.applied}}</b-alert>
                                <b-alert show class="alert card-alert alert-warning" v-if="getClosedStatus(mission)">
                                    {{languageData.label.closed}}</b-alert>
                                <div v-if="checkDefaultMediaFormat(mission.default_media_type)" class="group-img" :style="{backgroundImage: 'url('+getMediaPath(mission.default_media_path)+')'}">
                                    <img :src="getMediaPath(mission.default_media_path)" alt="mission.default_media_path">
                                </div>

                                <div v-else class="group-img" :style="{backgroundImage: 'url('+youtubeThumbImage(mission.default_media_path)+')'}">
                                </div>

                            </div>
                            <div class="group-category" v-if="mission.mission_theme != null && isThemeSet"><span class="category-text">{{getThemeTitle(mission.mission_theme.translations)}}</span>
                            </div>
                        </b-card-header>

                        <b-card-body>

                            <div class="content-block">
                                <div class="mission-label-wrap">
                                    <div class="mission-label virtual-label" v-if="mission.is_virtual == 1">
                                        <span>{{languageData.label.virtual_mission}}</span>
                                    </div>
                                </div>
                                <div class="content-inner-block">
                                    <b-link target="_blank" :to="'/mission-detail/' + mission.mission_id" class="card-title mb-2" v-if="checkMissionTypeVolunteering(mission.mission_type)">
                                        {{mission.title | substring(60)}}
                                    </b-link>

                                    <div class="group-ratings" v-if="checkMissionTypeTime(mission.mission_type) || checkMissionTypeGoal(mission.mission_type)">
                                        <star-rating v-if="isStarRatingDisplay" v-bind:increment="0.5" v-bind:max-rating="5" inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18" :rating="mission.mission_rating_count" :read-only="true">
                                        </star-rating>
                                    </div>

                                    <b-card-text>
                                        {{mission.short_description | substring(105)}}
                                    </b-card-text>
                                </div>
                                <div class="event-block has-progress">
                                    <p class="event-name" v-if="mission.organization != null">{{ languageData.label.for }} <span>{{mission.organization.name}}</span></p>

                                    <b-button class="like-btn">
                                        <img v-if="mission.is_favourite == 1" :src="$store.state.imagePath+'/assets/images/heart-fill-icon.svg'" alt="Heart Icon" />
                                    </b-button>
                                </div>

                            </div>
                            <div class="init-hidden">
                                <div class="group-details"><!-- add 'mb-3' class here when no data -->
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
                                                    {{ languageData.label.ongoing }}
                                                </template>
                                            </template>
                                            <!-- Mission type goal -->
                                            <template v-else>
                                                <template v-if="mission.objective != ''">
                                                    {{mission.objective}}
                                                </template>
                                            </template>
                                        </span>
                                    </div>
                                    <div class="content-wrap"><!-- remove this block when no data -->
                                        <template v-if="checkMissionTypeTime(mission.mission_type)">
                                            <div class="group-details-inner">
                                                <template v-if="mission.total_seats != 0 && mission.total_seats !== null">
                                                    <div class="detail-column info-block">
                                                        <i class="icon-wrap">
                                                            <img :src="$store.state.imagePath+'/assets/images/user-icon.svg'" alt="user">

                                                        </i>
                                                        <div class="text-wrap">
                                                            <span class="title-text mb-1">{{mission.seats_left}}</span>
                                                            <span class="subtitle-text">{{ languageData.label.seats_left }}</span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <template v-if="mission.application_deadline != null">
                                                    <div class="detail-column info-block">
                                                        <i class="icon-wrap">
                                                            <img :src="$store.state.imagePath+'/assets/images/clock.svg'" alt="user">
                                                        </i>
                                                        <div class="text-wrap">
                                                            <span class="title-text mb-1">{{mission.application_deadline | formatDate}}</span>
                                                            <span class="subtitle-text">{{ languageData.label.deadline }}</span>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template v-if="checkMissionTypeGoal(mission.mission_type)">
                                            <div class="group-details-inner volunteer-progress">
                                                <div class="detail-column info-block">
                                                    <i class="icon-wrap">
                                                        <img :src="$store.state.imagePath+'/assets/images/user-icon.svg'" alt="user">
                                                    </i>
                                                    <div class="text-wrap">
                                                        <span class="title-text mb-1">{{mission.seats_left}}</span>
                                                        <span class="subtitle-text">{{ languageData.label.seats_left }}</span>
                                                    </div>
                                                </div>
                                                <div v-bind:class="{
                                                    'progress-bar-block': (mission.total_seats == 0 || mission.total_seats === null),
                                                    'detail-column' : true,
                                                    'progress-block' :true
                                                    }">
                                                    <i class="icon-wrap">
                                                        <img :src="$store.state.imagePath+'/assets/images/target-ic.svg'" alt="user">
                                                    </i>
                                                    <div class="text-wrap">
                                                        <b-progress :value="mission.achieved_goal | filterGoal" :max="mission.goal_objective"></b-progress>
                                                        <span class="subtitle-text">
                                                            {{mission.achieved_goal}}
                                                            <em v-if="mission.label_goal_achieved != ''">
                                                                {{ mission.label_goal_achieved }}
                                                            </em>
                                                            <em v-else>{{ languageData.label.achieved }}</em>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="card-action-block">
                                    <div class="left-btn">
                                        <b-link :to="'/mission-detail/' + mission.mission_id" v-if="checkMissionTypeVolunteering(mission.mission_type)" class="btn-bordersecondary icon-btn">
                                            <span>{{ languageData.label.view_detail | substring(36) }}</span>
                                            <i class="icon-wrap">
                                                <svg width="18" height="9" viewBox="0 0 18 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.3571 4.54129C17.3571 4.63504 17.3237 4.7154 17.2567 4.78237L13.3996 8.33817C13.2924 8.43192 13.1752 8.45201 13.048 8.39844C12.9208 8.33817 12.8571 8.24107 12.8571 8.10714V5.85714H0.321429C0.227679 5.85714 0.15067 5.82701 0.0904018 5.76674C0.0301339 5.70647 0 5.62946 0 5.53571V3.60714C0 3.51339 0.0301339 3.43638 0.0904018 3.37612C0.15067 3.31585 0.227679 3.28571 0.321429 3.28571H12.8571V1.03571C12.8571 0.895089 12.9208 0.797991 13.048 0.744419C13.1752 0.690848 13.2924 0.707589 13.3996 0.794642L17.2567 4.31027C17.3237 4.37723 17.3571 4.45424 17.3571 4.54129Z" fill="#ffffff" />
                                                </svg>
                                            </i>
                                        </b-link>
                                    </div>
                                    <div class="social-btn">
                                        <b-button class="icon-btn" v-if="isInviteCollegueDisplay" v-b-tooltip.hover :title="languageData.label.recommend_to_co_worker" @click="handleModal(mission.mission_id)">
                                            <img :src="$store.state.imagePath+'/assets/images/multi-user-icon.svg'" alt="multi user icon">
                                        </b-button>

                                        <b-button v-bind:class="{

                                            'icon-btn' : true,

                                            'fill-heart-btn' : mission.is_favourite == 1

                                            }" v-b-tooltip.hover :title="mission.is_favourite == 1 ?  languageData.label.remove_from_favourite :languageData.label.add_to_favourite" @click="favoriteMission(mission.mission_id)">
                                            <img v-if="mission.is_favourite == 0" :src="$store.state.imagePath+'/assets/images/heart-icon.svg'" alt="heart icon">
                                            <img v-if="mission.is_favourite == 1" :src="$store.state.imagePath+'/assets/images/heart-fill-icon.svg'" alt="heart icon">
                                        </b-button>

                                    </div>
                                </div>
                            </div>
                        </b-card-body>
                    </b-card>
                </div>
            </b-col>
        </b-row>
    </div>
    <b-modal @hidden="hideModal" ref="userDetailModal" :modal-class="myclass" size="lg" hide-footer>
        <template slot="modal-header" slot-scope="{ close }">
            <i class="close" @click="close()" v-b-tooltip.hover :title="languageData.label.close"></i>
            <h5 class="modal-title">{{languageData.label.search_user}}</h5>
        </template>
        <b-alert show :variant="classVariant" dismissible v-model="showErrorDiv">{{ message }}</b-alert>
        <div class="autocomplete-control">
            <div class="autosuggest-container">
                <VueAutosuggest ref="autosuggest" name="user" v-model="query" :suggestions="filteredOptions" @input="onInputChange" @selected="onSelected" :get-suggestion-value="getSuggestionValue" :input-props="{
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
                <b-button class="btn-bordersecondary" @click="inviteColleagues" ref="autosuggestSubmit" v-bind:disabled="submitDisable">
                    {{ languageData.label.submit }}</b-button>
            </div>
        </b-form>
    </b-modal>
</div>
<div class="no-data-found" v-else>
    <h2 class="text-center">{{noRecordFound()}}</h2>
    <div class="btn-wrap" v-if="isSubmitNewMissionSet" @click="submitNewMission">
        <b-button class="btn-bordersecondary icon-btn">
            <span>{{ languageData.label.submit_new_mission }}</span>
            <i>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16" width="19" height="15">
                    <g id="Main Content">
                        <g id="1">
                            <g id="Button">
                                <path id="Forma 1 copy 12" class="shp0" d="M16.49,1.22c-0.31,-0.3 -0.83,-0.3 -1.16,0c-0.31,0.29 -0.31,0.77 0,1.06l5.88,5.44h-19.39c-0.45,0 -0.81,0.33 -0.81,0.75c0,0.42 0.36,0.76 0.81,0.76h19.39l-5.88,5.43c-0.31,0.3 -0.31,0.78 0,1.07c0.32,0.3 0.85,0.3 1.16,0l7.27,-6.73c0.32,-0.29 0.32,-0.77 0,-1.06z" />
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
import {
    favoriteMission,
    inviteColleague,
    applyMission
} from '../services/service';
import {
    VueAutosuggest
} from 'vue-autosuggest';
import moment from 'moment';

export default {
    name: 'MissionGridView',
    components: {
        StarRating,
        VueAutosuggest
    },
    props: {
        items: Array,
        userList: Array,
        relatedMission: Boolean
    },
    data() {
        return {
            query: '',
            selected: '',
            myclass: ['userdetail-modal'],
            currentMissionId: 0,
            invitedUserId: 0,
            showErrorDiv: false,
            message: null,
            classVariant: 'success',
            autoSuggestPlaceholder: '',
            submitDisable: true,
            languageData: [],
            isInviteCollegueDisplay: true,
            isStarRatingDisplay: true,
            isSubmitNewMissionSet: true,
            isThemeSet: true,
            submitNewMissionUrl: "",
            cardHeightAdjIntervalId: null
        };
    },
    computed: {
        filteredOptions() {
            if (this.userList) {
                return [{
                    data: this.userList.filter(option => {
                        const firstName = option.first_name.toLowerCase();
                        const lastName = option.last_name.toLowerCase();
                        const email = option.email.toLowerCase();
                        const searchString = `${firstName}${lastName}${email}`;
                        return searchString.indexOf(this.query.toLowerCase()) > -1;
                    })
                }];
            }
        }
    },
    methods: {
        hideModal() {
            this.autoSuggestPlaceholder = '';
            this.submitDisable = true;
            this.invitedUserId = '';
            this.query = '';
            this.selected = '';
            const cardHeader = document.querySelectorAll(
                ".card-grid .card .card-header"
            );
            const cardBody = document.querySelectorAll(".card-grid .card .card-body");
            cardBody.forEach(function (cardBodyElem) {
                cardBodyElem.style.transform = "translateY(0)";
            });
            cardHeader.forEach(function (cardHeaderElem) {
                cardHeaderElem.style.transform = "translateY(0)";
			});
			
			const cardInner = document.querySelectorAll(".card-grid .card-inner");
			cardInner.forEach(function (cardInnerElem) {
				cardInnerElem.classList.remove("active");
			});
           
        },
        getAppliedStatus(missionDetail) {
            const currentDate = moment().format('YYYY-MM-DD');
            const missionEndDate = moment(missionDetail.end_date).format('YYYY-MM-DD');
            let checkEndDateExist = true;
            if (missionDetail.end_date != '' && missionDetail.end_date != null) {
                if (currentDate > missionEndDate) {
                    checkEndDateExist = false;
                }
            }
            if (missionDetail.user_application_count == 1 && checkEndDateExist) {
                return true;
            }
        },
        getClosedStatus(missionDetail) {
            const currentDate = moment().format('YYYY-MM-DD');
            const missionEndDate = moment(missionDetail.end_date).format('YYYY-MM-DD');
            if (missionDetail.end_date != '' && missionDetail.end_date != null) {
                if (currentDate > missionEndDate) {
                    return true;
                }
            }
        },
        // No record found
        noRecordFound() {
            const defaultLang = store.state.defaultLanguage.toLowerCase();
            if (JSON.parse(store.state.missionNotFoundText) != '') {
                const missionNotFoundArray = JSON.parse(store.state.missionNotFoundText);
                const data = missionNotFoundArray.filter(item => {
                    if (item.lang == defaultLang) {
                        return item;
                    }
                });

                if (data[0] && data[0].message) {
                    return data[0].message;
                } else {
                    return this.languageData.label.no_record_found;
                }
            } else {
                return this.languageData.label.no_record_found;
            }
        },
        handleFav() {
            const btn_active = document.querySelector('.favourite-icon');
            btn_active.classList.toggle('active');
        },
        // get theme title
        getThemeTitle(translations) {
            if (translations) {
                const filteredObj = translations.filter((item, i) => {
                    if (item.lang === store.state.defaultLanguage.toLowerCase()) {
                        return translations[i].title;
                    }
                });

                if (filteredObj[0]) {
                    return filteredObj[0].title;
                } else {
                    let filtereObj = translations.filter((item, i) => {
                        if (item.lang === store.state.defaultTenantLanguage.toLowerCase()) {
                            return translations[i].title;
                        }
                    });

                    if (filtereObj[0]) {
                        return filtereObj[0].title;
                    }
                }
            }
        },
        // Is default media is video or not
        checkDefaultMediaFormat(mediaType) {
            return mediaType != constants.YOUTUBE_VIDEO_FORMAT;
        },
        // Check mission type
        checkMissionTypeTime(missionType) {
            return missionType == constants.MISSION_TYPE_TIME;
        },
        // Check mission type
        checkMissionTypeVolunteering(missionType) {
            if (missionType == constants.MISSION_TYPE_TIME || missionType == constants.MISSION_TYPE_GOAL) {
                return true
            }
            return false;
        },
        // Get Youtube Thumb images
        youtubeThumbImage(videoPath) {
            let data = videoPath.split('=');
            return `https://img.youtube.com/vi/${data.slice(-1)[0]}/mqdefault.jpg`;
        },
        // Add mission to favorite
        favoriteMission(missionId) {
            const bodyTag = document.querySelector("body");
            bodyTag.classList.add("has-favourite");
            const missionData = {
                mission_id: ''
            };
            missionData.mission_id = missionId;
            favoriteMission(missionData).then(response => {
                if (response.error == true) {
                    this.makeToast('danger', response.message);
                } else {
                    this.makeToast('success', response.message);
                    this.$emit('getMissions', 'removeLoader');
                }
            });
        },
        onInputChange() {
            this.submitDisable = true;
        },
        // For selected user id.
        onSelected(item) {
            if (item) {
                this.selected = item.item;
                this.submitDisable = false;
                this.invitedUserId = item.item.user_id;
            }
        },
        // This is what the <input/> value is set to when you are selecting a suggestion.
        getSuggestionValue(suggestion) {
            const firstName = suggestion.item.first_name;
            const lastName = suggestion.item.last_name;
            return `${firstName} ${lastName}`;
        },
        getMediaPath(mediaPath) {
            if (mediaPath != '') {
                return mediaPath;
            } else {
                return `${store.state.imagePath}/assets/images/${constants.MISSION_DEFAULT_PLACEHOLDER}`;
            }
        },
        // Open auto suggest modal
        handleModal(missionId) {
            this.autoSuggestPlaceholder = this.languageData.placeholder.search_user;
            this.showErrorDiv = false;
            this.message = null;
            this.$refs.userDetailModal.show();
            this.currentMission = missionId;
            setTimeout(() => {
                this.$refs.autosuggest.$refs.inputAutoSuggest.focus();
                const input = document.getElementById('autosuggest__input');
                input.addEventListener('keyup', event => {
                    if (event.keyCode === 13 && !this.submitDisable) {
                        event.preventDefault();
                        this.inviteColleagues();
                    }
                });
            }, 100);
        },
        // invite collegues api call
        inviteColleagues() {
            const inviteData = {};
            inviteData.mission_id = this.currentMission;
            inviteData.to_user_id = this.invitedUserId;
            inviteColleague(inviteData).then(response => {
                this.submitDisable = true;
                if (response.error == true) {
                    this.classVariant = 'danger';
                    this.message = response.message;
                    this.$refs.autosuggest.$data.currentIndex = null;
                    this.$refs.autosuggest.$data.internalValue = "";
                    this.showErrorDiv = true;
                } else {
                    this.query = '';
                    this.selected = '';
                    this.currentMissionId = 0;
                    this.invitedUserId = 0;
                    this.$refs.autosuggest.$data.currentIndex = null;
                    this.$refs.autosuggest.$data.internalValue = '';
                    this.classVariant = 'success';
                    this.message = response.message;
                    this.showErrorDiv = true;
                }
            });
        },
        // Apply for mission
        applyForMission(mission) {
            const missionData = {};
            missionData.mission_id = mission.mission_id;
            missionData.availability_id = mission.availability_id;
            applyMission(missionData).then(response => {
                if (response.error == true) {
                    this.makeToast('danger', response.message);
                } else {
                    this.makeToast('success', response.message);
                    this.$emit('getMissions');
                }
            });
        },
        makeToast(variant = null, message) {
            this.$bvToast.toast(message, {
                variant: variant,
                solid: true,
                autoHideDelay: 1000
            });
        },
        submitNewMission() {
            if (this.submitNewMissionUrl != '') {
                window.open(this.submitNewMissionUrl, '_self');
            }
        },
        cardHeightAdj() {
            const cardBodyList = document.querySelectorAll('.card-grid .card-body');
            // check if card content is already visible in the DOM
            if (cardBodyList.length > 0) {
                if (!cardBodyList[0].children[0].offsetHeight) {
                    return;
                }

                cardBodyList.forEach((cardBody) => {
                    const card = cardBody.parentNode;
                    const cardHeight = cardBody.children[0].offsetHeight + card.children[1].offsetHeight;
                    card.style.height = `${cardHeight}px`;

                    cardBody.parentNode.addEventListener('mouseover', function (mouseEvent) {
                        const cardBodyH = this.children[2].children[1].offsetHeight + this.children[2].children[0].offsetHeight + this.children[1].offsetHeight;
                        const cardTotalHeight = cardBodyH - this.offsetHeight;
                        this.children[1].style.transform = `translateY(-${cardTotalHeight}px)`;
                        this.children[2].style.transform = `translateY(-${cardTotalHeight}px)`;
                        this.parentNode.classList.add('active');
                    });

                    cardBody.parentNode.addEventListener('mouseleave', function () {
                        if (!document.body.classList.contains("modal-open")) {
                            this.children[1].style.transform = 'translateY(0)';
                            this.children[2].style.transform = 'translateY(0)';
                            this.parentNode.classList.remove('active');
                        }
                    });
                });

                if (this.cardHeightAdjIntervalId) {
                    clearInterval(this.cardHeightAdjIntervalId);
                }
            }
        },

        checkMissionTypeGoal(missionType) {
            if (constants.MISSION_TYPE_GOAL == missionType) {
                return true;
            } else {
                return false;
            }
        }
    },
    created() {
        this.languageData = JSON.parse(store.state.languageLabel);
        this.isInviteCollegueDisplay = this.settingEnabled(
            constants.INVITE_COLLEAGUE
        );
        this.isStarRatingDisplay = this.settingEnabled(constants.MISSION_RATINGS);
        this.isSubmitNewMissionSet = this.settingEnabled(
            constants.USER_CAN_SUBMIT_MISSION
        );
        this.isThemeSet = this.settingEnabled(constants.THEMES_ENABLED);
        this.submitNewMissionUrl = store.state.submitNewMissionUrl;
    },
    mounted() {
        this.cardHeightAdjIntervalId = setInterval(this.cardHeightAdj, 500);
    }
};
</script>
