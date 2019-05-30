<template>
  <div class="signin-footer">
    <div class="footer-menu">
      <b-list-group>
        <b-list-group-item href="#" title="Privacy Policy">Privacy Policy</b-list-group-item>
        <b-list-group-item href="#" title="Cookie Policy">Cookie Policy</b-list-group-item>
        <b-list-group-item href="#" title="Terms of Use">Terms of Use</b-list-group-item>
      </b-list-group>
    </div>
    <div class="copyright-text">
      <p>
        Powered by
        <b-link title="Optimy">Optimy</b-link>
      </p>
    </div>
  </div>
</template>

<script>
export default {
  components: {},

  name: "SigninFooter",

  data() {
    return {};
  }
};
</script>

<style lang="scss" scoped>

.signin-footer {
  position: relative;
  margin: 0 auto;
  .browser-ios & {
    margin-bottom: 20px;
    @include sm-max {
      margin-bottom: 30px;
    }
  }
  .list-group {
    flex-wrap: wrap;
    justify-content: center;
    a {
      display: inline-block;
      width: auto;
      color: $black-primary;
      font-size: 14px;
      font-weight: 300;
      line-height: 18px;
      background: transparent;
    }
    .list-group-item {
      @include sm-max {
        margin: 0 12px 10px;
      }
      margin: 0 20px 20px;
    }
  }
  .copyright-text {
    text-align: center;
    p {
      margin: 0;
      color: $gray-primary;
      font-size: 14px;
      font-weight: 300;
      line-height: 18px;

      a {
        text-decoration: underline;
        &:hover,
        &:focus {
          &:after {
            display: none;
          }
        }
      }
    }
  }
}
</style>


