<template>
  <div v-if="optionList.length > 0" class="custom-dropdown">
    <span class="select-text">{{default_text}}</span>
    <div class="option-list-wrap" data-simplebar>
      <ul class="option-list">
        <li
          v-for="item in optionList"
          v-bind:data-id="item[0]"
          @click="handleSelect"
          @touchend="handleSelect"
        >{{item[1]}}</li>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  name: "CustomDropdown",
  components: {},
  props: {
    optionList: Array,
    default_text: String
  },
  data() {
    return {
      default_text_val: this.default_text
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
    }
  },
  beforeDestroy() {
    document.removeEventListener("click", this.onClick);
  }
};
</script>

