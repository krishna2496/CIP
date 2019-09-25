<template>
	<div class="news-detail-page inner-pages">
		<header>
			<ThePrimaryHeader></ThePrimaryHeader>
		</header>
		<main>
			
			<b-container>
				<div class="news-detail-container">
					<div class="news-detail-block" v-if="isContentLoaded">
						<h2>{{newsDetailList.news_content.title}}<b-badge class="status-label">{{newsDetailList.news_category[0]}}</b-badge></h2>
						<h3 class="author-name">{{newsDetailList.user_name}} - <span>{{newsDetailList.user_title}}</span></h3>
						<p class="publish-date" v-if="newsDetailList.published_on != null">{{langauageData.label.published_on}} {{newsDetailList.published_on | formatDate}}</p>
						<div class="news-img-wrap" :style="{backgroundImage: 'url('+newsDetailList.news_image+')'}" v-if="newsDetailList.news_image"></div>
						<div class="news-content cms-content"
						v-bind:class="{'news-img-wrap' : !newsDetailList.news_image}"
						 v-html="newsDetailList.news_content.description">
							
						</div>
					</div>
					
				</div>
			</b-container>
		</main>
	<footer>
		<TheSecondaryFooter></TheSecondaryFooter>
	</footer>
	</div>
</template>
<script>
import constants from '../constant';
import store from '../store';
import {
		newsDetail,
	} from "../services/service";
export default {
	components: {
		ThePrimaryHeader : () => import("../components/Layouts/ThePrimaryHeader"),
		TheSecondaryFooter: () => import("../components/Layouts/TheSecondaryFooter"),
	},
	data() {
		return {
			bgImg: [
				require("@/assets/images/group-img1.png"),
				require("@/assets/images/group-img9.png"),
				require("@/assets/images/group-img6.png")
			],
			isNewsDisplay : true,
			isContentLoaded : false,
			newsDetailList : [],
			langauageData : [],
			newsId : this.$route.params.newsId
		};
	},
	mounted() {},
	computed: {},
	methods: {
		getNewsDetail() {
			newsDetail(this.newsId).then(response => {
				if(response.error == false) {
					this.newsDetailList = response.data
					this.isContentLoaded = true
				} else {
					this.$router.push('/404');
				}
			})
		}
	},
	created() {
		this.langauageData = JSON.parse(store.state.languageLabel);
		this.isNewsDisplay = this.settingEnabled(constants.NEWS_ENABLED);
		if(!this.isNewsDisplay) {
			this.$router.push('/home')
		}
		this.getNewsDetail();
	},
	updated() {}
};
</script>


