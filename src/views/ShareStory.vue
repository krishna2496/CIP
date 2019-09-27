<template>
	<div class="share-stories-page inner-pages">
		<header>
			<ThePrimaryHeader></ThePrimaryHeader>
		</header>
		<main>
			<b-container>
				<h1>{{languageData.label.share_your_story}}</h1>
				<b-row class="story-form-wrap">
					<b-col xl="8" lg="7" class="left-col">
						<div class="story-form">
							<b-form>
								<b-form-group>
									<label for>{{languageData.label.my_story_title}}</label>
									<b-form-input id type="text" placeholder="Enter story title"></b-form-input>
								</b-form-group>
								<b-row>
									<b-col md="12">
										<b-form-group>
											<label for>{{languageData.label.select_mission}}</label>
											<AppCustomDropdown 
											:optionList="missionTitle" 
											@updateCall="updateMissionTitle" 
											translationEnable="false"
											:defaultText="defaultMissionTitle"
											/>
									    </b-form-group>
									</b-col>
									<!-- <b-col md="6">
										<b-form-group>
											<label for>Date</label>
											<date-picker v-model="time1" valuetype="format" :first-day-of-week="1"
												:lang="lang"></date-picker>
											
										</b-form-group>
									</b-col> -->
								</b-row>
								<b-form-group>
									<label for>{{languageData.label.my_story}}</label>
									<b-form-textarea id placeholder="Enter your stories" size="lg" rows="25"
										class="text-editor"></b-form-textarea>
								</b-form-group>
							</b-form>
						</div>
						<div class="btn-row">
							<b-button class="btn-borderprimary" title="Cancel">{{languageData.label.cancel}}</b-button>
						</div>
					</b-col>
					<b-col xl="4" lg="5" class="right-col">
						<div class="story-form">
							<b-form-group>
								<label for>{{languageData.label.enter_video_url}}</label>
								<b-form-textarea id placeholder="Enter your stories" size="lg" rows="5">
								</b-form-textarea>
							</b-form-group>
							<b-form-group>
								<label for>{{languageData.label.upload_your_photos}}</label>
								<file-upload class="btn" extensions="gif,jpg,jpeg,png,webp"
									accept="image/png, image/gif, image/jpeg, image/webp" :multiple="true" :drop="true"
									:drop-directory="true" :size="1024 * 1024 * 10" v-model="files"
									@input-filter="inputFilter" ref="upload">{{languageData.label.drag_and_drop_pictures}}</file-upload>
							</b-form-group>
							<div class="uploaded-block">
								<div class="uploaded-file-details" v-for="(file, index) in files" :key="index">
									<span v-if="file.thumb" class="image-thumb">
										<img :src="file.thumb" width="40" height="auto" />
										<b-button type="button" @click.prevent="remove(file)" class="remove-btn">
											<img :src="$store.state.imagePath+'/assets/images/cross-ic-white.svg'" alt />
										</b-button>
									</span>
									<span v-else>{{languageData.label.no_image}}</span>
								</div>
							</div>
						</div>
						<div class="btn-row">
							<b-button class="btn-borderprimary">{{languageData.label.preview}}</b-button>
							<b-button class="btn-bordersecondary">{{languageData.label.save}}</b-button>
							<b-button class="btn-bordersecondary btn-submit">{{languageData.label.submit}}</b-button>
						</div>
					</b-col>
				</b-row>
			</b-container>
		</main>
		<footer>
			<TheSecondaryFooter></TheSecondaryFooter>
		</footer>
	</div>
</template>
<script>
	import AppCustomDropdown from "../components/AppCustomDropdown";
	import FileUpload from "vue-upload-component";
	import DatePicker from "vue2-datepicker";
	import store from '../store';
	import constants from '../constant';

	export default {
		components: {
			ThePrimaryHeader : () => import("../components/Layouts/ThePrimaryHeader"),
			TheSecondaryFooter: () => import("../components/Layouts/TheSecondaryFooter"),
			AppCustomDropdown,
			FileUpload,
			DatePicker
		},
		data() {
			return {
				languageData : [],
				isStoryDisplay : true,
				defaultMissionTitle: "",
				missionTitle: [
					["title1","Mission title1"],
					["title2","Mission title2"],
					["title3","Mission title3"],
					["title4","Mission title4"]
				],
				files: [],
				time1: "",
				lang: {
					days: [" Sun ", " Mon ", " Tue ", " Wed ", " You ", " Fri ", " Sat "],
					months: [
						"Jan",
						"Feb",
						"Mar",
						"Apr",
						"May",
						"Jun",
						"Jul",
						"Aug",
						"Sep",
						"Oct",
						"Nov",
						"Dec"
					],
					pickers: [
						"next 7 days",
						"next 30 days",
						"previous 7 days",
						"previous 30 days"
					],
					placeholder: {
						date: "Select date",
						dateRange: "Select Date Range"
					}
				}
			};
		},
		mounted() {},
		computed: {},
		methods: {
			updateMissionTitle(value) {
				this.defaultMissionTitle = value;
			},
			inputFilter(newFile, prevent) {
				console.log(newFile);
				if (newFile) {
					if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
						return prevent();
					}
					// Filter php html js file
					if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
						return prevent();
					}
				}
				if (newFile) {
					// Create a blob field
					newFile.blob = "";
					let URL = window.URL || window.webkitURL;
					if (URL && URL.createObjectURL) {
						newFile.blob = URL.createObjectURL(newFile.file);
					}
					// Thumbnails
					newFile.thumb = "";
					if (newFile.blob && newFile.type.substr(0, 6) === "image/") {
						newFile.thumb = newFile.blob;
					}
				}
			},
			remove(file) {
				this.$refs.upload.remove(file);
			}
		},
		created() {
			this.languageData = JSON.parse(store.state.languageLabel);
			this.isStoryDisplay = this.settingEnabled(constants.STORIES_ENABLED);
			if(!this.isStoryDisplay) {
				this.$router.push('/home')
			}
			this.defaultMissionTitle =  this.languageData.label.mission_title
		},
		updated() {}
	};
</script>