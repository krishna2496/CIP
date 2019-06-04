<template>
  <div class="locale-changer" v-show="flag">
    <select v-model="$i18n.locale" @change="langChange">
      <option v-for="(lang, i) in langs" :key="`Lang${i}`" :value="lang">{{
        lang
      }}</option>
    </select>
    <p>{{ $t("test") }}</p>
    <p>{{ $t("label.title") }}</p>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "HelloI18n",
  data() {
    return { langs: ["en", "fr"], flag: false };
  },
  methods: {
    langChange() {
      console.log("Before Messages Updated");
      console.log(this.$i18n.locale, this.$i18n.messages);
      this.loadLocaleMessages(this.$i18n.locale);
      console.log("Updated Messages");
      console.log(this.$i18n.messages);
    },
    async loadLocaleMessages(lang = "en") {
      var _this = this;
      await axios
        .get(`http://localhost/locales/${lang}.json`, {
          method: "get",
          headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*"
          }
        })
        .then(function(res) {
          _this.$i18n.setLocaleMessage(
            _this.$i18n.locale,
            res.data
          );
        });
    }
  },
  mounted(){
    var _this = this;
    setTimeout(function(){
      _this.flag=true;  
    },500);
    
  }
};
</script>