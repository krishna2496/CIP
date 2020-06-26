<template>
    <div class="cards-wrapper" v-if="items.length > 0">
        <div class="card-listing">
            <div class="card-outer" v-for="(mission, index) in items" :key=index>
                <b-card no-body>
                    <b-card-header>
                        <div class="header-img-block">
                            <b-alert show class="alert card-alert alert-success" v-if="getAppliedStatus(mission)">
                                {{languageData.label.applied}}</b-alert>
                            <b-alert show class="alert card-alert alert-warning" v-if="getClosedStatus(mission)">
                                {{languageData.label.closed}}</b-alert>
                            <div v-if="checkDefaultMediaFormat(mission.default_media_type)" class="group-img"
                                :style="{backgroundImage: 'url('+getMediaPath(mission.default_media_path)+')'}">
                                <img :src="getMediaPath(mission.default_media_path)" alt="">
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
                         
                        </div>
                    </b-card-header>

                    <b-card-body>
                        <div class="card-detail-column">
                            
                            <div class="content-block">
                                <!-- <div class="mission-label" v-if="mission.is_virtual == 1">
                                  <span>{{languageData.label.virtual_mission}}</span>
                                </div> -->
                                <div class="mission-label-wrap">
                                    <div class="group-category" v-if="mission.mission_theme != null && isThemeSet"><span class="category-text">{{getThemeTitle(mission.mission_theme.translations)}}</span></div>
                                    <div class="mission-label volunteer-label" v-if="isDispalyMissionLabel && checkMissionTypeVolunteering(mission.mission_type)">
										<span :style="{ backgroundColor: volunteeringMissionTypeLabels.backgroundColor}"><i class="icon-wrap"><img :src="volunteeringMissionTypeLabels.icon" alt="volunteer icon"></i>{{volunteeringMissionTypeLabels.label}}</span>
									</div>
                                    <div class="mission-label virtual-label" v-if="mission.is_virtual == 1">
                                        <span>{{languageData.label.virtual_mission}}</span>
                                    </div>
                                    
                                </div>
                                <b-link target="_blank" :to="'/mission-detail/' + mission.mission_id"
                                        class="card-title">
                                    {{mission.title | substring(75)}}
                                </b-link>
                                <div class="ratings" v-if="isStarRatingDisplay">
                                    <star-rating v-bind:increment="0.5" v-bind:max-rating="5" inactive-color="#dddddd" active-color="#F7D341" v-bind:star-size="18" :rating="mission.mission_rating_count" :read-only="true">
                                    </star-rating>
                                </div>
                                <b-card-text>
                                    {{mission.short_description | substring(150)}}
                                </b-card-text>
                                <p class="event-name">{{ languageData.label.for }} <span>{{mission.organization.name}}</span></p>
                            </div>
                            <div class="group-details volunteer-progress">
                                <template v-if="mission.total_seats != 0 && mission.total_seats !== null">
                                    <div class="detail-column seat-info">
                                        <i class="icon-wrap">
                                            <img :src="$store.state.imagePath+'/assets/images/user-icon.svg'"alt="user">
                                        </i>
                                        <div class="text-wrap">
                                            <span class="title-text">{{mission.seats_left}}</span>
                                            <span class="subtitle-text">{{ languageData.label.seats_left }}</span>
                                        </div>
                                    </div>
                                </template>
                                <template v-if="mission.application_deadline != null ||
                                        checkMissionTypeTime(mission.mission_type)
                                        ">
                                    <div class="detail-column info-block" v-if="mission.application_deadline != null">
                                        <i class="icon-wrap">
                                            <img :src="$store.state.imagePath+'/assets/images/clock.svg'" alt="user">
                                        </i>
                                        <div class="text-wrap">
                                            <span
                                                    class="title-text">{{mission.application_deadline | formatDate}}</span>
                                            <span class="subtitle-text">{{ languageData.label.deadline }}</span>
                                        </div>
                                    </div>
                                </template>
                                <div class="detail-column calendar-col">
                                    <i class="icon-wrap">
                                        <img :src="$store.state.imagePath+'/assets/images/calendar.svg'" alt="user">
                                    </i>
                                    <div class="text-wrap" v-if="mission.end_date !== null">
                                        <span class="title-text"><em>{{ languageData.label.from }}</em>
                                            {{mission.start_date | formatDate }}</span>
                                        <span class="title-text"><em>{{ languageData.label.until}}</em>
                                            {{ mission.end_date | formatDate }}</span>
                                    </div>
                                </div>
                                <div class="detail-column progress-block" v-if="!checkMissionTypeTime(mission.mission_type)">
                                    <i class="icon-wrap">
                                        <img :src="$store.state.imagePath+'/assets/images/target-ic.svg'"
                                             alt="user">
                                    </i>
                                    <div class="text-wrap">
                                        <b-progress :value="mission.achieved_goal | filterGoal" :max="mission.goal_objective"></b-progress>
                                        <span class="subtitle-text">{{mission.achieved_goal}}
                                        <span v-if="mission.label_goal_achieved != ''"> {{ mission.label_goal_achieved }}
                                                    </span>
										<span v-else>{{ languageData.label.achieved }}</span>
                                    </span>
                                    </div>
                                </div>
                                <div class="detail-column skill-col" v-if="mission.skill && isSkillDisplay">
                                    <i class="icon-wrap">
                                        <img :src="$store.state.imagePath+'/assets/images/skill-icon.svg'" alt="skill icon">
                                    </i>
                                    <div class="text-wrap">
                                        <span class="title-text">{{ languageData.label.skills }}</span>
                                        <span class="subtitle-text skill-text-wrap">{{getSkills(mission.skill)}}</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-action-block">
                            <!-- <div class="donate-btn-wrap">
                                <b-form-group>
                                    <label for="">$</label>
                                    <b-form-input id="" type="text" :class="form-control" value="20"></b-form-input>
                                    <b-button class="btn-donate btn-fillsecondary">Donate</b-button>
                                </b-form-group>
                            </div> -->
                            <div class="btn-wrap">
                                <b-link :to="'/mission-detail/' + mission.mission_id">
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
                            <div class="social-btn">
								<b-button class="icon-btn"  v-if="isInviteCollegueDisplay" v-b-tooltip.hover
										:title="languageData.label.recommend_to_co_worker"
										@click="handleModal(mission.mission_id)">
										<img :src="$store.state.imagePath+'/assets/images/multi-user-icon.svg'" alt="multi user icon">
								</b-button>
								
								<b-button
								v-bind:class="{
									'icon-btn' : true,
									'fill-heart-btn' : mission.is_favourite == 1
								}"
								:title="mission.is_favourite == 1 ?  languageData.label.remove_from_favourite :languageData.label.add_to_favourite"
								@click="favoriteMission(mission.mission_id)"
								>
									<img v-if="mission.is_favourite == 0" :src="$store.state.imagePath+'/assets/images/heart-icon.svg'" alt="heart icon">
									<img v-if="mission.is_favourite == 1" :src="$store.state.imagePath+'/assets/images/heart-fill-icon.svg'" alt="heart icon">
								</b-button>
							</div>                                

                        </div>
                    </b-card-body>
                </b-card>

            </div>

        </div>
        <b-modal @hidden="hideModal" ref="userDetailModal" :modal-class="myclass" size="lg" hide-footer>
            <template slot="modal-header" slot-scope="{ close }">
                <i class="close" @click="close()" v-b-tooltip.hover :title="languageData.label.close"></i>
                <h5 class="modal-title">{{languageData.label.search_user}}</h5>
            </template>
            <b-alert show :variant="classVariant" dismissible v-model="showErrorDiv">{{ message }}</b-alert>
            <div class="autocomplete-control">
                <div class="autosuggest-container">
                    <VueAutosuggest ref="autosuggest" name="user" v-model="query" :suggestions="filteredOptions"
                                    @input="onInputChange" @selected="onSelected" :get-suggestion-value="getSuggestionValue"
                                    :input-props="{
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
            <b-button class="btn-bordersecondary icon-btn">
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
import store from "../store";
import constants from "../constant";
import StarRating from "vue-star-rating";
import moment from "moment";
import {
	favoriteMission,
	inviteColleague,
	applyMission
} from "../services/service";
import { VueAutosuggest } from "vue-autosuggest";

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
		autoSuggestPlaceholder: "",
		submitDisable: true,
		languageData: [],
		isInviteCollegueDisplay: true,
		isStarRatingDisplay: true,
		isQuickAccessSet: true,
		isSubmitNewMissionSet: true,
		isThemeSet: true,
		submitNewMissionUrl: "",
		isSkillDisplay: true,
		isDispalyMissionLabel : false,
		isVolunteeringSet : true,
		isDonationSet : true,
		missionTypeLabels : "",
		volunteeringMissionTypeLabels : {
			'icon' : '',
			'label' : '',
			'backgroundColor' : ''
		},
		donationMissionTypeLabels : {
			'icon' : '',
			'label' : '',
			'backgroundColor' : ''
		}
		};
	},
	computed: {
		filteredOptions() {
		if (this.userList) {
			return [
			{
				data: this.userList.filter(option => {
				let firstName = option.first_name.toLowerCase();
				let lastName = option.last_name.toLowerCase();
				let email = option.email.toLowerCase();
				let searchString = firstName + "" + lastName + "" + email;
				return searchString.indexOf(this.query.toLowerCase()) > -1;
				})
			}
			];
		}
		}
	},
	methods: {
		hideModal() {
		this.autoSuggestPlaceholder = "";
		this.submitDisable = true;
		this.invitedUserId = "";
		this.query = "";
		this.selected = "";
		},
		noRecordFound() {
		let defaultLang = store.state.defaultLanguage.toLowerCase();
		if (JSON.parse(store.state.missionNotFoundText) != "") {
			let missionNotFoundArray = JSON.parse(store.state.missionNotFoundText);
			let data = missionNotFoundArray.filter(item => {
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
		// Get theme title
		getThemeTitle(translations) {
		if (translations) {
			let filteredObj = translations.filter((item, i) => {
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
		getMediaPath(mediaPath) {
		if (mediaPath != "") {
			return mediaPath;
		} else {
			return (
			store.state.imagePath +
			"/assets/images/" +
			constants.MISSION_DEFAULT_PLACEHOLDER
			);
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
		// Get Youtube Thumb images
		youtubeThumbImage(videoPath) {
		let data = videoPath.split("=");
		return (
			"https://img.youtube.com/vi/" + data.slice(-1)[0] + "/mqdefault.jpg"
		);
		},
		// Add mission to favorite
		favoriteMission(missionId) {
		let missionData = {
			mission_id: ""
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
		if (item) {
			this.selected = item.item;
			this.submitDisable = false;
			this.invitedUserId = item.item.user_id;
		}
		},
		//This is what the <input/> value is set to when you are selecting a suggestion.
		getSuggestionValue(suggestion) {
		let firstName = suggestion.item.first_name;
		let lastName = suggestion.item.last_name;
		return firstName + " " + lastName;
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
			var input = document.getElementById("autosuggest__input");
			input.addEventListener("keyup", event => {
			if (event.keyCode === 13 && !this.submitDisable) {
				event.preventDefault();
				this.inviteColleagues();
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
			this.$refs.autosuggest.$data.internalValue = "";
			this.showErrorDiv = true;
			} else {
			this.query = "";
			this.selected = "";
			this.currentMissionId = 0;
			this.invitedUserId = 0;
			this.$refs.autosuggest.$data.currentIndex = null;
			this.$refs.autosuggest.$data.internalValue = "";
			this.classVariant = "success";
			this.message = response.message;
			this.showErrorDiv = true;
			}
		});
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
			});
		},
		makeToast(variant = null, message) {
		this.$bvToast.toast(message, {
			variant: variant,
			solid: true,
			autoHideDelay: 1000
		});
		},
		getAppliedStatus(missionDetail) {
			let currentDate = moment().format("YYYY-MM-DD HH::mm:ss");
			let missionEndDate = moment(missionDetail.end_date).format(
				"YYYY-MM-DD HH::mm:ss"
			);
			let checkEndDateExist = true;
			if (missionDetail.end_date != "" && missionDetail.end_date != null) {
				if (currentDate > missionEndDate) {
				checkEndDateExist = false;
				}
			}
			if (missionDetail.user_application_count == 1 && checkEndDateExist) {
				return true;
			}
		},
		getClosedStatus(missionDetail) {
			let currentDate = moment().format("YYYY-MM-DD HH::mm:ss");
			let missionEndDate = moment(missionDetail.end_date).format(
				"YYYY-MM-DD HH::mm:ss"
			);
			if (missionDetail.end_date != "" && missionDetail.end_date != null) {
				if (currentDate > missionEndDate) {
				return true;
				}
			}
		},
		submitNewMission() {
			if (this.submitNewMissionUrl != "") {
				window.open(this.submitNewMissionUrl, "_self");
			}
		},
		getSkills(skills) {
			let skillString = "";
			if (skills) {
				skills.filter((data, index) => {
				if (data) {
					if (skillString != "") {
					skillString = skillString + ", " + data.title;
					} else {
					skillString = data.title;
					}
				}
				});
			}
			return skillString;
		},
		checkMissionTypeVolunteering(missionType) {
			if (constants.MISSION_TYPE_TIME == missionType || constants.MISSION_TYPE_GOAL == missionType) {
				return true;
			} else {
				return false;
			}
		},
		checkMissionTypeDonation(missionType) {
			if (constants.MISSION_TYPE_DONATION == missionType) {
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
		this.isQuickAccessSet = this.settingEnabled(constants.QUICK_ACCESS_FILTERS);
		this.isSubmitNewMissionSet = this.settingEnabled(
		constants.USER_CAN_SUBMIT_MISSION
		);
		this.isThemeSet = this.settingEnabled(constants.THEMES_ENABLED);
		this.submitNewMissionUrl = store.state.submitNewMissionUrl;
		this.isSkillDisplay = this.settingEnabled(constants.SKILLS_ENABLED);

		this.isVolunteeringSet = this.settingEnabled(constants.VOLUNTERRING_ENABLED);
		this.isDonationSet = this.settingEnabled(constants.DONATION_ENABLED);
		if (this.isDonationSet && this.isVolunteeringSet) {
			this.isDispalyMissionLabel = true;
		}
		this.missionTypeLabels = JSON.parse(store.state.missionTypeLabels);
		if (JSON.parse(store.state.missionTypeLabels) != "") {
			let defaultLang = store.state.defaultLanguage.toLowerCase();
			this.missionTypeLabels.filter((item, i) => {
				// volunteering mission label
				if (item.type == constants.VOLUNTERRING_ENABLED) {
					this.volunteeringMissionTypeLabels.icon = item.icon;
					this.volunteeringMissionTypeLabels.backgroundColor = item.background_color;
					let data = item.translations.filter(translationsItem => {
						if (translationsItem.language_code == defaultLang) {
							this.volunteeringMissionTypeLabels.label = translationsItem.description;
						}
					});
					if (this.volunteeringMissionTypeLabels.label == "" && data[0] && data[0].description) {
						this.volunteeringMissionTypeLabels.label = data[0].description;
					}
				}
				// Donation mission label
				if (item.type == constants.VOLUNTERRING_ENABLED) {
					this.donationMissionTypeLabels.icon = item.icon;
					this.donationMissionTypeLabels.backgroundColor = item.background_color;
					let data = item.translations.filter(translationsItem => {
						if (translationsItem.language_code == defaultLang) {
							this.donationMissionTypeLabels.label = translationsItem.description;
						}
					});
					if (this.donationMissionTypeLabels.label == "" && data[0] && data[0].description) {
						this.donationMissionTypeLabels.label = data[0].description;
					}
				}

			});
		}
	}
};
</script>
