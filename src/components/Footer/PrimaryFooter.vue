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
