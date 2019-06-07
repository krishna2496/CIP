<template>
  <div v-if="optionList.length > 0" class="custom-dropdown" v-on:touchend.stop>
    <span @click="handleClick">{{default_text}}</span>
    <div class="option-list-wrap" data-simplebar>
      <ul class="option-list">
        <li v-for="item in optionList" v-bind:data-id="item[0]" @click="handleSelect">{{item[1]}}</li>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  name: "customDropdown",
  components: {},
  props: {
    optionList: Array,
    default_text: String
  },
  data() {
    return {
      default_text_val: this.default_text,
    };
  },
  mounted() {
    if (screen.width < 1025) {
      document.addEventListener("touchend", this.onClick);
    } else {
      document.addEventListener("click", this.onClick);
    }
  },
  methods: {
    onClick() {
        var dropdownList = document.querySelectorAll('.dropdown-open');
        for(var i = 0; i < dropdownList.length ; ++i ){
            dropdownList[i].classList.remove('dropdown-open');
        }
    },
    handleClick(e) {
      e.stopPropagation();
      e.target.parentNode.classList.toggle('dropdown-open');
       var dropdownList = document.querySelectorAll('.dropdown-open');
        for(var i = 0; i < dropdownList.length ; ++i ){
            if(dropdownList[i] != e.target.parentNode){
            dropdownList[i].classList.remove('dropdown-open');
            }
        }
    },
    handleSelect(e) {
        var selectedData = []
        selectedData['selectedVal']  = e.target.innerHTML;
        selectedData['selectedId']  = e.target.dataset.id;
         
        this.$emit("updateCall", selectedData);
    }
  },
  beforeDestroy() {
    document.removeEventListener("click", this.onClick);
  }
};
</script>
