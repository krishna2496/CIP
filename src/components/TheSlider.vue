<template>
    <div class="signin-slider">
        <b-carousel  id="carousel-1" fade :interval="2000" indicators v-if="isDynamicCarsousetSet">
			<b-carousel-slide 
				v-for="item in carouselItems"
				:key="item.sort_order"
				:caption="getTitle(item.translations)"
				:text="getDescription(item.translations)"
				:img-src="item.url">     
			</b-carousel-slide>
        </b-carousel>
        
		<b-carousel id fade :interval="2000" indicators v-else>
			<b-carousel-slide img-src="../assets/images/login/sliderimg1.png" ></b-carousel-slide>
        </b-carousel>
    </div>

</template>

<script>

import store from '../store';
import axios from "axios";

export default {
	name: "TheSlider",
	data() {
		return {
			carouselItems: [],
			isDynamicCarsousetSet : false
		};
	},
	created(){
		if (JSON.parse(store.state.slider).length > 0) { 
		   this.carouselItems = JSON.parse(store.state.slider);
		   this.isDynamicCarsousetSet =true
		}
	},
	methods:{
		getTitle: (translations) => {
			// Fetch slider title by language
			if (translations) {
				var filteredObj  = translations.filter(function (item, i) { 
				if (item.lang === store.state.defaultLanguage.toLowerCase()) {
					return translations[i].slider_title;
				}
				});
				if (filteredObj[0].slider_title) {
			   		return filteredObj[0].slider_title;
			   	}
			}
		},

		getDescription: (translations) => {
			// Fetch slider description by language
			if (translations) {
				var filteredObj  = translations.filter(function (item, i) { 
				if (item.lang === store.state.defaultLanguage.toLowerCase()) {
					return translations[i].slider_description;
				}
				});
				if (filteredObj[0].slider_description) {
					return filteredObj[0].slider_description;
				}
			}
		}
	}

};
</script>
