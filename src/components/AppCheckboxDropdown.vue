<template>
    <div class="checkbox-select select-dropdown dropdown-with-counter">
        <span class="select-text" @click="handleClick">{{filterTitle}}</span>
    <div class="chk-select-wrap dropdown-option-wrap" data-simplebar @click.stop @touchend.stop>


    <ul class="chk-select-options dropdown-option-list" v-if="checkList.length > 0">
        <li 
            v-for="(item , i) in checkList" 
            v-bind:data-id="item[1].id"
            :key="i"           
            >
             {{item}}
            <b-form-checkbox name  v-model="items" @click.native ="filterTable" v-bind:value="item[1].id">{{item[1].title}}<span class="counter">{{item[1].mission_count}}</span></b-form-checkbox>
        </li>
    </ul>
    <ul class="chk-select-options dropdown-option-list" v-else>
        <li>
            <label class="no-checkbox">{{ langauageData.label.no_record_found }}</label>
        </li>
    </ul>
    </div>
    </div>
</template>

<script>
import Vue from "vue";
import store from '../store';
export default {
    name: "AppCheckboxDropdown",
    components: {},
    props: {
        filterTitle: String,
        checkList: {
        type: Array,
            default: () => []
        },
        selectedItem: Array,
    },

    data() {
        return {
            items: this.selectedItem,
            langauageData : [],
        };
    },
    mounted() {},
    methods: {
        filterTable() {
            this.$emit("changeParmas");
        },
        handleClick(e) {
      e.stopPropagation();
      setTimeout(function() {
        // console.log(e.target)
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
        // console.log(dropdown_list)
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
    watch: {
        items: function(val){            
            this.$emit("updateCall",val.join(','));
        },
        selectedItem:function(val){
            this.items = this.selectedItem;
        },
    },
    created() {
         this.langauageData = JSON.parse(store.state.languageLabel);
    },
};
</script>