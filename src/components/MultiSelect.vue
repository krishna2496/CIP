<template>
    <div>
        <div class="skillset-wrap">
            <ul class="skill-list-wrapper">
                <li>Anthropology</li>
                <li>Archeology</li>
                <li>Astronomy</li>
                <li>Computer Science</li>
                <li>Environmental Science</li>
                <li>History</li>
            </ul>
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
                <b-button @click="$refs.skillModal.hide()" class="btn-borderprimary">{{langauageData.label.cancel}}</b-button>
                <b-button class="btn-bordersecondary">{{langauageData.label.save}}</b-button>
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
    props: {},
    data() {
        return {
            langauageData : [],
            selectedListIndexs: [],
            fromList: [
                { name: "Anthropology", id: 0 },
                { name: "Archeology", id: 1 },
                { name: "Astronomy", id: 2 },
                { name: "Computer Science", id: 3 },
                { name: "Environmental Science", id: 4 },
                { name: "History", id: 5 },
                { name: "Library Sciences", id: 6 },
                { name: "Mathematics", id: 7 },
                { name: "Music Theory", id: 8 },
                { name: "Research", id: 9 },
                { name: "Administrative Support", id: 10 },
                { name: "Customer Service", id: 11 },
                { name: "Data Entry", id: 12 },
                { name: "Executive Admin", id: 13 },
                { name: "Office Management", id: 14 },
                { name: "Office Reception", id: 15 },
                { name: "Program Management", id: 16 },
                { name: "Transactions", id: 17 },
                { name: "Agronomy", id: 18 },
                { name: "Animal Care / Handling", id: 19 },
                { name: "Animal Therapy", id: 20 },
                { name: "Aquarium Maintenance", id: 21 },
                { name: "Botany", id: 22 },
                { name: "Environmental Education", id: 23 },
                { name: "Environmental Policy", id: 24 },
                { name: "Farming", id: 25 }
            ],
            toList: [
                 { name: "Anthropology", id: 0 },
                { name: "Archeology", id: 1 },
                { name: "Astronomy", id: 2 },
                { name: "Computer Science", id: 3 },
            ],
            updated: false,
            selectList: [],
            myclass: ["skill-modal"]
        };
    },
    mounted() {
        var a = this.$refs.skillModal;
    },
    methods: {
        handleclick() {
          var fromlist_group = document.querySelectorAll(".fromlist-group li");
          //   var _btn = document.querySelector(".btn-rightselected");
          for (var i = 0; i < fromlist_group.length; ++i) {
            fromlist_group[i].addEventListener("click", this.handleSelected);
          }
          //   _btn.addEventListener("click", this.addFromList);
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
            setTimeout(() => {
                // this.handleclick();
            });
        },
        hideModal() {
            this.toList = this.toList;
            var tolist_group = document.querySelectorAll(".tolist-group li");
            //   console.log(tolist_group);
        },
        // Add to list
        addToList(id) {
            var _this = this; 
            var filteredObj  = this.fromList.filter(function (item, i) { 
                    if (item.id == id) {
                        _this.fromList.splice(i,1);
                        return item;
                    }
            });
            // this.fromList
            this.toList.push(filteredObj[0])
        },
        // Remove data from to list 
        removeFromToList(id) {
            alert(id);
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
        }
    },

    updated() {
    },
    created() {
        this.langauageData = JSON.parse(store.state.languageLabel);
    }
};
</script>

