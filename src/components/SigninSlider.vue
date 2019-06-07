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
	name: "SigninSlider",
	data() {
		return {
			carouselItems: [],
			isDynamicCarsousetSet : false
		};
	},

	created(){

		if(JSON.parse(store.state.slider)) { 
		   this.carouselItems = store.state.slider;
		}
		// this.createConnection();
	},
	methods:{
		// createConnection(){
		// 	axios.get(process.env.VUE_APP_API_ENDPOINT+"connect")
  //               .then((response) => {
  //                   if (response.data.data.slider) {
  //                       var slider = response.data.data.slider 
  //                       if (slider) {
  //                           // Convert slider object to array
  //                           let listOfSliderObjects = Object.keys(slider).map((key) => {
  //                           return slider[key]
  //                       })

  //                           this.carouselItems = listOfSliderObjects;
  //                           this.isDynamicCarsousetSet =true
  //                       } else {
  //                           var sliderData = [];        
  //                       }
  //                   }else{
  //                       var slider = []; 
  //                   } 
  //               })
  //               .catch(error => {
  //                   this.createConnection();
  //               })
		// },
		getTitle: (translations) => {
			// Fetch slider title by language
			if(translations){
				var filteredObj  = translations.filter(function (item, i) { 
				if(item.lang === store.state.defaultLanguage.toLowerCase()){
					return translations[i].slider_title;
				}
				});
				if(filteredObj[0].slider_title){
			   		return filteredObj[0].slider_title;
			   	}
			}
		},

		getDescription: (translations) => {
			// Fetch slider description by language
			if(translations){
				var filteredObj  = translations.filter(function (item, i) { 
				if(item.lang === store.state.defaultLanguage.toLowerCase()){
					return translations[i].slider_description;
				}
				});
				if(filteredObj[0].slider_description){
					return filteredObj[0].slider_description;
				}
			}
		}
	}

};
</script>
