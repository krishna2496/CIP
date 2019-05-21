<template>
<div class="custom-dropdown" v-on:click.stop>
  <span @click = "handleClick">{{default_text_val}}</span>
  <div class="option-list-wrap" v-if="showDropdown" data-simplebar >
    <ul class="option-list">
      <li v-for="item in optionList" :key="item"  @click="handleSelect">{{item}}</li>
    </ul>
  </div>
</div>
</template>
<script>
export default {
  name: "customDropdown",
  props:{
    optionList:Array,
    default_text:String,
  },
  data() {
    return {
      showDropdown : false,
      default_text_val : this.default_text,
    };
  },
  components: {},
  computed: {
  },
  methods: {
     onClick(){
       this.showDropdown = false;
    },
    handleClick(){
      this.showDropdown = !this.showDropdown;
    },
    handleSelect(e){
      var selected_val = e.target.innerHTML;
      this.default_text_val = selected_val;
      this.showDropdown = false;
    }
  },
 
  created() {

  },
  beforeUdate() {
  },
  mounted() {
    document.addEventListener('click' , this.onClick);
  },
  beforeDestroy() {
    document.removeEventListener('click' , this.onClick);
  },
};
</script>

<style lang="scss" scoped>
.custom-dropdown {
  position: relative;
  width: 200px;
  margin-bottom: 30px;
  color: $black-primary;
  font-size: 14px;
  line-height: 18px;
  & > span {
    display: block;
    padding: 6px 35px 6px 15px;
    background: url(/img/down-arrow.41472b83.svg) no-repeat right 12px center/13px 11px;
    border: 1px solid $border-color;
    border-radius: 3px;
    cursor: pointer;
  }
  .option-list-wrap {
    @extend .dropdown_shadow;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 99;
    width: 100%;
    max-width: 100%;
    max-height: 226px;
    margin-top: -1px;
    background: #fff;
    border: 1px solid $border-color;
    border-radius: 3px;
    .option-list {
      padding: 4px 0;
      margin: 0;
      list-style-type: none;
      li {
        @include transition(all .3s);
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
