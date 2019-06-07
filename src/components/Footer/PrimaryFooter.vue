<template>
  <div class="primary-footer">
    <b-container>
      <b-row>
        <b-col md="6" class="footer-menu">
           <b-list-group v-if="isDynamicFooterItemsSet">
                <b-list-group-item  
                v-for="item in footerItems" 
                :to="'/'+getUrl(item)" 
                :title="getTitle(item)">{{getTitle(item)}}
                </b-list-group-item>
            </b-list-group>
        </b-col>
        <b-col md="6" class="copyright-text">
          <p>© {{year}} Optimy.com. All rights reserved.</p>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import axios from "axios";
import store from '../../store';

export default {
  components: {},
  name: "primaryFooter",
  data() {
    return {
        footerItems: [],
        isDynamicFooterItemsSet : false,
        year : new Date().getFullYear()
    };
  },
  created() {
     // Fetching footer CMS pages
     this.getPageListing();
  },
  methods:{  
    getPageListing(){
        axios.get(process.env.VUE_APP_API_ENDPOINT+"cms/listing")
    .then((response) => {

        if (response.data.data) {
            this.footerItems = response.data.data
            this.isDynamicFooterItemsSet = true
        }
        }).catch(error => {
            this.getPageListing();
        })
    },
    getTitle(items){
        // console.log(items.pages);return false;
        //Get title according to language
        items = items.pages;
        if (items) { 
            var filteredObj  = items.filter(function (item,i) { 
                if (item.language_id == store.state.defaultLanguageId) {
                    return item;
                }
            });
            if (filteredObj[0]) {
                return filteredObj[0].title
            }
        }
    },

    getUrl(items){
        if (items) { 
            return items.slug
        }
    }
},
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


