<template>
  <div class="signin-slider">
    <b-carousel id fade :interval="2000" indicators>
      <b-carousel-slide
        caption="Sed ut perspiciatis unde omnis iste natus voluptatem."
        text="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
        img-src="../assets/images/login/sliderimg1.png"
      ></b-carousel-slide>

      <b-carousel-slide
        caption="Heading2"
        text="Phasellus efficitur cursus sapien, non efficitur mauris interdum accumsan. In hac habitasse platea dictumst. Phasellus fringilla odio at ante interdum maximus. Vestibulum luctus lacus at ex porta consectetur. Aliquam velit diam, rhoncus eu mattis eu, mollis ac erat. Sed id sem vel lorem aliquet malesuada. Nunc lobortis suscipit lobortis. Aenean tempor efficitur neque. Nulla bibendum massa magna. Nulla luctus sapien urna, quis luctus odio blandit quis. Praesent consectetur faucibus odio, vitae maximus dolor feugiat id."
        img-src="../assets/images/login/sliderimg2.jpg"
      ></b-carousel-slide>

      <b-carousel-slide
        caption="Sed ut perspiciatis unde omnis iste natus voluptatem."
        text="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
        img-src="../assets/images/login/sliderimg3.jpg"
      ></b-carousel-slide>

      <b-carousel-slide
        caption="Sed ut perspiciatis unde omnis iste natus voluptatem."
        text="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
        img-src="../assets/images/login/sliderimg4.jpg"
      ></b-carousel-slide>
    </b-carousel>
  </div>
</template>
<script>
export default {
  name: "SigninSlider",
  data() {
    return {};
  }
};
</script>
