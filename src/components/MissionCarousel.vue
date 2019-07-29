<template>
	<!-- 	<div class="content-loader-wrap slider-loader">
				<div class="content-loader"></div>
			</div> -->
	<div>
		<div v-bind:class="{ 'content-loader-wrap': true, 'slider-loader': carouselLoader}">
			<div class="content-loader"></div>
		</div>
		<div class="thumb-slider" v-if="mediaCarouselList.length > 0">
			<div class="gallery-top">
				<div class="img-wrap inner-gallery-block">
					<img src="../assets/images/gallery-img03.jpg">
				</div>
				<div class="video-wrap inner-gallery-block">
					 <iframe id="video" width="560" height="315" src='' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> 
				</div>
				
			</div>

			<carousel  :nav="true" :dots="false" :items="5" :loop="true" :mouseDrag="false" :touchDrag="false" class="gallery-thumbs" :margin ="8" :responsive="{0:{items:3},576:{items:4},1200:{items:5}}">
				<div class="thumbs-col" v-bind:class="{'video-block': media.media_type == 'mp4', 'img-block': media.media_type != 'mp4'}" v-for="(media , v) in mediaCarouselList" :key="v">
					<img :src="media.media_path" v-bind:class="{'video-item': media.media_type == 'mp4'}">
					<i v-if="media.media_type == 'mp4'" class="btn-play"></i>
				</div>
			</carousel>
		</div>
	</div>
</template>

<script>
import {missionCarousel} from "../services/service";
import carousel from 'vue-owl-carousel';

export default {
    name: "MissionCarousel",
    components: {carousel},
    props: [
    ],
    data: function() {
        return {
           mediaCarouselList:[],
		   carouselLoader: true
	    }
        },
    directives: {},
    computed: {
        
    },
    methods: {
		
    	
    },
    created() {
		if (this.$route.params.misisonId) 
		{
			missionCarousel(this.$route.params.misisonId).then(response => {
				this.carouselLoader = true;	
				if (!response.error) {
					this.mediaCarouselList = response.data;
					this.carouselLoader = false;
				}
			})		
		}
    }
};
</script>

