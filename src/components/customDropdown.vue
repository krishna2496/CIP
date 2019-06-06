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
    overflow: hidden;
    @extend .control_shadow;
    max-width:100%;
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
      display:block;
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
    display:none;
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
