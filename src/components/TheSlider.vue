<template>
	<div class="signin-slider">
		<b-carousel id="carousel-1" fade :interval="2000" :sliding-start="0" :sliding-end="1" indicators
			v-if="isDynamicCarsousetSet">
			<b-carousel-slide :no-wrap="wrap" v-for="item in carouselItems" :key="item.sort_order"
				:caption="getTitle(item.slider_detail)" :text="getDescription(item.slider_detail)" :img-src="item.url">
			</b-carousel-slide>
		</b-carousel>

		<b-carousel id fade :interval="0" indicators v-else>
			<b-carousel-slide :img-src="$store.state.imagePath+'/assets/images/sliderimg1.png'"></b-carousel-slide>
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
				isDynamicCarsousetSet: false,
				wrap: true
			};
		},
		created() {
			if (store.state.slider != null && JSON.parse(store.state.slider).length > 0) {
				this.carouselItems = JSON.parse(store.state.slider);
				this.isDynamicCarsousetSet = true
			}
		},
		methods: {
			getTitle: (sliderDetail) => {
				if (typeof sliderDetail !== 'undefined') {
					let translations = JSON.parse(JSON.stringify(sliderDetail)).translations;
					//Fetch slider title by language
					if (translations) {
						let filteredObj = translations.filter( (item, i) => {
							if (item.lang === store.state.defaultLanguage.toLowerCase()) {
								return translations[i].slider_title;
							}
						});
						if (filteredObj.length > 0 && filteredObj[0].slider_title) {
							return filteredObj[0].slider_title;
						}
					}
				}
			},
			getDescription: (sliderDetail) => {

				if (typeof sliderDetail !== 'undefined') {
					let translations = JSON.parse(JSON.stringify(sliderDetail)).translations;
					// Fetch slider description by language			
					if (translations) {
						let filteredObj = translations.filter( (item, i) => {
							if (item.lang === store.state.defaultLanguage.toLowerCase()) {
								return translations[i].slider_description;
							}
						});
						if (filteredObj.length > 0 && filteredObj[0].slider_description) {
							return filteredObj[0].slider_description;
						}
					}
				}
			}
		}

	};
</script>