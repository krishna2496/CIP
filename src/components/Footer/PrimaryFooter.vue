<template>
  <div class="primary-footer">
    <b-container>
      <b-row>
        <b-col md="6" class="footer-menu">
           <b-list-group v-if="isDynamicFooterItemsSet">
                <b-list-group-item  
                v-for="item in footerItems" 
                :to="item.slug" 
                :title="getTitle(item)"
                @click.native="clickHandler"
                >{{getTitle(item)}}
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
import router from "../../router";
import { loadLocaleMessages } from "../../services/service";

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
     loadLocaleMessages(store.state.defaultLanguage);
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
    },
    clickHandler($event) {
        this.$emit('cmsListing',this.$route.params.slug);
    },
},
};
</script>
