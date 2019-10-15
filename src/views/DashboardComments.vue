<template>
	<div class="dashboard-comments inner-pages">
		<header>
			<ThePrimaryHeader></ThePrimaryHeader>
		</header>
		<main>
			<DashboardBreadcrumb />
			<div v-bind:class="{ 'content-loader-wrap': true, 'loader-active': isLoaderActive}">
				<div class="content-loader"></div>
			</div>
			<div class="dashboard-tab-content">
				<b-container>
					<div class="heading-section">
						<h1>{{languageData.label.comments}}</h1>
					</div>
					<b-alert show :variant="classVariant" dismissible v-model="showDismissibleAlert">{{ message }}
					</b-alert>
					<div class="inner-content-wrap">
						<b-list-group class="status-bar inner-statusbar">
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/published-ic.svg'" alt />
									</i>
									<p>
										<span>{{statsField.published}}</span>{{languageData.label.published}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/pending-ic.svg'"
											alt="Pending" />
									</i>
									<p>
										<span>{{statsField.pending}}</span>{{languageData.label.pending}}
									</p>
								</div>
							</b-list-group-item>
							<b-list-group-item>
								<div class="list-item">
									<i>
										<img :src="$store.state.imagePath+'/assets/images/decline-ic.svg'"
											alt="Decline" />
									</i>
									<p>
										<span>{{statsField.declined}}</span>{{languageData.label.declined}}
									</p>
								</div>
							</b-list-group-item>
						</b-list-group>
						<div class="dashboard-table" v-if="commentItems.length > 0">
							<div class="table-outer">
								<div class="table-inner">
									<h3>{{languageData.label.comment_history}}</h3>
									<b-table :items="commentItems" responsive :fields="commentfields"
										class="history-table">
										<template slot="mission" slot-scope="data">
											<b-link to="/home" class="table-link">{{ data.item.Mission }}</b-link>
										</template>
										<template slot="date" slot-scope="data">
											{{ data.item.Date | formatDate }}
										</template>
										<template slot="comment" slot-scope="data">
											{{ data.item.Comment }}
										</template>
										<template slot="status" slot-scope="data">
											{{ data.item.Status }}
										</template>
										<template slot="actions" slot-scope="data">
											<b-button class="btn-action btn-expand">
												<img :src="$store.state.imagePath+'/assets/images/expand-ic.svg'"
													alt="Expand" />
											</b-button>
											<b-button class="btn-action" @click="deleteComments(data.item.comment_id)">
												<img :src="$store.state.imagePath+'/assets/images/gray-delete-ic.svg'"
													alt="Delete" />
											</b-button>
										</template>
									</b-table>
								</div>
								<div class="btn-row">
									<b-button class="btn-bordersecondary ml-auto" @click="exportFile()">
										{{languageData.label.export}}</b-button>
								</div>
							</div>
						</div>
					</div>
					<!-- <b-modal ref="my-modal" :modal-class="'table-expand-modal table-modal'">
						<template slot="modal-title">Help old people</template>
						<h4>
							Date:
							<span>4/28/2018</span>
						</h4>
						<h4>
							Status:
							<span>Published</span>
						</h4>
						<div class="mission-row">
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
								ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
								ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
								reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur
								sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id
								est laborum.</p>
						</div>
						<template slot="modal-footer" slot-scope="{ cancel}">
							<b-button class="btn-bordersecondary" @click="cancel()">Cancel</b-button>
						</template>
					</b-modal> -->
				</b-container>
			</div>
		</main>
		<footer>
			<TheSecondaryFooter></TheSecondaryFooter>
		</footer>
	</div>
</template>

<script>
	import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
	import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
	import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
	import store from '../store';
	import ExportFile from "../services/ExportFile";
	import {
		commentListing,
		deleteComment
	} from "../services/service";
	export default {
		components: {
			ThePrimaryHeader,
			TheSecondaryFooter,
			DashboardBreadcrumb
		},

		name: "dashboardcomments",

		data() {
			return {
				selectedMonth: false,
				commentfields: [{
						key: "mission",
						class: "mission-col",
						label: ""
					},
					{
						key: "date",
						class: "date-col",
						label: ""
					},
					{
						key: "comment",
						class: "expand-col",
						label: ""
					},
					{
						key: "status",
						class: "status-col",
						label: ""
					},
					{
						key: "actions",
						class: "action-col",
						label: ""
					}
				],
				commentItems: [],
				statsField: {
					'published': 0,
					'pending': 0,
					'declined': 0
				},
				languageData: [],
				classVariant: 'danger',
				message: null,
				showDismissibleAlert: false,
				isLoaderActive: true
			};
		},
		methods: {
			getCommentListing() {
				this.isLoaderActive = true
				this.showDismissibleAlert = false;
				commentListing().then(response => {
					if (response.error == false) {
						if (response.data && response.data.comments) {
							let mission = this.languageData.label.mission;
							let date = this.languageData.label.date;
							let comment = this.languageData.label.comment;
							let status = this.languageData.label.status;
							let data = response.data.comments
							let currentData = [];
							if (data.length > 0) {
								data.filter((item) => {
									currentData.push({
										[mission]: item.title,
										[date]: item.created_at,
										[comment]: item.comment,
										[status]: item.approval_status,
										['comment_id']: item.comment_id
									})

								})
								this.commentItems = currentData
							}
							if (response.data.stats) {
								this.statsField.published = response.data.stats[0].published;
								this.statsField.pending = response.data.stats[0].pending;
								this.statsField.declined = response.data.stats[0].declined;
							}
						}
						this.isLoaderActive = false
					} else {
						this.showDismissibleAlert = true;
						this.classVariant = 'danger'
						//set error msg
						this.message = response.message
						this.getMyStory();
					}
				})
			},
			deleteComments(commentId) {
				this.showDismissibleAlert = false;
				this.isLoaderActive = true
				deleteComment(commentId).then(response => {
					this.showDismissibleAlert = true;
					if (response.error == false) {
						this.classVariant = 'success'
						//set error msg
						this.message = this.languageData.label.comment + ' ' + this.languageData.label
							.deleted_successfully
					}
					this.isLoaderActive = false
				});
			},
			exportFile() {
				this.isLoaderActive = true
				let fileName = this.languageData.export_timesheet_file_names.COMMENT_LISTING_XLSX
				let exportUrl = "/app/dashboard/comments/export"
				ExportFile(exportUrl, fileName);
				this.isLoaderActive = false
			}
		},
		created() {
			this.languageData = JSON.parse(store.state.languageLabel);
			setTimeout(() => {
				var buttonExpand = document.querySelectorAll(".btn-expand");
				buttonExpand.forEach(function (event) {
					event.addEventListener("click", function () {
						var getcommentCell = this.parentNode.parentNode.childNodes[2];

						var getcommenthtml = getcommentCell.innerHTML;
						var strlenght = getcommenthtml.length;
						var rowParent = this.parentNode.parentNode.parentNode;
						var rowSibling = rowParent.childNodes;
						if (strlenght > 30) {
							getcommentCell.classList.toggle("remove-truncate");
						}
						for (var i = 0; i < rowSibling.length; i++) {
							var siblingChild = rowSibling[i].childNodes;
							for (var j = 0; j < siblingChild.length; j++) {
								if (siblingChild[j] != getcommentCell) {
									siblingChild[j].classList.remove("remove-truncate");
								}
							}
						}
					});
				});
			}, 2000);
			this.commentfields[0].label = this.languageData.label.mission
			this.commentfields[1].label = this.languageData.label.date
			this.commentfields[2].label = this.languageData.label.comment
			this.commentfields[3].label = this.languageData.label.status
			this.commentfields[4].label = this.languageData.label.action

			this.getCommentListing()
		}
	};
</script>