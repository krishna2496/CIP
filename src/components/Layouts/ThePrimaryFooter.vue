<template>
    <div class="signin-footer">
        <div class="footer-menu" v-if="isDynamicFooterItemsSet">
            <b-list-group>
                <b-list-group-item
                    v-for="item in footerItems"
                    :to="'/'+item.slug"
                    :title="getTitle(item)"
                    >{{getTitle(item)}}
                </b-list-group-item>
            </b-list-group>
        </div>
        <div class="copyright-text">
            <p>
                {{ $t("label.powered_by") }}
                <b-link title="Optimy" href="https://www.optimy.com/">  
                Optimy</b-link>
            </p>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import store from "../../store";
import { loadLocaleMessages,cmsPages} from "../../services/service";

export default {
    components: {},
    name: "ThePrimaryFooter",
    data() {
        return {
            footerItems: [],
            isDynamicFooterItemsSet: false
        };
    },
    mounted() {},
    created() {
        // Fetching footer CMS pages
        this.getPageListing();
        //Fetch language json file
        loadLocaleMessages(store.state.defaultLanguage);
    },
    methods: {
        async getPageListing(){
            await cmsPages().then(response => {
                    this.footerItems = response;
                    this.isDynamicFooterItemsSet = true;  
            })       
        },

        getTitle(items){
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
        
    }
};
</script>

