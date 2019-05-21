<template>
  <div class="login-slider">
     <b-carousel
      id=""
      fade
      :interval="800"
      indicators
    >
      <b-carousel-slide 
      caption="Sed ut perspiciatis unde omnis iste natus voluptatem." 
      text="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
      img-src="../assets/images/login/sliderimg1.png"></b-carousel-slide>

      <b-carousel-slide  caption="Heading2" 
      text="Phasellus efficitur cursus sapien, non efficitur mauris interdum accumsan. In hac habitasse platea dictumst. Phasellus fringilla odio at ante interdum maximus. Vestibulum luctus lacus at ex porta consectetur. Aliquam velit diam, rhoncus eu mattis eu, mollis ac erat. Sed id sem vel lorem aliquet malesuada. Nunc lobortis suscipit lobortis. Aenean tempor efficitur neque. Nulla bibendum massa magna. Nulla luctus sapien urna, quis luctus odio blandit quis. Praesent consectetur faucibus odio, vitae maximus dolor feugiat id."
      img-src="../assets/images/login/sliderimg2.jpg"></b-carousel-slide>

      <b-carousel-slide  caption="Sed ut perspiciatis unde omnis iste natus voluptatem." 
      text="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
      img-src="../assets/images/login/sliderimg3.jpg"></b-carousel-slide>

      <b-carousel-slide  caption="Sed ut perspiciatis unde omnis iste natus voluptatem." 
      text="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
      img-src="../assets/images/login/sliderimg4.jpg"></b-carousel-slide>
    </b-carousel>
    </div>
</template>
<script>
export default {
  name: "LoginSlider",
  data() {
    return {
    };
  },
  methods: {}
};
</script>
<style lang="scss" >
// @import "../assets/scss/variables.scss";

.login-slider {
  .carousel-item {
    &:after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 30%;
      background:    -moz-linear-gradient(top, rgba(240,240,240,0) 32%, rgba(233,233,233,0) 34%, rgba(0,0,0,.7) 98%, rgba(0,0,0,.5) 100%);
      background: -webkit-gradient(left top, left bottom, color-stop(32%, rgba(240,240,240,0)), color-stop(34%, rgba(233,233,233,0)), color-stop(98%, rgba(0,0,0,.7)), color-stop(100%, rgba(0,0,0,.5)));
      background: -webkit-linear-gradient(top, rgba(240,240,240,0) 32%, rgba(233,233,233,0) 34%, rgba(0,0,0,.7) 98%, rgba(0,0,0,.5) 100%);
      background:      -o-linear-gradient(top, rgba(240,240,240,0) 32%, rgba(233,233,233,0) 34%, rgba(0,0,0,.7) 98%, rgba(0,0,0,.5) 100%);
      background:     -ms-linear-gradient(top, rgba(240,240,240,0) 32%, rgba(233,233,233,0) 34%, rgba(0,0,0,.7) 98%, rgba(0,0,0,.5) 100%);
      background:         linear-gradient(to bottom, rgba(240,240,240,0) 32%, rgba(233,233,233,0) 34%, rgba(0,0,0,.7) 98%, rgba(0,0,0,.5) 100%);
    }
    img {
      height: 100%;
      object-fit: cover;
    }
  }
  .carousel-indicators {
    li {
      width: 8px;
      height: 8px;
      border-radius: 100%;
      &:focus {
        outline: none;
      }
    }
  }
  .carousel-caption {
    text-align: left;
  }
}

</style>
