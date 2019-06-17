<template>
    <div class="cards-wrapper">
        <div class="card-grid">
            <b-row>
                <b-col lg="4" sm="6" class="card-outer" data-aos="fade-up" v-for="mission in items">
                    <b-card no-body>
                        <b-card-header>
                            <div class="header-img-block">
                                <b-link class="group-img" :style="{backgroundImage: 'url('+mission.default_media_path+')'}">
                                    <img src="mission.default_media_path" alt="group-img">
                                </b-link>
                                <b-link href="#" class="location" :title="$t('label.location')">
                                    <i>
                                        <img src="../assets/images/landing/location.svg" 
                                        :alt="$t('label.location')">
                                    </i>{{mission.city_name}}
                                </b-link>
                               <!--  <b-button class="favourite-icon" v-b-tooltip.hover 
                                :title="$t('label.add_to_favourite')">
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
                                </b-button>
                                <b-button class="add-icon" :title="$t('label.add')">
                                    <img src="../assets/images/landing/add-group-ic.svg" 
                                    :alt="$t('label.add')">
                                </b-button -->
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
                                <div class="group-ratings">
                                    <span class="group-name">{{mission.organisation_name}}</span>
                                    <!-- <span class="ratings">
                                        <StarRating :config="config" ratings="3"></StarRating>
                                    </span> -->
                                </div>
                            </div>
                            <div class="group-details">
                                <div class="top-strip">
                                    
                                    <span>
                                        <!-- Mission type time -->
                                        <template v-if="mission.mission_type == 'TIME'">

                                            <template v-if="mission.start_date != '' && mission.end_date != ''">
                                                {{ $t("label.from")+' '+mission.start_date+' '+$t("label.until")+' '+mission.end_date }}   
                                            </template>

                                            <template v-else>
                                                {{ $t("label.on_going_opportunities") }}  
                                            </template>
                                            
                                        </template>

                                        <!-- Mission type goal -->
                                        <template v-if="mission.mission_type == 'GOAL'">
                                            {{mission.objective}}
                                        </template>

                                    </span>    
                                   
                                </div>
                                <template v-if="mission.mission_type == 'TIME'">
                                    <div class="group-details-inner">
                                        
                                        <template v-if="mission.total_seats != 0">
                                        <div class="detail-column info-block">
                                            <i class="icon-wrap">
                                                <img src="../assets/images/landing/user-icon.svg" alt="user">
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
                                                <img src="../assets/images/landing/user-icon1.svg" alt="user">
                                            </i>
                                            <div class="text-wrap">
                                                <span class="title-text mb-1">{{mission.mission_application_count}}</span>
                                                <span class="subtitle-text">{{ $t("label.already_volunteered") }}</span>
                                            </div>
                                        </div>
                                        </template>

                                        <template v-if="mission.application_deadline != ''">
                                        <div class="detail-column info-block">
                                            <i class="icon-wrap">
                                                <img src="../assets/images/landing/clock.svg" alt="user">
                                            </i>
                                            <div class="text-wrap">
                                                <span class="title-text mb-1">{{mission.application_deadline}}</span>
                                                <span class="subtitle-text">{{ $t("label.Deadline") }}</span>
                                            </div>
                                        </div>
                                        </template>

                                    </div>
                                </template>

                                <template v-else>
                                    <div class="group-details-inner has-progress">
                                    <div class="detail-column info-block">

                                        <template v-if="mission.total_seats != 0">
                                            <i class="icon-wrap">
                                                <img src="../assets/images/landing/user-icon.svg" alt="user">
                                            </i>
                                            <div class="text-wrap">
                                                <span class="title-text mb-1">{{mission.seats_left}}</span>
                                                <span class="subtitle-text">{{ $t("label.seats_left") }}</span>
                                            </div>
                                        </template>

                                        <template v-else>
                                            <i class="icon-wrap">
                                                <img src="../assets/images/landing/user-icon1.svg" alt="user">
                                            </i>
                                            <div class="text-wrap">
                                                <span class="title-text mb-1">{{mission.mission_application_count}}</span>
                                                <span class="subtitle-text">{{ $t("label.already_volunteered") }}</span>
                                            </div>
                                        </template>

                                    </div>
                                    <div class="detail-column progress-block">
                                        <i class="icon-wrap">
                                            <img src="../assets/images/landing/target-ic.svg" alt="user">
                                        </i>
                                        <div class="text-wrap">
                                            <b-progress :value="value" :max="max" class="mb-2"></b-progress>
                                            <span class="subtitle-text">8000 achieved</span>
                                        </div>
                                    </div>
                                </div>
                                </template>

                            </div>
                        </b-card-body>
                        <b-card-footer>
                            <b-button class="btn-bordersecondary icon-btn">
                                <template v-if="mission.set_view_detail == 0"><span>{{ $t("label.apply") }}</span></template>
                                <template v-if="mission.set_view_detail == 1"><span>{{ $t("label.view_detail") }}</span></template>
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
                        </b-card-footer>
                    </b-card>
                </b-col>
    

            </b-row>
        </div>
    </div>
</template>
<script>
import StarRating from "./StarRating";
import store from '../store';
    export default {
        name: "LandingCard",
        components:{
            StarRating
        },
        props: {
            items: Array,
        },
        data() {
            return {
                showBlock: false,
                max: 100,
                value: 80,
                activeFav : false,
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
                ],
                config: {
                    isIndicatorActive: true,
                    style: {
                    fullStarColor: "#F7D341",
                    }
                },
                default_text_val: this.default_text
            };
        },
        methods: {
            handleFav(){
                var btn_active = document.querySelector(".favourite-icon") 
                btn_active.classList.toggle('active');
            },
            //get theme title
            getThemeTitle(translations){
                if(translations){

                    var filteredObj  = translations.filter(function (item, i) { 
                    if(item.lang === store.state.defaultLanguage.toLowerCase()){
                        return translations[i].title;
                    }
                    });
                    if(filteredObj[0].title){
                        return filteredObj[0].title;
                    }
                }
            }

		},
		mounted(){ 
			 var btn_active = document.querySelectorAll(".favourite-icon");
			  btn_active.forEach(function(event){
				  event.addEventListener("click", function(){
					  event.classList.toggle("active");
				  })
			  });
		}
    };
</script>