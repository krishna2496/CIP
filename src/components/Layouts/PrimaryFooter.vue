<template>
    <div class="signin-footer">
        <div class="footer-menu">
            <b-list-group v-if="isDynamicFooterItemsSet">
                <b-list-group-item
                    v-for="item in footerItems"
                    :to="item.slug"
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
import { loadLocaleMessages } from "../../services/service";

export default {
    components: {},
    name: "Footer",
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
        getPageListing() {
        axios.get(process.env.VUE_APP_API_ENDPOINT + "cms/listing").then(response => {
            if (response.data.data) {
                this.footerItems = response.data.data;
                this.isDynamicFooterItemsSet = true;
            }
        }) .catch(error => {});
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

