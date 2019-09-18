<template>
	<div class="news-detail-page inner-pages">
		<header>
			<ThePrimaryHeader></ThePrimaryHeader>
		</header>
		<main>
			<b-container>
				<div class="news-detail-container">
					<div class="news-detail-block">
						<h2>Grow Trees – On the path to environment sustainability</h2>
						<h3 class="author-name">Charles Vigue -<sapn>CEO</sapn></h3>
						<p class="publish-date">Published on 23/07/2019</p>
						<div class="news-img-wrap" :style="{backgroundImage: 'url('+bgImg[0]+')'}"></div>
						<div class="news-content cms-content">
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
							<b-row class="more-img">
								<b-col cols="6" class="img-col">
									<div class="news-img-wrap" :style="{backgroundImage: 'url('+bgImg[1]+')'}"></div>
								</b-col>
								<b-col cols="6" class="img-col">
									<div class="news-img-wrap" :style="{backgroundImage: 'url('+bgImg[2]+')'}"></div>
								</b-col>
							</b-row>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
							<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
							<p class="list-title">We use these technologies for a number of purposes, such as:</p>
							<b-list-group>
								<b-list-group-item>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain.</b-list-group-item>
								<b-list-group-item>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi.</b-list-group-item>
								<b-list-group-item>On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized</b-list-group-item>
								<b-list-group-item>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain.</b-list-group-item>
								<b-list-group-item>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore</b-list-group-item>
							</b-list-group>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
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
		};
	},
	mounted() {},
	computed: {},
	methods: {},
	created() {
		this.isNewsDisplay = this.settingEnabled(constants.NEWS_ENABLED);
		if(!this.isNewsDisplay) {
			this.$router.push('/home')
		}
	},
	updated() {}
};
</script>


