<template>
  <div class="signin-page-wrapper">
    <SigninSlider/>
    <div class="signin-form-wrapper">
      <div class="lang-drodown-wrap">
        <customDropdown :optionList="langList" :default_text="defaut_lang"/>
      </div>
      <div class="signin-form-block">
        <a href="/home" class="logo-wrap" title>
          <img src="../assets/images/optimy-logo.png">
        </a>
        <div class="form-title-block">
          <h1>Forgot Password</h1>
          <p>Enter your email address you've using for your account below and we will send you a password reset link</p>
        </div>
        <b-form class="signin-form">
          <b-form-group>
            <label for>Email Address</label>
            <b-form-input id type="email" required placeholder="Enter your email address..."></b-form-input>
          </b-form-group>
          <b-button
            type="submit"
            class="btn btn-bordersecondary"
            title="Reset my Password"
          >Reset my Password</b-button>
        </b-form>
        <div class="form-link">
          <b-link href="/" title="Login">Login</b-link>
        </div>
      </div>
      <SigninFooter/>
    </div>
  </div>
</template>
<script>
import SigninSlider from "../components/SigninSlider";
import SigninFooter from "../components/Footer/SigninFooter";
import customDropdown from "../components/customDropdown";

export default {
  components: {
    SigninSlider,
    SigninFooter,
    customDropdown
  },
  data() {
    return {
      myValue: "",
      defaut_lang: "EN",
      langList: ["EN", "IND", "FR"]
    };
  },
  computed: {},
  methods: {},
  mounted() {},
  created() {}
};
</script>
<style lang="scss">
</style>