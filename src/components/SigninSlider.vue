<template>
    <div class="signin-slider">
        <b-carousel  id="carousel-1" fade :interval="2000" indicators v-if="isDynamicCarsousetSet">

        <b-carousel-slide 
        v-for="item in carouselItems"
        :key="item.sort_order"
        :caption="getTitle(item.translations)"
        :text="getDescription(item.translations)"
        :img-src="item.url">     
        </b-carousel-slide>

        </b-carousel>
        <b-carousel id fade :interval="2000" indicators v-else>

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

import store from '../store';

export default {
name: "SigninSlider",
data() {
    return {
        carouselItems: [],
        isDynamicCarsousetSet : false
    };
},

created(){
    //Set carousel dynamically 
    if(JSON.parse(store.state.slider) != null){
        this.carouselItems = JSON.parse(store.state.slider);
        this.isDynamicCarsousetSet = true
    }
},

methods:{
    //Slider title by language
    getTitle: (translations) => {
        var filteredObj  = translations.filter(function (item,i) { 
        if(item.lang === store.state.defaultLanguage.toLowerCase()){
            return translations[i].slider_title;
        }
        });
           return filteredObj[0].slider_title;
    },

//Slider description by language
    getDescription: (translations) => {
        var filteredObj  = translations.filter(function (item,i) { 
        if(item.lang === store.state.defaultLanguage.toLowerCase()){
            return translations[i].slider_description;
        }
        });
            return filteredObj[0].slider_description;
    }
}

};
</script>

<style lang="scss" >
.signin-slider {
  .carousel-item {
    transition: none !important;
    transition: opacity 0.6s !important;
    -webkit-transition: opacity 0.6s !important;
    -ms-transition: opacity 0.6s !important;
    -moz-transition: opacity 0.6s !important;
    &:after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 35%;
      background: -moz-linear-gradient(
        top,
        rgba(240, 240, 240, 0) 32%,
        rgba(233, 233, 233, 0) 34%,
        rgba(0, 0, 0, 0.7) 100%,
        rgba(0, 0, 0, 0.5) 100%
        );
      background: -webkit-gradient(
        left top,
        left bottom,
        color-stop(32%, rgba(240, 240, 240, 0)),
        color-stop(34%, rgba(233, 233, 233, 0)),
        color-stop(98%, rgba(0, 0, 0, 0.7)),
        color-stop(100%, rgba(0, 0, 0, 0.5))
        );
      background: -webkit-linear-gradient(
        top,
        rgba(240, 240, 240, 0) 32%,
        rgba(233, 233, 233, 0) 34%,
        rgba(0, 0, 0, 0.7) 100%,
        rgba(0, 0, 0, 0.5) 100%
        );
      background: -o-linear-gradient(
        top,
        rgba(240, 240, 240, 0) 32%,
        rgba(233, 233, 233, 0) 34%,
        rgba(0, 0, 0, 0.7) 100%,
        rgba(0, 0, 0, 0.5) 100%
        );
      background: -ms-linear-gradient(
        top,
        rgba(240, 240, 240, 0) 32%,
        rgba(233, 233, 233, 0) 34%,
        rgba(0, 0, 0, 0.7) 100%,
        rgba(0, 0, 0, 0.5) 100%
        );
      background: linear-gradient(
        to bottom,
        rgba(240, 240, 240, 0) 32%,
        rgba(233, 233, 233, 0) 34%,
        rgba(0, 0, 0, 0.7) 100%,
        rgba(0, 0, 0, 0.5) 100%
        );
  }
  img {
      height: 100vh;
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
    left: 11%;
    text-align: left;
    bottom: 64px;
    @include xlg-max {
      bottom: 40px;
  }
  .browser-ios & {
      bottom: 80px;
  }
  h3 {
      font-size: 43px;
      line-height: 52px;
      margin-bottom: 22px;
      max-width: 65%;
      @include xlg-max {
        font-size: 30px;
        line-height: 34px;
        max-width: 100%;
        margin-bottom: 15px;
    }
}
p {
  font-size: 16px;
  line-height: 28px;
  max-width: 80%;
  @include xlg-max {
    max-width: 100%;
    font-size: 15px;
    line-height: 20px;
}
}
}
.carousel-indicators {
    margin-bottom: 30px;
    li {
      margin-left: 5px;
      margin-right: 5px;
  }
  .browser-ios & {
      margin-bottom: 60px;
  }
}
}
</style>
