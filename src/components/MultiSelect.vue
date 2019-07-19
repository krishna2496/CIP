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
      <b-button class="btn-borderprimary add-skill-btn" @click="showSkillModal">Add Skills</b-button>
      <b-modal
        centered
        title="Add your Skills"
        ref="skillModal"
        :modal-class="myclass"
        hide-footer
        @hidden="hideModal"
      >
        <!-- <b-button @click="$refs.skillModal.hide()" class="btn-cross">
          <img src="../assets/images/cross-ic.svg" alt />
        </b-button>-->
        <div class="multiselect-options">
          <div class="options-col" data-simplebar>
            <ul class="fromlist-group">
              <li
                v-for="(fromitem, index) in fromList"
                :key="index"
                :id="fromitem.id"
              >{{fromitem.name}}</li>
            </ul>
          </div>
          <div class="action-col">
            <b-button class="btn-rightselected">
              <i>
                <img src="../assets/images/next-arrow.svg" alt />
              </i>
            </b-button>
            <b-button class="btn-leftselected">
              <i>
                <img src="../assets/images/back-arrow.svg" alt />
              </i>
            </b-button>
          </div>
          <div class="options-col" data-simplebar>
            <ul class="tolist-group">
              <li v-for="(toitem, idx) in toList" :id="toitem.id" :key="idx">{{toitem.name}}</li>
            </ul>
          </div>
        </div>
        <div class="btn-wrap">
          <b-button @click="$refs.skillModal.hide()" class="btn-borderprimary">Cancel</b-button>
          <b-button class="btn-bordersecondary">Save</b-button>
        </div>
      </b-modal>
      <div class="btn-wrapper">
        <b-button class="btn-bordersecondary">Save</b-button>
      </div>
    </div>
  </div>
</template>
<script>
import { setTimeout } from "timers";
export default {
  name: "Multiselect",
  props: {},
  data() {
    return {
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
        { name: "Office Management", id: 4 },
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
      toList: [],
      updated: false,
      selectList: [],
      myclass: ["skill-modal"]
    };
  },
  mounted() {
    var a = this.$refs.skillModal;
    //   console.log(a)
    //    a.on("hidden.bs.modal",function(){
    //        console.log("modal hide called");
    //    })
  },
  methods: {
    handleclick() {
      var fromlist_group = document.querySelectorAll(".fromlist-group li");
      var _btn = document.querySelector(".btn-rightselected");
      for (var i = 0; i < fromlist_group.length; ++i) {
        fromlist_group[i].addEventListener("click", this.handleSelected);
      }
      _btn.addEventListener("click", this.addFromList);
    },

    handleclick1() {
      var tolist_group = document.querySelectorAll(".tolist-group li");
      var _btn1 = document.querySelector(".btn-leftselected");
      for (var i = 0; i < tolist_group.length; ++i) {
        tolist_group[i].addEventListener("click", this.handleSelected1);
      }
      _btn1.addEventListener("click", this.addFromList1);
    },

    handleSelected(e) {
      var fromlist_group = document.querySelectorAll(".fromlist-group li");
      var _this = this;
      e.target.classList.toggle("selected");
      if (
        e.target.classList.contains("selected") &&
        !_this.selectList.includes(_this.fromList[e.target.id])
      ) {
        _this.selectList.push(_this.fromList[e.target.id]);
      } else {
        var idx = _this.selectList.indexOf(_this.fromList[e.target.id]);
        _this.selectList.splice(idx, 1);
      }
    },
    handleSelected1(e) {
      var fromlist_group = document.querySelectorAll(".fromlist-group li");
      var _this = this;
      e.target.classList.toggle("selected");
      if (
        e.target.classList.contains("selected") &&
        !_this.selectList.includes(_this.fromList[e.target.id])
      ) {
        _this.selectList.push(_this.fromList[e.target.id]);
      } else {
        var idx = _this.selectList.indexOf(_this.fromList[e.target.id]);
        _this.selectList.splice(idx, 1);
      }
    },
    showSkillModal: function() {
      this.$refs.skillModal.show();
      setTimeout(() => {
        this.handleclick();
      });
    },

    addFromList(e) {
      if (e.target.closest(".fromlist-group") != "null") {
        var fromlist_group = document.querySelectorAll(".fromlist-group li");
        this.toList = this.toList.concat(this.selectList);
        for (var j = 0; j < fromlist_group.length; ++j) {
          for (var i = 0; i < this.selectList.length; ++i) {
            if (this.selectList[i].id == fromlist_group[j].id) {
              fromlist_group[j].remove();
            }
          }
        }

        this.selectList = [];
      }
    },
    addFromList1() {
      //  if(e.target.closest('.tolist-group') != 'null'){
      var tolist_group = document.querySelectorAll(".tolist-group li");
      this.fromList = this.fromList.concat(this.selectList);
      for (var j = 0; j < tolist_group.length; ++j) {
        for (var i = 0; i < this.selectList.length; ++i) {
          if (this.selectList[i].id == tolist_group[j].id) {
            tolist_group[j].remove();
          }
        }
      }

      this.selectList = [];
      // }
    },
    hideModal() {
      this.toList = this.toList;
      var tolist_group = document.querySelectorAll(".tolist-group li");
    }
  },

  updated() {
    setTimeout(() => {
      this.handleclick1();
      this.updated = true;
      var fromlist_group = document.querySelectorAll(".fromlist-group li");
      var _btn = document.querySelector(".btn-rightselected");
      var _this = this;
      for (var i = 0; i < fromlist_group.length; ++i) {
        fromlist_group[i].removeEventListener("click", _this.handleSelected);
        fromlist_group[i].addEventListener("click", _this.handleSelected);
      }
      _btn.removeEventListener("click", this.addFromList);
      _btn.addEventListener("click", this.addFromList);
    });
  }
};
</script>

