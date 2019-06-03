<template>
  <div class="primary-footer">
    <b-container>
      <b-row>
        <b-col md="6" class="footer-menu">
          <b-list-group>
            <b-list-group-item href="#" title="Privacy Policy">Privacy Policy</b-list-group-item>
            <b-list-group-item href="#" title="Cookie Policy">Cookie Policy</b-list-group-item>
            <b-list-group-item href="#" title="Terms of Use">Terms of Use</b-list-group-item>
          </b-list-group>
        </b-col>
        <b-col md="6" class="copyright-text">
          <p>© 2019 Optimy.com. All rights reserved.</p>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
export default {
  components: {},
  name: "primaryFooter",
  data() {
    return {};
  }
};
</script>

<style lang="scss" scoped>
.primary-footer {
  padding: 36px 0;
  border-top: 1px solid $border-color;
  @include md-max {
    padding: 30px 0;
  }

  .list-group {
    @include sm-max {
      display: block;
      text-align: center;
      margin-bottom: 15px;
    }
    a {
      color: $black-primary;
      font-size: 14px;
      font-weight: 300;
      line-height: 18px;
      display: inline-block;
      width: auto;
      background: transparent;
      &:after {
        position: absolute;
        content: "";
        left: 0;
        width: 0;
        bottom: -2px;
        height: 1px;
        background: $black-primary;
        @include transition(all 0.3s);
      }
      &:hover {
        &:after {
          width: 100%;
        }
      }
    }
    .list-group-item {
      margin: 0 18px;
      &:first-child {
        margin-left: 0;
      }
      &:last-child {
        margin-right: 0;
      }
      @include md-max {
        margin: 0 16px;
      }
      @include sm-max {
        margin: 0 16px 10px;
      }
    }
  }
  .copyright-text {
    text-align: right;
    @include sm-max {
      text-align: center;
    }
    p {
      color: $black-primary;
      font-size: 14px;
      font-weight: 300;
      line-height: 18px;
      margin: 0;
    }
  }
}
</style>


