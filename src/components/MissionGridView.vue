<template>
    <div class="cards-wrapper" v-if="items.length > 0">
        <div class="card-grid">
            <b-row>
                <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up" 
                    v-for="mission in items">
                    <b-card no-body>
                            <b-card-header>
                                <div class="header-img-block">
                                    <div  v-if="checkDefaultMediaFormat(mission.default_media_type)"  class="group-img" 
                                    :style="{backgroundImage: 'url('+mission.default_media_path+')'}">
                                        <img :src="mission.default_media_path" alt="mission.default_media_path">
                                    </div>

                                    <div  v-else  class="group-img" 
                                    :style="{backgroundImage: 'url('+youtubeThumbImage(mission.default_media_path)+')'}">
                                    </div>

                                    <div class="location">
                                        <i>
                                            <img :src="`${this.$store.state.imagePath}/assets/images/location.svg`" 
                                            :alt="$t('label.location')">
                                        </i>
                                        {{mission.city_name}}
                                    </div>      
                                </div>
                                <div class="group-category" 
                                v-if="mission.mission_theme != null">{{getThemeTitle(mission.mission_theme.translations)}}
                                </div>
                            </b-card-header>

                            <b-card-body>
                                <div class="content-block">
                                    <h2 class="card-title mb-2">
                                        {{mission.title}}
                                    </h2>
                                    <b-card-text>
                                        {{shortDescriptionSubString(mission.short_description)}}
                                    </b-card-text>
                                    <div class="group-ratings">
                                        <span class="group-name">{{mission.organisation_name}}</span>
                                    </div>
                                </div>
                                <div class="group-details">
                                    <div class="top-strip">
                                        <span>
                                        <!-- Mission type time -->
                                        <template v-if="checkMissionTypeTime(mission.mission_type)">
                                        <template v-if="mission.end_date !== null">
                                            {{ $t("label.from") }} 
                                            {{mission.start_date | formatDate }} 
                                            {{ $t("label.until")}}
                                            {{ mission.end_date | formatDate }} 
                                        </template>
                                        <template v-else>
                                            {{ $t("label.on_going_opportunities") }}  
                                        </template>
                                        </template>
                                        <!-- Mission type goal -->
                                        <template v-else>
                                            {{mission.objective}}
                                        </template>
                                        </span>    
                                    </div>
                                    <template v-if="checkMissionTypeTime(mission.mission_type)">
                                        <div class="group-details-inner">
                                            <template v-if="mission.total_seats != 0">
                                                <div class="detail-column info-block">
                                                    <i class="icon-wrap">
                                                        <img :src="`${this.$store.state.imagePath}/assets/images/user-icon.svg`" alt="user">
                                                        
                                                    </i>
                                                    <div class="text-wrap">
                                                        <span class="title-text mb-1">{{mission.seats_left}}</span>
                                                        <span class="subtitle-text">{{ $t("label.seats_left") }}</span>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="detail-column info-block">
                                                    <i class="icon-wrap">
                                                        <img :src="`${this.$store.state.imagePath}/assets/images/user-icon1.svg`" alt="user">
                                                    </i>
                                                    <div class="text-wrap">
                                                        <span class="title-text mb-1">{{mission.mission_application_count}}</span>
                                                        <span class="subtitle-text">{{ $t("label.already_volunteered") }}</span>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-if="mission.application_deadline != null">
                                                <div class="detail-column info-block">
                                                    <i class="icon-wrap">
                                                        <img :src="`${this.$store.state.imagePath}/assets/images/clock.svg`" alt="user">
                                                    </i>
                                                    <div class="text-wrap">
                                                        <span class="title-text mb-1">{{mission.application_deadline | formatDate}}</span>
                                                        <span class="subtitle-text">{{ $t("label.deadline") }}</span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="group-details-inner">
                                            <div class="detail-column info-block">
                                            <template v-if="mission.total_seats != 0">
                                                <i class="icon-wrap">
                                                    <img :src="`${this.$store.state.imagePath}/assets/images/user-icon.svg`" alt="user">
                                                </i>
                                                <div class="text-wrap">
                                                    <span class="title-text mb-1">{{mission.seats_left}}</span>
                                                    <span class="subtitle-text">{{ $t("label.seats_left") }}</span>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <i class="icon-wrap">
                                                    <img :src="`${this.$store.state.imagePath}/assets/images/user-icon1.svg`" alt="user">
                                                </i>
                                                <div class="text-wrap">
                                                    <span class="title-text mb-1">{{mission.mission_application_count}}</span>
                                                    <span class="subtitle-text">{{ $t("label.already_volunteered") }}</span>
                                                </div>
                                            </template>

                                            </div>
                                        </div>
                                    </template>
                                </div>                        
                            </b-card-body>

                            <b-card-footer>
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
                            </b-card-footer>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
    <div class="cards-wrapper" v-else>
        <h2 class="text-center">{{ $t("label.no_record_found")}}</h2>
    </div>
</template>

<script>
import store from '../store';
import constants from '../constant';

export default {
    name: "MissionGridView",
    components:{},
    props: {
        items: Array,
    },
    data() {
        return { };
    },
    methods: {
        handleFav() {
            var btn_active = document.querySelector(".favourite-icon") 
            btn_active.classList.toggle('active');
        },
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
        // Is default media is video or not
        checkDefaultMediaFormat(mediaType) {
            return mediaType != constants.YOUTUBE_VIDEO_FORMAT
        },
        // Check mission type
        checkMissionTypeTime(missionType) {
            return missionType == constants.MISSION_TYPE_TIME
        },
        // Get sub string of short description
        shortDescriptionSubString(shortDescription) {
            if (shortDescription.length <= constants.MISSION_GRID_VIEW_SHORT_DESCRIPTION_CHARACTER) {
                return shortDescription
            } else {
                return shortDescription.substring(0,constants.MISSION_GRID_VIEW_SHORT_DESCRIPTION_CHARACTER)+"...";
            }
        },
        // Get Youtube Thumb images
        youtubeThumbImage(videoPath) {
            let data = videoPath.split("=");
            return  "https://img.youtube.com/vi/"+data.slice(-1)[0]+"/mqdefault.jpg";
        }
    },
    mounted(){          
        var btn_active = document.querySelectorAll(".favourite-icon");
        btn_active.forEach(function(event){
            event.addEventListener("click", function(){
                event.classList.toggle("active");
            })
        });
    },
};
</script>