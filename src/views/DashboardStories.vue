<template>
    <div class="dashboard-stories inner-pages">
        <header>
            <ThePrimaryHeader></ThePrimaryHeader>
        </header>
        <main>
            <DashboardBreadcrumb />
            
            <div class="dashboard-tab-content">
                <b-container>
                    <div
                        v-bind:class="{ 'content-loader-wrap': true, 'loader-active': isLoaderActive}">
                        <div class="content-loader"></div>
                    </div>
                    <div class="heading-section">
                        <h1>{{languageData.label.my_stories}}</h1>
                        <b-button type="button" class="btn-bordersecondary" @click="publishNewStory">
                            {{languageData.label.publish_new_story}}
                        </b-button>
                    </div>
                    <div class="dashboard-story-content">
                        <p>
                            {{storyText}}
                        </p>
                    </div>
                    <b-alert show :variant="classVariant" dismissible v-model="showDismissibleAlert">{{ message }}</b-alert>
                   
                    <b-list-group class="status-bar inner-statusbar">
                        <b-list-group-item>
                            <div class="list-item">
                                <i>
                                    <img :src="$store.state.imagePath+'/assets/images/published-ic.svg'" alt />
                                </i>
                                <p>
                                    <span v-if="stats.published"> {{stats.published}}</span><span v-else>0</span>{{languageData.label.published}}
                                </p>
                            </div>
                        </b-list-group-item>
                        <b-list-group-item>
                            <div class="list-item">
                                <i>
                                    <img :src="$store.state.imagePath+'/assets/images/pending-ic.svg'" alt="Pending" />
                                </i>
                                <p>
                                    <span v-if="stats.pending"> {{stats.pending}}</span><span v-else>0</span>{{languageData.label.pending}}
                                </p>
                            </div>
                        </b-list-group-item>
                        <b-list-group-item>
                            <div class="list-item">
                                <i>
                                    <img :src="$store.state.imagePath+'/assets/images/decline.svg'" alt="Decline" />
                                </i>
                                <p>
                                    <span v-if="stats.declined"> {{stats.declined}}</span><span v-else>0</span>{{languageData.label.declined}}
                                </p>
                            </div>
                        </b-list-group-item>
                        <b-list-group-item>
                            <div class="list-item">
                                <i>
                                    <img :src="$store.state.imagePath+'/assets/images/draft.svg'" alt="Draft" />
                                </i>
                                <p>
                                    <span v-if="stats.draft"> {{stats.draft}}</span><span v-else>0</span>{{languageData.label.draft}}
                                </p>
                            </div>
                        </b-list-group-item>
                    </b-list-group>
                    <div class="story-card-wrap">
                        <h2>{{languageData.label.story_history}}</h2>
                        <b-row class="story-card-row" v-if="storyData.length > 0">
                            <b-col class="story-card-block" md="6" lg="4" v-for="(data,index) in storyData" :key=index>
                                <div class="story-img"  :style="{backgroundImage: 'url('+getMediaPath(data)+')'}"></div>
                                <div class="story-card">
                                   
                                    <h4 class="story-card-title">
                                        <b-link
                                            :to="'/story-detail/'+data.story_id"
                                            :title="data.title"
                                            v-if="data.title"
                                            >{{data.title | substring(40)}}
                                        </b-link>
                                    </h4>
                                    <div class="story-card-body">
                                        <span>{{data.created | formatDate}}</span>
                                        <p v-if="data.description" v-html="getDescription(data.description)"></p>
                                    </div>
                                    <div class="story-card-footer">
                                        <span class="status-label" v-if="data.status != ''">{{data.status}}</span>
                                        <div class="action-block">
                                            <b-button class="btn-action" v-b-tooltip.hover :title="languageData.label.delete" v-if="getDeleteAction(data.status)" @click="deleteStory(data.story_id)">
                                                <img :src="$store.state.imagePath+'/assets/images/gray-delete-ic.svg'" alt="Delete" />
                                            </b-button>
                                            <b-link class="btn-action" v-b-tooltip.hover :title="languageData.label.redirect"  :to="'/story-detail/' + data.story_id" v-if="getRedirectAction(data.status)">
                                                <img :src="$store.state.imagePath+'/assets/images/external-link.svg'" alt="Redirect" />
                                            </b-link>
                                            <b-button class="btn-action" v-b-tooltip.hover :title="languageData.label.copy" v-if="getCopyAction(data.status)" @click="copyStory(data.story_id)">
                                                <img :src="$store.state.imagePath+'/assets/images/copy.svg'" alt="Copy" />
                                            </b-button>
                                            <b-link class="btn-action" v-if="getEditAction(data.status)"
                                                :to="'/edit-story/' + data.story_id"
                                                v-b-tooltip.hover :title="languageData.label.edit">
                                                <img :src="$store.state.imagePath+'/assets/images/edit-ic.svg'" alt="Edit" />
                                            </b-link>
                                        </div>
                                    </div>
                                </div>
                            </b-col>
                            
                        </b-row>
                        <div class="pagination-block" data-aos="fade-up" v-if="pagination.totalPages > 1">
                            <b-pagination
                                    v-model="pagination.currentPage"
                                    :total-rows="pagination.total"
                                    :per-page="pagination.perPage"
                                    align="center"
                                    @change="pageChange"
                                    aria-controls="my-cardlist"
                            ></b-pagination>
				        </div>
                        <div class="btn-row" v-if="storyData.length > 0">
                            <b-button class="btn-bordersecondary ml-auto"  @click="exportFile()">{{languageData.label.export}}</b-button>
                        </div>
                    </div>
                   
                </b-container>
            </div>
        </main>
        <footer>
            <TheSecondaryFooter></TheSecondaryFooter>
        </footer>
    </div>
</template>

<script>
    import constants from '../constant';
    import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
    import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
    import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
    import {
        myStory,
        copyStory,
        deleteStory,
    } from "../services/service";
    import ExportFile from "../services/ExportFile";
    import store from '../store';
    export default {
        components: {
            ThePrimaryHeader,
            TheSecondaryFooter,
            DashboardBreadcrumb
        },
        name: "dashboardstories",
        data() {
            return {
                stats : [],
                storyData : [],
                languageData : [],
                pagination : {
					'currentPage' :1,
					"total": 0,
					"perPage": 1,
					"totalPages": 0,
                },
                classVariant: 'danger',
                message: null,
                showDismissibleAlert : false,
                storyText : '',
                isLoaderActive : false
            };
        },
        methods: {
            pageChange(page){
				this.getStoryListing(page);
			},
            getMyStory() {
                this.isLoaderActive = true
                myStory().then(response => {
                    if(response.error == false) {
                        this.stats = response.data.stats
                        this.storyData = response.data.story_data
                        this.pagination.currentPage = response.pagination.current_page
						this.pagination.total = response.pagination.total
						this.pagination.perPage = response.pagination.per_page
						this.pagination.totalPages = response.pagination.total_pages
                    }
                })
                this.isLoaderActive = false
            },
            publishNewStory() {
                this.$router.push({
                    name: 'ShareStory'
                })
            },
            getDescription(description) {       
                let data = description.substring(0,150);
                return data
            },
            getMediaPath(data) {
			    if(data.storyMedia && data.storyMedia.path != '') {
                    let media = data.storyMedia;
                    if (media.type == 'video') {
                        let videoPath = media.path;
                        let videoId = '';
                        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                        let match = videoPath.match(regExp);
                        if (match && match[2].length == 11) {
                            videoId = match[2];
                        }
                        return "https://img.youtube.com/vi/" + videoId + "/mqdefault.jpg";
                    } else {
                        return media.path;
                    }
                } else {
                    return store.state.imagePath+'/assets/images/'+constants.MISSION_DEFAULT_PLACEHOLDER;
                }
            },
            getDeleteAction(status) {
                if(status != '') {
                    return true
                }
            },
            getRedirectAction(status) {
                if(status != '') {
                    if(status == constants.PUBLISHED_STORY || status == constants.PENDING_STORY) {
                        return true
                    } else {
                        return false
                    }
                }
            },
            getCopyAction(status) {
                if(status != '') {
                    if(status == constants.DECLINED_STORY) {
                        return true
                    } else {
                        return false
                    }
                }
            },
            
            getEditAction(status) {
                if(status != '') {
                    if(status == constants.DRAFT_STORY || status == constants.PENDING_STORY) {
                        return true
                    } else {
                        return false
                    }
                }
            },

            deleteStory(storyId) {
                this.isLoaderActive = true
                deleteStory(storyId).then(response => {
                    this.showDismissibleAlert = true
					if (response.error === true) { 
						this.classVariant = 'danger'
						//set error msg
                        this.message = response.message
                        this.isLoaderActive = false
					} else {
						this.classVariant = 'success'
						//set error msg
                        this.message = this.languageData.label.story_deleted
                        this.getMyStory();
					}
                })
            },
            copyStory(storyId) {
                this.isLoaderActive = true
                copyStory(storyId).then(response => {
                    this.showDismissibleAlert = true
					if (response.error === true) { 
						this.classVariant = 'danger'
						//set error msg
                        this.message = response.message
                        this.isLoaderActive = false
					} else {
						this.classVariant = 'success'
						//set error msg
                        this.message = response.message
                        this.getMyStory();
					}
                })
            },
            exportFile() {
                this.isLoaderActive = true
                let fileName = this.languageData.export_timesheet_file_names.MY_STORIES_XLSX
                let exportUrl = "/app/story/export"
                ExportFile(exportUrl,fileName);
                this.isLoaderActive = false
            }
        },

        created() {
            this.getMyStory();
            this.languageData = JSON.parse(store.state.languageLabel);
            let storyArray = JSON.parse(store.state.storyDashboardText)
			if(storyArray) {
				storyArray.filter((data,index) => {
					if(data.lang == store.state.defaultLanguage.toLowerCase()) {
						this.storyText = data.message
					}
				})
			}
        }
    };
</script>