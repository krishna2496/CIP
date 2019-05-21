<template>
  <div class="signin-page-wrapper">
    <SigninSlider/>
    <div class="signin-form-wrapper">
      <div class="lang-drodown-wrap">
         <customDropdown :optionList="langList" :default_text="defaut_lang" />
      </div>
      <div class="signin-form-block">
        <i class="logo-wrap">
          <img src="../assets/images/optimy-logo.png">
        </i>
        <b-form class="signin-form">
          <b-form-group>
            <label for="">Email Address</label>
            <b-form-input id="" type="email" required placeholder="Enter email"></b-form-input>
          </b-form-group>
          <b-form-group>
             <label for="">Password</label>
            <b-form-input id="" type="password" required placeholder="Enter Password"></b-form-input>
          </b-form-group>
          <b-button type="submit" class=" btn-bordersecondary">Login</b-button>
        </b-form>
        <div class="form-link">
          <b-link>Lost your password?</b-link>
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