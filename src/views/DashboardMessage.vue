<template>
	<div class="dashboard-message inner-pages">
		<header>
			<ThePrimaryHeader></ThePrimaryHeader>
		</header>
		<main>
			<DashboardBreadcrumb />
			<div class="dashboard-tab-content">
				<b-container>
					<div class="heading-section">
						<h1>{{languageData.label.messages}}</h1>
						<b-button title="Send Message" class="btn-bordersecondary"
							@click="$refs.sendMessageModal.show()">{{languageData.label.send}} {{languageData.label.message}} </b-button>
					</div>
					<div class="inner-content-wrap">
						<div class="message-count-block">
							<span class="highlighted-text">(2) {{languageData.label.new}} {{languageData.label.messages}}  </span>
							<span>(3) {{languageData.label.messages}}</span>
						</div>
						<ul class="message-box">
							<li v-for="(message, idx) in messageList" :key="idx">
								<b-button title="Delete message" class="delete-btn">
									<img src="../assets/images/delete-ic.svg" alt="delete" />
								</b-button>
								<div class="title-wrap">
									<h3>{{message.person }}</h3>
									<span class="date-detail">{{message.date}}</span>
								</div>
								<p v-for="(content, index) in message.contentList" :key="index">{{content.text}}</p>
							</li>
						</ul>
					</div>
					<div class="pagination-block" data-aos="fade-up">
						<b-pagination v-model="currentPage" :total-rows="rows" :per-page="perPage" align="center"
							aria-controls="my-cardlist"></b-pagination>
					</div>
				</b-container>
			</div>
		</main>
		<footer>
			<TheSecondaryFooter></TheSecondaryFooter>
		</footer>
		<b-modal :title="languageData.label.send_us_a_message" ref="sendMessageModal" :modal-class="'send-message-modal sm-popup'"
			hide-footer centered>
			<b-form-group class="d-flex">
				<label>{{languageData.label.name}} :</label>
				<p>Andrew Johnson</p>
			</b-form-group>
			<b-form-group class="d-flex">
				<label>{{languageData.label.email}} :</label>
				<p>andrew.johnson@gmail.com</p>
			</b-form-group>
			<b-form-group class="d-flex">
				<label>{{languageData.label.phone}} :</label>
				<p>9343567357</p>
				<p></p>
			</b-form-group>
			<b-form-group>
				<label>{{languageData.label.message}}</label>
				<b-form-textarea id :placeholder="languageData.placeholder.message" size="lg" no-resize rows="5"></b-form-textarea>
			</b-form-group>
			<div class="btn-wrap">
				<b-button class="btn-borderprimary" title="Cancel" @click="$refs.sendMessageModal.hide()">{{languageData.label.cancel}}
				</b-button>
				<b-button class="btn-bordersecondary" title="Send">{{languageData.label.send}}</b-button>
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
		sendMessage
	} from "../services/service";
	import store from '../store';
	export default {
		components: {
			ThePrimaryHeader,
			TheSecondaryFooter,
			DashboardBreadcrumb
		},
		name: "dashboardmessage",

		data() {
			return {
				rows: 25,
				perPage: 5,
				currentPage: 1,
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
				messageList: [{
						person: "Andrew Johnson",
						date: "20/07/2019, 08:30 PM",
						contentList: [{
							text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
						}]
					},
					{
						person: "Charles Brown",
						date: "20/07/2019, 08:30 PM",
						contentList: [{
							text: "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem."
						}]
					},
					{
						person: "Susan Felice",
						date: "20/07/2019, 08:30 PM",
						contentList: [{
								text: "But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure."
							},
							{
								text: "To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure."
							}
						]
					},
					{
						person: "Andrew Johnson",
						date: "20/07/2019, 08:30 PM",
						contentList: [{
							text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
						}]
					},
					{
						person: "Charles Brown",
						date: "20/07/2019, 08:30 PM",
						contentList: [{
							text: "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem."
						}]
					},
					{
						person: "Susan Felice",
						date: "20/07/2019, 08:30 PM",
						contentList: [{
								text: "But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure."
							},
							{
								text: "To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure."
							}
						]
					}
				]
			};
		},
		created() {
			this.languageData = JSON.parse(store.state.languageLabel);
			this.getMessageListing()
		},
		updated() {},
		methods: {
			getMessageListing() {
				messageListing().then(response => {
					// console.log(response);
				})
			}
		}
	};
</script>