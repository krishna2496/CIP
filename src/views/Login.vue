<template>
  <div class="login-page-wrapper">
    <LoginSlider/>
    <div class="login-form-wrapper">
      <div class="lang-drodown-wrap">
         <customDropdown :optionList="langList" :default_text="defaut_lang" />
      </div>
      <div class="login-form-block">
        <i class="logo-wrap">
          <img src="../assets/images/optimy-logo.png">
        </i>
        <b-form class="login-form">
          <b-form-group :state="false">
            <label for="">Email address</label>
            <b-form-input id="" type="email" required placeholder="Enter email"></b-form-input>
          </b-form-group>
          <b-form-group label="Password" label-for="input-1" :state="true" class="required">
            <b-form-input id="" type="password" required placeholder="Enter Password"></b-form-input>
          </b-form-group>
          <b-button type="submit" class="btn btn-bordersecondary">Login</b-button>
        </b-form>
        <div class="forgot-password-link">
          <b-link>Lost your password?</b-link>
        </div>
      </div>
      <LoginFooter/>
    </div>
  </div>
</template>
<script>
import LoginSlider from '../components/LoginSlider';
import LoginFooter from '../components/Footer/LoginFooter';
import customDropdown from '../components/customDropdown';

export default {
  components: {
    LoginSlider,
    LoginFooter, 
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
.login-page-wrapper {
  display: flex;
  justify-content: center;
  height: 100vh;
  overflow:hidden;

  .login-slider {
    @include lg-max {
      width: calc(100% - 350px);
    }

@include md-max {
      display: none;
    }  width: calc(100% - 480px);

     /deep/ .carousel-item {
      height: 100vh;
    }
  }

    .login-form-wrapper {
    position: relative;
    display: inline-flex;
    flex-wrap: wrap;
    justify-content: center;
    align-content: space-between;
    align-items: center;
    overflow-x:hidden;
    overflow-y: auto;
    width: 480px;
    max-width: 100%;
    max-height: 100vh;
    padding: 40px 40px 0;
    @include lg-max {
      width: 350px;
    }
    @include md-max {
      max-height: auto !important;
      overflow:inherit;
      width: 480px;
    } 
    .login-form-block {
      width: 100%;
    }

    .lang-drodown-wrap {
      display: flex;
      justify-content: flex-end;
      flex: 1;
      justify-cotnent: flex-end;
      .custom-dropdown{
      width: 69px !important;
      left:17px;
      @include xlg-max{
        left:0;
      }
      >span{
        padding:8px 35px 11px 15px !important;
      }
      }
    }

    .logo-wrap {
      display: block;
      width: 150px;
      margin: 0 auto 38px;
      @include sm-max{
        width:115px;
      }
    }

    .form-group {
      margin: 0 0 22px;
    }

    .btn-bordersecondary {
      display: flex;
      justify-content: center;
      flex: 1;
      width: 100%;
      margin-top: 28px;
    }

    .forgot-password-link {
      display: flex;
      justify-content: center;
      align-items: flex-start;

      a {
        color: $black-primary;
        font-size: 14px;
        line-height: 18px;

        &:hover {
          color: $secondary;
        }
      }
    }

    .login-form {
      margin-bottom: 20px;

      .form-control {
        @include sm-max {
          height: 45px;
        }
      }

      .btn {
        @include sm-max{
        min-height: 42px;
        }
      }
    }
  }
}
</style>