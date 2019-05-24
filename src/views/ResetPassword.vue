<template>
  <div class="signin-page-wrapper">
    <SigninSlider/>
    <div class="signin-form-wrapper">
      <div class="lang-drodown-wrap">
         <customDropdown :optionList="langList" :default_text="defaut_lang" />
      </div>
      <div class="signin-form-block">
        <a href="/home" class="logo-wrap" title="">
          <img src="../assets/images/optimy-logo.png">
        </a>
        <div class="form-title-block">
          <h1>New Password</h1>
          <p>Please enter a new password in the fields below.</p>
        </div>
        <b-form class="signin-form">
          <b-form-group :state="false">
            <label for="">New Password</label>
            <b-form-input id="" type="password" required value="Password"></b-form-input>
          </b-form-group>
          <b-form-group>
            <label for="">Confirm New Password</label>
            <b-form-input id="" type="password" required placeholder="Enter Password" value="Password"></b-form-input>
          </b-form-group>
          <b-button type="submit" class="btn btn-bordersecondary" title="Change Password">Change Password</b-button>
        </b-form>
        <div class="form-link">
          <b-link href="/login" title="Login">Login</b-link>
        </div>
      </div>
      <SigninFooter/>
    </div>
  </div>
</template>
<script>
import SigninSlider from '../components/SigninSlider';
import SigninFooter from '../components/Footer/SigninFooter';
import customDropdown from '../components/customDropdown';

export default {
  components: {
   SigninSlider,
    SigninFooter, 
    customDropdown, 
  },
  data() {
    return {
      myValue: '',
      defaut_lang:'EN',
      langList:['EN', 'IND', 'FR'],
    };
  },
  computed: {
  },
  methods: {
  },
 mounted() {
 },
  created() {
  }
};

</script>
<style lang="scss">

</style>