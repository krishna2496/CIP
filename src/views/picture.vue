<template>
<div>
  <a class="btn" @click="toggleShow">set avatar</a>
  <my-upload field="img"
    :width="300"
    :height="300"
    :url="url"
    :params="params"
    :headers="headers"
    lang-type="en"
    :value.sync="show"
    img-format="png"></my-upload>
  <img :src="imgDataUrl">
</div>


</template>


<script>
import "@babel/polyfill"; // es6 shim
import Vue from 'vue';
import myUpload from 'vue-image-crop-upload';
export default {
    name: "AppCheckboxDropdown",
    components: {
       'my-upload': myUpload
    },
    props: {
    },

    data() {
        return {
             show: false,
              params: {
                token: '123456798',
                name: 'avatar'
              },
              headers: {
                smail: '*_~'
              },
              imgDataUrl: '', // the datebase64 url of created image
              url : ''
        };
    },
    mounted() {},
    methods: {
        toggleShow() {
        this.show = !this.show;
      },
            /**
       * crop success
       *
       * [param] imgDataUrl
       * [param] field
       */
      cropSuccess(imgDataUrl, field){
        console.log('-------- crop success --------');
        this.imgDataUrl = imgDataUrl;
      },
      /**
       * upload success
       *
       * [param] jsonData  server api return data, already json encode
       * [param] field
       */
      cropUploadSuccess(jsonData, field){
        console.log('-------- upload success --------');
        console.log(jsonData);
        console.log('field: ' + field);
      },
      /**
       * upload fail
       *
       * [param] status    server api return error status, like 500
       * [param] field
       */
      cropUploadFail(status, field){
        console.log('-------- upload fail --------');
        console.log(status);
        console.log('field: ' + field);
      }
  
    },
    // 
    created() {
      this.url  =process.env.VUE_APP_API_ENDPOINT + "app/user/upload-profile-image"
         // this.langauageData = JSON.parse(store.state.languageLabel);
    },
};
</script>
