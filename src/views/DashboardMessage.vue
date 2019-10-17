<template>
	<div class="dashboard-message inner-pages">
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
					<div v-if="showErrorDiv">
						<b-alert show variant="danger" dismissible v-model="showErrorDiv">
							{{ message }}
						</b-alert>
					</div>
					<div v-else>
						<div>
							<div class="heading-section">
								<h1>{{languageData.label.messages}}</h1>
								<b-button title="Send Message" class="btn-bordersecondary"
									@click="$refs.sendMessageModal.show()">{{languageData.label.send}} {{languageData.label.message}} </b-button>
							</div>
						</div>
							<div class="inner-content-wrap">
								<div class="message-count-block">
									<span class="highlighted-text" v-if="newMessage > 1">({{newMessage}}) {{languageData.label.new}} {{languageData.label.messages | firstLetterSmall}}</span>
									<span class="highlighted-text" v-else>({{newMessage}}) {{languageData.label.new}} {{languageData.label.message | firstLetterSmall}}  </span>
									<span v-if="messageCount > 1">({{messageCount}}) {{languageData.label.total}} {{languageData.label.messages | firstLetterSmall}}</span>
									<span v-else>({{messageCount}}) {{languageData.label.message | firstLetterSmall}}</span>
								</div>
								<ul class="message-box" v-if="messageList.length > 0">
									<li v-for="(message, idx) in messageList" :key="idx" v-bind:class="{'new-message' :message.is_read == 0}" @click="readMessages(message.messageId,message.is_read)">
										<b-button :title="languageData.label.delete" class="delete-btn">
											<img :src="$store.state.imagePath+'/assets/images/delete-ic.svg'" @click="deleteMessage(message.messageId)" alt="delete" />
										</b-button>
										<div class="title-wrap">
											<h3>{{message.person }}</h3>
											<span class="date-detail">{{message.date | formatDateTime}}</span>
										</div>
										<p>{{message.text}}</p>
									</li>
								</ul>
								<ul v-else class="text-center">
									<h2>{{languageData.label.message}} {{languageData.label.not_found}}</h2>
								</ul>
							</div>
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
						
					</div>
				</b-container>
			</div>
		</main>
		<footer>
			<TheSecondaryFooter></TheSecondaryFooter>
		</footer>
		<b-modal  ref="sendMessageModal" :modal-class="'send-message-modal sm-popup'"
			hide-footer centered>
			<template slot="modal-header" slot-scope="{ close }">
				<i class="close" @click="close()" v-b-tooltip.hover :title="languageData.label.close"></i>
				<h5 class="modal-title">{{languageData.label.send_us_a_message}}</h5>
			</template>
			<b-alert show :variant="classVariant" dismissible v-model="showMessageErrorDiv">
				{{ sendMessage }}
			</b-alert>
			<b-form-group class="d-flex">
				<label>{{languageData.label.name}} :</label>
				<p>{{$store.state.firstName}} {{$store.state.lastName}}</p>
			</b-form-group>
			<b-form-group class="d-flex">
				<label>{{languageData.label.email}} :</label>
				<p>{{$store.state.email}}</p>
			</b-form-group>
			<b-form-group>
				<label>{{languageData.label.subject}}</label>
				<b-form-input id 
					v-model.trim="contactUs.subject" 
					maxLength="255"
					:class="{ 'is-invalid': submitted && $v.contactUs.subject.$error }"
					type="text" :placeholder="languageData.placeholder.subject">
				</b-form-input>
				<div v-if="submitted && !$v.contactUs.subject.required" class="invalid-feedback">
					{{ languageData.errors.subject_required }}
				</div>
			</b-form-group>
			<b-form-group>
				<label>{{languageData.label.message}}</label>
				<b-form-textarea id :placeholder="languageData.placeholder.message" 
				v-model.trim="contactUs.message" 
				:class="{ 'is-invalid': submitted && $v.contactUs.message.$error }"
				size="lg" no-resize rows="5"></b-form-textarea>
				<div v-if="submitted && !$v.contactUs.message.required" class="invalid-feedback">
					{{ languageData.errors.message_required }}
				</div>
			</b-form-group>
			<div class="btn-wrap">
				<b-button class="btn-borderprimary"  @click="$refs.sendMessageModal.hide()">{{languageData.label.cancel}}
				</b-button>
				<b-button class="btn-bordersecondary" v-bind:class="{disabled : isAjaxCall}" @click="submitContact">{{languageData.label.send}}</b-button>
			</div>
		</b-modal>
	</div>
</template>

<script>
	import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
	import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
	import DashboardBreadcrumb from "../components/DashboardBreadcrumb";
	import {
		deleteMessage,
		messageListing,
		contactUs,
		readMessage
	} from "../services/service";
	import {
        required,
        email,
        numeric,
        minLength
    } from 'vuelidate/lib/validators';
	import store from '../store';
	export default {
		components: {
			ThePrimaryHeader,
			TheSecondaryFooter,
			DashboardBreadcrumb
		},
		name: "dashboardmessage",
		validations: {
            contactUs: {
                message: {
                    required
				},
				subject : {
					required
				}
            }
        },
		data() {
			return {
				languageData : [],
                pagination : {
					'currentPage' :1,
					"total": 0,
					"perPage": 1,
					"totalPages": 0,
                },
				classVariant: 'danger',
				isLoaderActive:true,
				newMessage : 0,
				messageCount : 0,
				messageList:[],
				sendMessage : '',
				showErrorDiv : false,
				message : '',
				contactUs: {
					'message': '',
					'subject' : ''
				},
				submitted :false,
				showMessageErrorDiv : false,
				isAjaxCall :false,
				name : '',
				email:''
			};
		},
		created() {
			this.languageData = JSON.parse(store.state.languageLabel);
			this.getMessageListing()
		},
		updated() {},
		methods: {
			pageChange(page){
				this.pagination.currentPage = page
				this.getMessageListing();
			},
			makeToast(variant = null, message) {
				this.$bvToast.toast(message, {
					variant: variant,
					solid: true,
					autoHideDelay: 3000
				})
			},
			submitContact() {
                this.submitted = true;
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return
                }
                this.isAjaxCall = true;
                let contactData = {
                    'subject' : '',
                    'message' : '',
                    'admin' : null
                }
				contactData.message = this.contactUs.message;
				contactData.subject = this.contactUs.subject;
                contactUs(contactData).then(response => {
                    this.showMessageErrorDiv = true
                    this.isAjaxCall = false;
                    if(response.error == false) {
						this.classVariant = 'success';
						this.sendMessage = response.message
						this.contactUs.message =  ''
						this.contactUs.subject =  ''
						this.submitted = false;
                        this.$v.$reset();
                    } else {
                        this.classVariant = 'danger';
                        this.sendMessage = response.message
                        contactUs.subject =  ''
                        contactUs.sendMessage =  ''
                    }
                })
			},
			
			getMessageListing() {
				this.isLoaderActive = true;
				messageListing(this.pagination.currentPage).then(response => {
					this.messageList =[];
					if(response.error == false) {
						if(response.data) {
							let data = response.data
							this.messageCount = data.length
							let count = 0;
							data.filter((data,index) => {
								if(data.is_read == 0) {
									count++;
								}
								let name = ''
								if(data.is_anonymous == 1) {
									name = this.languageData.label.anonymous_user
								} else {
									name = data.admin_name
								}
								this.messageList.push({
									'person' : name,
									'date' : data.created_at,
									'text' : data.message,
									'messageId' : data.message_id,
									'is_read' : data.is_read 
								})
								if(response.pagination) {
									this.pagination.currentPage = response.pagination.current_page
									this.pagination.total = response.pagination.total
									this.pagination.perPage = response.pagination.per_page
									this.pagination.totalPages = response.pagination.total_pages
								}
							})
							this.newMessage = count
						}	
					} else {
						this.showErrorDiv = true;
						this.message = response.message
					}
					this.isLoaderActive = false
				})
			},

			deleteMessage(messageId) {
				this.isLoaderActive = true
				console.log("in delete ");
				deleteMessage(messageId).then(response => {
					let variant = 'success'
					let message = '';
					if(response.error == true) {
						variant = 'danger';
						this.isLoaderActive = false
						message = response.message
					} else {
						message = this.languageData.label.comment + ' ' + this.languageData.label.deleted_successfully
						this.getMessageListing();
					}
					this.makeToast(variant,message)
				})
			},

			readMessages(messageId,isRead) {
				console.log("in read");
				if(isRead == 0) {
				// readMessage(messageId).then(response => {
				// 	if(response.error == false) {
				// 		this.getMessageListing();
				// 	}
				// })
				}
			}
		}
	};
</script>