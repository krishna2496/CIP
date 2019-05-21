<template>
    <div class="login-footer">
        <div class="footer-menu">
          <b-list-group>
            <b-list-group-item href="/landing">Privacy Policy</b-list-group-item>
            <b-list-group-item href="#">Cookie Policy</b-list-group-item>
            <b-list-group-item href="#">Terms of Use</b-list-group-item>
          </b-list-group>
          </div>
        <div class="copyright-text">
          <p>Powered by <b-link>Optimy</b-link></p>
        </div>
    </div>
</template>

<script>

export default {
components: {},

  name: 'LoginFooter.vue',

   data() {
    return {
    };
  }
}
</script>

<style lang="scss" scoped>
.login-footer {
  position: relative;
  padding: 38px 0;

  .list-group {
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 20px;
    a {
      display: inline-block;
      width: auto;
      color: $black-primary;
      font-size: 14px;
      font-weight: 300;
      line-height: 18px;
      background: transparent;
      &:hover {
        color: $secondary;
      }
    }
    .list-group-item {
      @include sm-max {
        margin: 0 12px 20px;
      }  margin: 0 20px 20px;
    }
  }
  .copyright-text {
    text-align: center;
    p {
      margin: 0;
      color: $black-primary;
      font-size: 14px;
      font-weight: 300;
      line-height: 18px;
      a {
        &:hover {
          color: $primary;
          text-decoration: underline;
        }
      }
    }
  }
}

</style>


