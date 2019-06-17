<template>
    <div class="cards-wrapper">
        <div class="card-listing">
            <div class="card-outer" v-for="mission in items">
                <b-card no-body>
                    <b-card-header>
                        <div class="header-img-block">

                             <b-link  v-if="checkDefaultMediaFormat(mission.default_media_type)"  class="group-img" 
                                :style="{backgroundImage: 'url('+mission.default_media_path+')'}">
                                    <img src="mission.default_media_path" alt="mission.default_media_path">
                                </b-link>
                                
                                 <b-link  v-else  class="group-img">
                                    <b-embed
                                        type="iframe"
                                        aspect="16by9"
                                        src="https://www.youtube.com/embed/zpOULjyy-n8"
                                        allowfullscreen>        
                                        </b-embed>
                                </b-link>

                                <b-link  class="location" :title="$t('label.location')">
                                    <i>
                                        <img src="../assets/images/landing/location.svg" 
                                        :alt="$t('label.location')">
                                    </i>{{mission.city_name}}
                                </b-link>

                             <!-- <b-button class="favourite-icon" v-b-tooltip.hover title="Add to favourite">
                                    <i class="normal-img">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 21" width="24" height="21">
                                        <g id="Main Content">
                                            <g id="1">
                                                <g id="Image content">
                                                    <path id="Forma 1" d="M22.1 2.86C20.9 1.66 19.3 1 17.59 1C15.89 1 14.29 1.66 13.08 2.86L12.49 3.45L11.89 2.86C10.69 1.66 9.08 1 7.38 1C5.67 1 4.07 1.66 2.87 2.86C0.38 5.34 0.38 9.36 2.87 11.84L11.78 20.71C11.93 20.86 12.11 20.95 12.3 20.98C12.36 20.99 12.43 21 12.49 21C12.74 21 13 20.9 13.19 20.71L22.1 11.84C24.59 9.36 24.59 5.34 22.1 2.86ZM20.71 10.45L12.49 18.64L4.26 10.45C2.54 8.74 2.54 5.96 4.26 4.25C5.09 3.42 6.2 2.96 7.38 2.96C8.56 2.96 9.66 3.42 10.5 4.25L11.79 5.53C12.16 5.9 12.81 5.9 13.18 5.53L14.47 4.25C15.31 3.42 16.41 2.96 17.59 2.96C18.77 2.96 19.88 3.42 20.71 4.25C22.43 5.96 22.43 8.74 20.71 10.45Z" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    </i>
                                     <i class="hover-img">
                                       <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 492.7 426.8" style="enable-background:new 0 0 492.7 426.8;" xml:space="preserve">
                                        <g>
                                            <g id="Icons_18_">
                                                <path d="M492.7,133.1C492.7,59.6,433.1,0,359.7,0c-48,0-89.9,25.5-113.3,63.6C222.9,25.5,181,0,133,0
                                                    C59.6,0,0,59.6,0,133.1c0,40,17.7,75.8,45.7,100.2l188.5,188.6c3.2,3.2,7.6,5,12.1,5s8.9-1.8,12.1-5L447,233.2
                                                    C475,208.9,492.7,173.1,492.7,133.1z"/>
                                            </g>
                                        </g>
                                        </svg>
                                    </i>       
                                </b-button> -->
                          <!--   <b-button class="add-icon" title="add">
                                <img src="../assets/images/landing/add-group-ic.svg" alt="add">
                            </b-button> -->
                        </div>
                        <b-link target="_blank" class="group-category" 
                            v-if="mission.mission_theme != null">{{getThemeTitle(mission.mission_theme.translations)}}</b-link>
                    </b-card-header>

                    <b-card-body>
                        <div class="content-block">
                             <b-link target="_blank" class="card-title mb-2">
                                    {{mission.title}}
                                </b-link>
                                <b-card-text>
                                   {{mission.short_description}}
                                </b-card-text>
                        </div>
                        <div class="group-wrap">
                            <div class="rating-with-button">
                                <div class="group-ratings">
                                     <span class="group-name">{{mission.organisation_name}}</span>
                                    <span class="ratings">
                                        <img src="../assets/images/landing/star-img.png" alt="stars">
                                    </span>
                                </div>
                                <div class="bottom-block">
                                    <b-link v-if="mission.set_view_detail == 0" :to="'/apply/' + mission.mission_id">
                                    <b-button class="btn-bordersecondary icon-btn">
                                        <span>{{ $t("label.apply") }}</span>
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
                                    </b-link>

                            <b-link v-if="mission.set_view_detail == 1" :to="'/mission_detail/' + mission.mission_id">
                                <b-button class="btn-bordersecondary icon-btn" >
                                    <span>{{ $t("label.view_detail") }}</span>
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
                            </b-link>

                                </div>
                            </div>
                            <div class="group-details">
                                <div class="top-strip">
                                    <span>From 10/01/2019 until 25/02/2019</span>
                                </div>
                                <div class="group-details-inner">
                                    <div class="detail-column info-block">
                                        <i class="icon-wrap">
                                            <img src="../assets/images/landing/user-icon.svg" alt="user">
                                        </i>
                                        <div class="text-wrap">
                                            <span class="title-text mb-1">10</span>
                                            <span class="subtitle-text">Seats left</span>
                                        </div>
                                    </div>
                                    <div class="detail-column info-block">
                                        <i class="icon-wrap">
                                            <img src="../assets/images/landing/clock.svg" alt="user">
                                        </i>
                                        <div class="text-wrap">
                                            <span class="title-text mb-1">09/01/2019</span>
                                            <span class="subtitle-text">Deadline</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-card-body>
                </b-card>
            </div>   
        </div>
    </div>
</template>
<script>
import StarRating from "./StarRating";
import store from '../store';
import constants from '../constant';
    export default {
        name: "CardListBlock",
        props: {
            items: Array,
        },
        components:{
            StarRating
        },
        data() {
            return {
                showBlock: false,
                max: 50,
                value: 33.333333333,
                grpImages: [
                    require("@/assets/images/landing/group-img1.png"),
                    require("@/assets/images/landing/group-img2.png"),
                    require("@/assets/images/landing/group-img3.png"),
                    require("@/assets/images/landing/group-img4.png"),
                    require("@/assets/images/landing/group-img5.png"),
                    require("@/assets/images/landing/group-img6.png"),
                    require("@/assets/images/landing/group-img7.png"),
                    require("@/assets/images/landing/group-img8.png"),
                    require("@/assets/images/landing/group-img9.png"),
                ]
            };
        },
        methods: {
            //get theme title
            getThemeTitle(translations) {
                if (translations) {
                    var filteredObj  = translations.filter(function (item, i) { 
                    if (item.lang === store.state.defaultLanguage.toLowerCase()) {
                        return translations[i].title;
                    }
                    });
                    if (filteredObj[0].title) {
                        return filteredObj[0].title;
                    }
                }
            },

            checkDefaultMediaFormat(mediaType) {
                return mediaType != constants.YOUTUBE_VIDEO_FORMAT
            },

            checkMissionTypeTime(missionType) {
                return missionType == constants.MISSION_TYPE_TIME
            }
        }
    };
</script>