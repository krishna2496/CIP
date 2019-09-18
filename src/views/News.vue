<template>
	<div class="inner-pages news-page lists-page">
		<header>
			<ThePrimaryHeader></ThePrimaryHeader>
		</header>
		<main>
			<b-container>
				<div class="banner-wrap">
					<div :style="{backgroundImage: 'url('+bgImg+')'}" class="banner-section">
						<b-container>
							<h1>{{langauageData.label.news}}</h1>
						</b-container>
					</div>
				</div>
				<NewsCard />
				<div class="pagination-block" data-aos="fade-up">
					<b-pagination
						v-model="currentPage"
						:total-rows="rows"
						:per-page="perPage"
						align="center"
						aria-controls="my-cardlist"
					></b-pagination>
				</div>
			</b-container>
		</main>
	<footer>
		<TheSecondaryFooter></TheSecondaryFooter>
	</footer>
	<back-to-top bottom="68px" right="40px" title="back to top">
		<i class="icon-wrap">
			<img class="img-normal" src="../assets/images/down-arrow.svg" alt="Down Arrow" />
			<img class="img-rollover" src="../assets/images/down-arrow-black.svg" alt="Down Arrow" />
		</i>
	</back-to-top>
	</div>
</template>

<script>
import NewsCard from "../components/NewsCardView";
import store from '../store';
import constants from '../constant';

export default {
	components: {
		ThePrimaryHeader : () => import("../components/Layouts/ThePrimaryHeader"),
		TheSecondaryFooter: () => import("../components/Layouts/TheSecondaryFooter"),
		NewsCard
	},
	data() {
		return {
			bgImg: require("@/assets/images/banner-img.png"),
			rows: 25,
			perPage: 5,
			currentPage: 1,
			langauageData : [],
			isNewsDisplay : true,
		};
	},
	methods: {},
	created() {
		this.langauageData = JSON.parse(store.state.languageLabel);
		this.isNewsDisplay = this.settingEnabled(constants.NEWS_ENABLED);
		if(!this.isNewsDisplay) {
			this.$router.push('/home')
		}
	},
	destroyed() {}
};
</script>