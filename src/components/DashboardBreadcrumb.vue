<template>
	<div class="breadcrumb-wrap">
		<b-container>
			<div class="breadcrumb-dropdown-wrap">
				<span class="breadcrumb-current" @click.stop></span>
				<div class="breadcrumb-dropdown">
					<b-breadcrumb>
						<b-breadcrumb-item v-for="(item, idx) in items" :key="idx" :to="item.link" @click.stop>
							{{item.name}}
						</b-breadcrumb-item>
					</b-breadcrumb>
				</div>
			</div>
		</b-container>
	</div>
</template>
<script>
	import {
		setTimeout
	} from "timers";
	import store from "../store";
	export default {
		name: "Breadcrumb",
		props: {
			breadcrumbActive: String
		},
		data() {
			return {
				languageData: [],
				items: [{
						id: 1,
						name: '',
						link: "dashboard"
					},
					{
						id: 2,
						name: '',
						link: "volunteering-history"
					},
					{
						id: 3,
						name: '',
						link: "volunteering-timesheet"
					},
				]
			};
		},
		methods: {
			handleBreadcrumb() {
				if (screen.width < 768) {
					var breadcrumbDropdown = document.querySelector(
						".breadcrumb-dropdown-wrap"
					);
					breadcrumbDropdown.classList.toggle("open");
				}
			}
		},
		created() {
			setTimeout(() => {
				if (document.querySelector(".breadcrumb") != null) {
					var currentDashboard = document.querySelector(
						".breadcrumb .router-link-active"
					).innerHTML;
					this.currentDashboardPage = currentDashboard;
					var currentLink = document.querySelector(".breadcrumb-current");
					currentLink.innerHTML = this.currentDashboardPage;
					var breadcrumbItem = document.querySelectorAll(".breadcrumb-item");
					currentLink.addEventListener("click", this.handleBreadcrumb);
				}
			});
			this.languageData = JSON.parse(store.state.languageLabel);
			this.items[0].name = this.languageData.label.dashboard
			this.items[1].name = this.languageData.label.volunteering_history
			this.items[2].name = this.languageData.label.volunteering_timesheet
		}
	};
</script>