<template>

    <div 
    v-bind:class="{
        'checkbox-select' :true,
        'select-dropdown':true,
        'dropdown-with-counter' : true,
        'no-list-item' : checkList.length > 0 ? false : true
      }">
      <!-- {{filterTitle}} -->
        <span class="select-text" @click="handleClick">{{filterTitle}}</span>
    <div class="chk-select-wrap dropdown-option-wrap" data-simplebar @click.stop>


    <ul  class="chk-select-options dropdown-option-list">
        <li 
            v-for="(item , i) in checkList" 
            v-bind:data-id="item[1].id"
            :key="i"           
            >
            <b-form-checkbox name  v-model="items" @click.native ="filterTable" v-bind:value="item[1].id">{{item[1].title}}<span class="counter">{{item[1].mission_count}}</span></b-form-checkbox>
        </li>
    </ul>
   <!--  <ul class="chk-select-options dropdown-option-list" v-else>
        <li>
            <label class="no-checkbox">{{ langauageData.label.no_record_found }}</label>
        </li>
    </ul> -->
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
      if(simplebarOffset != null && window.innerWidth > 991){
         var simplebarOffset_width = parseInt(window.getComputedStyle(simplebarOffset).getPropertyValue("width"));
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
         }
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
          setTimeout(function(){
      var selectDropdown = document.querySelectorAll('.select-dropdown');
      window.addEventListener("resize", function() {
         for(var i=0; i < selectDropdown.length ; i++){
            selectDropdown[i].classList.remove('dropdown-open');
        }
    });
    })
    },
};
</script>