<template>
    <div class="signin-footer">
        <div class="footer-menu">
            <b-list-group v-if="isDynamicFooterItemsSet">
                <b-list-group-item  
                v-for="item in footerItems" 
                :to="'cms/'+getUrl(item)" 
                :title="getTitle(item)">{{getTitle(item)}}
                </b-list-group-item>
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
import axios from "axios";
import store from '../../store';
export default {
components: {},

name: "SigninFooter",

data() {
    return {
         footerItems: [],
         isDynamicFooterItemsSet : false
    };
},

created() {
    // Fetching footer CMS pages
    axios.get(process.env.VUE_APP_API_ENDPOINT+"cms")
    .then((response) => {
        if (response.data.data) {
            this.footerItems = response.data.data
            this.isDynamicFooterItemsSet = true
        }
        }).catch(error => {
            console.log(error)
        })
},
methods:{  
    getTitle(items){
        //Get title according to language
        var filteredObj  = items.filter(function (item,i) { 
            if (item.language_id == store.state.defaultLanguageId) {
                return item;
            }
        });
        return filteredObj[0].title
    },

    getUrl(items){
        //Get url according to language 
        var filteredObj  = items.filter(function (item,i) { 
            if (item.language_id == store.state.defaultLanguageId) {
                return item;
            }
        });
        return filteredObj[0].page_id
    }
}

};

</script>

