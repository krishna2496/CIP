<template>
    <div>
        <div class="skillset-wrap">
            <b-button class="btn-borderprimary add-skill-btn" @click="showSkillModal">
                {{langauageData.label.add_skills}}
            </b-button>
            <b-modal
                centered
                :title="langauageData.label.add_your_skills"
                ref="skillModal"
                :modal-class="myclass"
                hide-footer
                @hidden="hideModal"
            >
            <b-alert show variant="danger" dismissible v-model="showErrorDiv">
                    {{ message }}
            </b-alert>
            <div class="multiselect-options">
                <div class="options-col" data-simplebar>

                    <ul class="fromlist-group">
                        <li v-for="(fromitem, index) in fromList" :key="index" :id="fromitem.id">
                            <span>{{fromitem.name}}</span>
                            <b-button  @click="addToList(fromitem.id)">
                                <img :src="$store.state.imagePath+'/assets/images/plus-ic.svg'" 
                                alt="plus icon sss"
                            />
                            </b-button>
                        </li>
                    </ul>
                </div>
                <div class="options-col" data-simplebar>
                    <ul class="tolist-group">
                        <li v-for="(toitem, idx) in toList" :id="toitem.id" :key="idx">
                            <span>{{toitem.name}}</span>
                            <b-button @click="removeFromToList(toitem.id)">
                                <img :src="$store.state.imagePath+'/assets/images/cross-ic.svg'" alt="cross icon" />
                            </b-button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="btn-wrap">
                <b-button @click="resetSkill" class="btn-borderprimary">{{langauageData.label.cancel}}</b-button>
                <b-button @click="saveSkill" class="btn-bordersecondary">{{langauageData.label.save}}</b-button>
            </div>
            </b-modal>
           
        </div>
    </div>
</template>
<script>
import { setTimeout } from "timers";
import store from "../store";

export default {
    name: "Multiselect",
    props: {
        fromList:Array,
        toList : Array,
    },
    data() {
        return {
            langauageData : [],
            selectedListIndexs: [],
            updated: false,
            selectList: [],
            myclass: ["skill-modal"],
            showErrorDiv : false,
            message : ''
        };
    },
    mounted() {
        var a = this.$refs.skillModal;
    },
    methods: {
        handleclick() {
          var fromlist_group = document.querySelectorAll(".fromlist-group li");
          for (var i = 0; i < fromlist_group.length; ++i) {
            fromlist_group[i].addEventListener("click", this.handleSelected);
          }
        },
        showSkillModal: function() {
            var _this = this
            var filteredObj  = this.toList.filter(function (toItem, toIndex) { 
                var filteredObj  = _this.fromList.filter(function (fromItem, fromIndex) { 
                    if(toItem.id == fromItem.id) {
                        _this.fromList.splice(fromIndex,1);
                    }
                });    
            });
            this.$refs.skillModal.show();
        },
        hideModal() {
            this.toList = this.toList;
            var tolist_group = document.querySelectorAll(".tolist-group li");
        },
        // Add to list
        addToList(id) {
            var _this = this; 
            if(this.toList.length <= 14) {
                var filteredObj  = this.fromList.filter(function (item, i) { 
                        if (item.id == id) {
                            _this.fromList.splice(i,1);
                            return item;
                        }
                });
                this.toList.push(filteredObj[0])
                this.showErrorDiv = false
            } else {
                this.showErrorDiv = true,
                this.message = this.langauageData.errors.max_skill_selection
            }
        },

        // Remove data from to list 
        removeFromToList(id) {
            var _this = this;
            var filteredObj  = this.toList.filter(function (item, i) { 
                    if (item.id == id) {
                        _this.toList.splice(i,1);
                        return item;
                    }
            });
            // this.fromList
            this.fromList.push(filteredObj[0])  
            this.fromList.sort();  
            this.fromList.sort(function(first, next) { 
                first = first.id;
                next = next.id;
                return first < next ? -1 : (first > next ? 1 : 0);
            });
        },
        resetSkill() {
            this.fromList = [];
            this.toList = [],
            this.$emit("resetData");
        },
        saveSkill(){
            this.$emit("saveSkillData",this.toList);
            this.$refs.skillModal.hide();
        }
    },

    updated() {
    },
    watch : {
       
    },
    created() {
        this.langauageData = JSON.parse(store.state.languageLabel);

    }
};
</script>

