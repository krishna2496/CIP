<template>
  <div class="checkbox-select">
    <span class="select-text">{{filterTitle}}</span>
    <div class="chk-select-wrap" data-simplebar @click.stop @touchend.stop>
      <ul class="chk-select-options">
        <li v-for="(item , i) in checkList" :key="i">
          <b-form-checkbox name>{{item.value}}</b-form-checkbox>
        </li>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  name: "CheckboxDropdown",
  components: {},
  props: {
    filterTitle: String,
    checkList: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {};
  },
  mounted() {
    var dropdwon_toggle = document.querySelectorAll(".select-text");
    for (var i = 0; i < dropdwon_toggle.length; ++i) {
      if (screen.width < 1025) {
        dropdwon_toggle[i].addEventListener("touchend", this.handleClick);
      } else {
        dropdwon_toggle[i].addEventListener("click", this.handleClick);
      }
    }
  },
  methods: {
    handleClick(e) {
      e.stopPropagation();
      e.target.parentNode.classList.toggle("dropdown-open");
      var dropdownList = document.querySelectorAll(".dropdown-open");
      for (var i = 0; i < dropdownList.length; ++i) {
        if (dropdownList[i] != e.target.parentNode) {
          dropdownList[i].classList.remove("dropdown-open");
        }
      }
    }
  }
};
</script>