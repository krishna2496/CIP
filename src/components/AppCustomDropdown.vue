<template>

    <div v-if="optionList != null && optionList.length > 0" 
      v-bind:class="{
        'custom-dropdown' :true,
        'select-dropdown':true
      }"
      >
        <span class="select-text" @click="handleClick">{{defaultText}}</span>
        <div class="option-list-wrap dropdown-option-wrap " data-simplebar>
            <ul class="option-list dropdown-option-list" v-if="translationEnable == 'false'">
                <li
                    v-for="item in optionList"
                    v-bind:data-id="item[0]"
                    @click="handleSelect"
                    @touchend="handleSelect"
                >{{item[1]}}</li>
            </ul>
            <ul class="option-list dropdown-option-list" v-else>
                <li
                    v-for="item in optionList"
                    v-bind:data-id="item[0]"
                    @click="handleSelect"
                    @touchend="handleSelect">{{langauageData.label[item[1]]}}</li>
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
        translationEnable : String
    },
    data() {
        return {
            defaultTextVal: this.defaultText,
            langauageData : []
        };
    },
    mounted() {
    },
    methods: {
        handleSelect(e) {
            var selectedData = []
            selectedData['selectedVal']  = e.target.innerHTML;
            selectedData['selectedId']  = e.target.dataset.id;
            this.$emit("updateCall", selectedData);
        },
        handleClick(e) {
      e.stopPropagation();
      setTimeout(function() {
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
        var dropdown_list = document.querySelectorAll(".select-dropdown");
        dropdown_list.forEach(function(e) {
          var dropdown_list_width = parseInt(
            window.getComputedStyle(e).getPropertyValue("width")
          );
          var optionlist_wrap = e.querySelector(".dropdown-option-wrap");
          var optionlist_wrap_height = parseInt(
            window
              .getComputedStyle(optionlist_wrap)
              .getPropertyValue("max-height")
          );
          var optionlist = optionlist_wrap.querySelector(
            ".dropdown-option-list"
          );
          var optionlist_height = optionlist.offsetHeight;
          var optionlist_width = parseInt(
            window.getComputedStyle(optionlist).getPropertyValue("width")
          );
          if (optionlist_wrap_height < optionlist_height) {
            var minwidth_style = e.querySelector(".simplebar-offset");
            minwidth_style.setAttribute("style", "left: 0 !important");
            if (
              optionlist_wrap_height < optionlist_height &&
              dropdown_list_width < optionlist_width
            ) {
              minwidth_style.setAttribute("style", "left: auto !important");
            }
          }
        });
      });
    }
    },
    beforeDestroy() {
        document.removeEventListener("click", this.onClick);
    },
    created() {
        this.langauageData = JSON.parse(store.state.languageLabel);
    }
};
</script>

