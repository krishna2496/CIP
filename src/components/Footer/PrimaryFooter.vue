<template>
    <div class="primary-footer">
        <b-container>
           <b-row>
             <b-col md="6" class="footer-menu">
                <b-list-group>
                  <b-list-group-item href="/landing">Privacy Policy</b-list-group-item>
                  <b-list-group-item href="#">Cookie Policy</b-list-group-item>
                  <b-list-group-item href="#">Terms of Use</b-list-group-item>
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

  name: 'primaryFooter',

   data() {
    return {
    };
  }
}
</script>

<style lang="scss" scoped>

  .primary-footer{
    padding: 40px 0;
    border-top: 1px solid $border-color;

    .list-group{
        a{
          color: $black-primary;
          font-size: 14px;
          font-weight: 300;
          line-height: 18px;
          display: inline-block;
          width: auto;
          background: transparent;
          &:hover{
            color: $secondary;
          }
        }
      .list-group-item{
        margin: 0 20px; 
      
      }
    }
    .copyright-text{
      text-align: right;
      p{
         color: $black-primary;
          font-size: 14px;
          font-weight: 300;
          line-height: 18px;
          margin: 0;
      }
    }
  }

</style>


