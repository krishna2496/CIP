<template>
  <div
    class="custom-dropdown"
    v-on:touchend.stop
    v-on:click.stop
    v-if="optionList.length > 0"
    :class="showDropdown ? 'dropdown-open' : ' '"
  >
    <span @click="handleClick">{{default_text_val}}</span>
    <div class="option-list-wrap" v-if="showDropdown" data-simplebar>
      <ul class="option-list">
        <li v-for="item in optionList" :key="item" @click="handleSelect">{{item}}</li>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  name: "customDropdown",
  props: {
    optionList: Array,
    default_text: String
  },
  data() {
    return {
      showDropdown: false,
      default_text_val: this.default_text
    };
  },
  components: {},
  computed: {},
  methods: {
    onClick() {
      this.showDropdown = false;
    },
    handleClick() {
      this.showDropdown = !this.showDropdown;
    },
    handleSelect(e) {
      var selected_val = e.target.innerHTML;
      this.default_text_val = selected_val;
      this.showDropdown = false;
    }
  },

  created() {},
  beforeUdate() {},
  mounted() {
    if (screen.width < 1025) {
      document.addEventListener("touchend", this.onClick);
    } else {
      document.addEventListener("click", this.onClick);
    }
  },
  beforeDestroy() {
    document.removeEventListener("click", this.onClick);
  }
};
</script>

<style lang="scss" scoped>

.custom-dropdown {
  position: relative;
  width: 200px;
  font-size: 14px;
  line-height: 18px;
  & > span {
    display: block;
    padding: 6px 35px 6px 15px;
    border: 1px solid $control-border;
    border-radius: 3px;
    @extend .control_shadow;
    cursor: pointer;
    &:after {
      position: absolute;
      content: "";
      background: url("../assets/images/down-arrow.svg") no-repeat;
      background-size: 11px;
      width: 11px;
      height: 11px;
      right: 13px;
      top: 50%;
      @include transformY(-50%);
      @include transition(all 0.3s);
    }
  }
  &.dropdown-open {
    span {
      border-radius: 3px 3px 0 0;
      &:after {
        transform: translateY(-50%) rotate(180deg);
        -webkit-transform: translateY(-50%) rotate(180deg);
        -ms-transform: translateY(-50%) rotate(180deg);
        -moz-transform: translateY(-50%) rotate(180deg);
        -o-transform: translateY(-50%) rotate(180deg);
      }
    }
    .option-list-wrap {
      border-radius: 0 0 3px 3px;
    }
  }

  .option-list-wrap {
    @extend .control_shadow;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 99;
    width: 100%;
    max-width: 100%;
    max-height: 226px;
    margin-top: -1px;
    background: #fff;
    border: 1px solid $control-border;
    border-radius: 3px;
    .option-list {
      padding: 4px 0;
      margin: 0;
      list-style-type: none;
      li {
        @include transition(all 0.3s);
        padding: 6px 15px;
        text-align: left;
        background: $white;
        cursor: pointer;
        &:hover {
          background: $border-color;
        }
      }
    }
  }
}
</style>
