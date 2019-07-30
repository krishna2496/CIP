<template>
	<div>
		<div v-bind:class="{ 'content-loader-wrap': true, 'slider-loader': carouselLoader}">
			<div class="content-loader"></div>
		</div>
		<div class="thumb-slider" v-if="mediaCarouselList.length > 0">
			<div  
				v-bind:class="{
				'gallery-top' : true,
				'default-img': deafultImage,
				'default-video': deafultVideo
				}"
			>
				<div class="img-wrap inner-gallery-block" >
						<img :src="mediaCarouselList[0].media_path">
				</div>
				<div class="video-wrap inner-gallery-block">
					<iframe id="video" width="560" height="315" :src="getDefaultEmbededPath(mediaCarouselList[0])" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
					</iframe>
				</div>	
			</div>
			<carousel  :nav="true" :dots="false" :items="5" :loop="loop" :mouseDrag="false" :touchDrag="false" class="gallery-thumbs" :margin ="8" :responsive="{0:{items:3},576:{items:4},1200:{items:5}}">
				<div class="thumbs-col" v-bind:class="{
				'video-block': media.media_type == 'mp4', 
				'img-block': media.media_type != 'mp4'}" 
				v-for="(media , v) in mediaCarouselList" :key="v">
					<img :src="getMediaPath(media)" v-bind:class="{'video-item': media.media_type == 'mp4'}"
					:data-src="getEmbededPath(media)"
					>
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
		   carouselLoader: true,
		   deafultImage : true,
		   deafultVideo : false,
		   loop :true
	    }
        },
    directives: {},
    computed: {
        
    },
    methods: {
    	getMediaPath(media) {
    		if(media.media_type == 'mp4') {
    			let videoPath = media.media_path;
			 	let data = videoPath.split("=");
            	return  "https://img.youtube.com/vi/"+data.slice(-1)[0]+"/mqdefault.jpg";
    		} else {
    			return media.media_path;
    		}
    	},

    	getEmbededPath(media) {
    		if(media.media_type == 'mp4') {
    			let videoPath = media.media_path;
			 	let data = videoPath.split("=");
            	return  "https://www.youtube.com/embed/"+data.slice(-1)[0];
    		} else {
    			return media.media_path;
    		}
    	},

    	getDefaultEmbededPath(media) {
    		if(media.media_type == 'mp4') {
    			let videoPath = media.media_path;
			 	let data = videoPath.split("=");
            	return  "https://www.youtube.com/embed/"+data.slice(-1)[0];
    		} 
    	},

		handleSliderClick(event){
			event.stopPropagation()
			var hideVideo = document.querySelector(".video-wrap");
			var galleryImg = document.querySelector(".gallery-top .img-wrap");
			var galleryImgSrc = document.querySelector(".gallery-top .img-wrap img");
			var videoSrc =  document.querySelector(".video-wrap iframe");
			var dataSrc = event.target.getAttribute('data-src');
			if(event.target.classList.contains("video-item")){
				videoSrc.src = dataSrc
				hideVideo.style.display = "block";
				galleryImg.style.display = "none";	
			}
			else if(event.target.classList.contains("btn-play")){
				var parentBtn = event.target.parentNode;
				var siblingBtn = parentBtn.childNodes;
				hideVideo.style.display = "block";
				galleryImg.style.display = "none";
				videoSrc.src = siblingBtn[0].getAttribute('data-src')
			}
			else{
				 galleryImgSrc.src = event.target.src ;		
				galleryImg.style.display = "block";
				hideVideo.style.display = "none";
			}
		},
    },
    created() {

		if (this.$route.params.misisonId) 
		{
			missionCarousel(this.$route.params.misisonId).then(response => {
				this.carouselLoader = true;	
				if (!response.error) {
					this.mediaCarouselList = response.data;
					if(response.data.length <= 5) {
						this.loop = false
					}
					this.carouselLoader = false;  
					
					if(this.mediaCarouselList[0].media_type == "mp4") {
					 	this.deafultVideo = true;
					 	this.deafultImage = false;
					}
		  
				}
			})		
		}
		var globalThis = this;
		 setTimeout(() => {
		  var thumbImg = document.querySelectorAll(".gallery-thumbs .owl-item img, .gallery-thumbs .owl-item .btn-play");
			thumbImg.forEach(function(itemEvent){
				itemEvent.addEventListener("click", globalThis.handleSliderClick);	
			});
			
		},1000);
		window.addEventListener('resize', function() {
			setTimeout(() => {
				var thumbImg = document.querySelectorAll(".gallery-thumbs .owl-item img, .gallery-thumbs .owl-item .btn-play");
					thumbImg.forEach(function(itemEvent){
					itemEvent.removeEventListener("click", globalThis.handleSliderClick);	
					itemEvent.addEventListener("click", globalThis.handleSliderClick);	
				});
			},2000);
		});
    }
};
</script>

