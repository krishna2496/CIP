<template>
	<div class="breadcrumb-wrap">
		<b-container>
			<div class="breadcrumb-dropdown-wrap">
				<span class="breadcrumb-current" @touchend.stop></span>
				<div class="breadcrumb-dropdown">
					<b-breadcrumb>
						<b-breadcrumb-item v-for="(item, idx) in items" :key="idx" :to="item.link" @touchend.stop>
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
				langauageData: [],
				dashboard : '',
				items: [{
						id: 1,
						name: dashboard,
						link: "dashboard"
					},
					{
						id: 2,
						name: "Volunteering History",
						link: "volunteering-history"
					},
					{
						id: 3,
						name: "Volunteering Timesheet",
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
			this.langauageData = JSON.parse(store.state.languageLabel);
			this.dashboard  = langauageDat.label.dashboard 
		}
	};
</script>