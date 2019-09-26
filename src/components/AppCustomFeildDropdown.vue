<template>
	<div v-if="optionList != null && optionList.length > 0" v-bind:class="{
        'custom-dropdown' :true,
        'select-dropdown':true,
        'is-invalid' : errorClass
      }">
		<span class="select-text" @click="handleClick">{{defaultText}}</span>
		<div class="option-list-wrap dropdown-option-wrap " data-simplebar>
			<ul class="option-list dropdown-option-list" v-if="translationEnable == 'false'">
				<li v-for="item in optionList" v-bind:data-id="item.value" @click="handleSelect">{{item.text}}</li>
			</ul>
			<ul class="option-list dropdown-option-list" v-else>
				<li v-for="item in optionList" v-bind:data-id="item.value" @click="handleSelect">
					{{langauageData.label[item.text]}}</li>
			</ul>
		</div>
	</div>
</template>

<script>
	import store from '../store';
	export default {
		name: "AppCustomDropdown",
		components: {},
		props: {
			optionList: Array,
			defaultText: String,
			translationEnable: String,
			errorClass: Boolean,
			fieldId: Number
		},
		data() {
			return {
				defaultTextVal: this.defaultText,
				langauageData: []
			};
		},
		mounted() {},
		methods: {
			handleSelect(e) {
				var selectedData = []
				selectedData['selectedVal'] = e.target.innerHTML;
				selectedData['selectedId'] = e.target.dataset.id;
				selectedData['fieldId'] = this.fieldId;
				this.$emit("updateCall", selectedData);
			},
			handleClick(e) {
				e.stopPropagation();
				var profile_toggle = document.querySelector(
					".profile-menu .dropdown-toggle"
				);
				var profile_menu = document.querySelector(".profile-menu");
				if (profile_menu != null) {
					if (profile_menu.classList.contains("show")) {
						profile_toggle.click();
					}
				}
				var notification_btn = document.querySelector(
					".notification-menu .nav-link .btn-notification"
				);
				var notification_popover = document.querySelector(
					".notification-popover"
				);
				if (notification_popover != null) {
					notification_btn.click();
				}

				e.target.parentNode.classList.toggle("dropdown-open");
				var dropdownList = document.querySelectorAll(".dropdown-open");
				for (var i = 0; i < dropdownList.length; ++i) {
					if (dropdownList[i] != e.target.parentNode) {
						dropdownList[i].classList.remove("dropdown-open");
					}
				}
				var simplebarOffset = e.target.parentNode.querySelector(".simplebar-offset");
				if (simplebarOffset != null && window.innerWidth > 1024) {
					var simplebarOffset_width = parseInt(window.getComputedStyle(simplebarOffset).getPropertyValue(
						"width"));
					var simplebarWrapper = simplebarOffset.parentNode.parentNode;
					simplebarWrapper.style.width = simplebarOffset_width + "px";

					var dropdown_list = e.target.parentNode;
					var dropdown_list_width = parseInt(window.getComputedStyle(dropdown_list).getPropertyValue("width"));
					var optionlist_wrap = dropdown_list.querySelector(".dropdown-option-wrap");
					var optionlist = optionlist_wrap.querySelector(".dropdown-option-list");
					if (optionlist != null) {
						var optionlist_width = parseInt(window.getComputedStyle(optionlist).getPropertyValue("width"));
						var minwidth_style = dropdown_list.querySelector(".simplebar-offset");
						if (dropdown_list_width > optionlist_width) {
							minwidth_style.setAttribute("style", "left: 0 !important");
						}
					}
					 setTimeout(function(){
						var dropdown_list_child = dropdown_list.childNodes[1];
						var optionlist_height = parseInt(window.getComputedStyle(optionlist).getPropertyValue("height"));
						var dropdown_list_height = parseInt(window.getComputedStyle(dropdown_list_child).getPropertyValue("height"));
						var minheight_style = dropdown_list.querySelector(".dropdown-option-wrap");
						if (dropdown_list_height > optionlist_height){
							minheight_style.setAttribute("style", "overflow-x:hidden");
						}
					},500);
				}
			}
		},
		beforeDestroy() {
			document.removeEventListener("click", this.onClick);
		},
		created() {
			this.langauageData = JSON.parse(store.state.languageLabel);
			setTimeout(function () {
				var selectDropdown = document.querySelectorAll('.select-dropdown');
				window.addEventListener("resize", function () {
					for (var i = 0; i < selectDropdown.length; i++) {
						selectDropdown[i].classList.remove('dropdown-open');
					}
				});
			})
		}
	};
</script>