<template>
    <div class="primary-footer">
        <b-container>
            <b-row>
                <b-col md="6" class="footer-menu">
                    <b-list-group v-if="isDynamicFooterItemsSet">
                        <b-list-group-item v-for="item in footerItems" :to="{ path: '/'+item.slug}"
                            :title="getTitle(item)" @click.native="clickHandler">{{getTitle(item)}}
                        </b-list-group-item>
                    </b-list-group>
                </b-col>
                <b-col md="6" class="copyright-text">
                    <p>© {{year}} Optimy.com. {{ langauageData.label.all_rights_reserved }}.</p>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    import axios from "axios";
    import store from '../../store';
    import router from "../../router";
    import {
        loadLocaleMessages,
        cmsPages
    } from "../../services/service";

    export default {
        components: {},
        name: "TheSecondaryFooter",
        data() {
            return {
                footerItems: [],
                isDynamicFooterItemsSet: false,
                year: new Date().getFullYear(),
                langauageData: [],
            };
        },
        created() {

            this.langauageData = JSON.parse(store.state.languageLabel);
            // Fetching footer CMS pages
            this.getPageListing();
            // loadLocaleMessages(store.state.defaultLanguage);
            this.footerAdj();
            window.addEventListener("resize", this.footerAdj);
        },
        methods: {
            async getPageListing() {
                await cmsPages().then(response => {
                    this.footerItems = response;
                    this.isDynamicFooterItemsSet = true;
                })
            },

            getTitle(items) {
                //Get title according to language
                items = items.pages;
                if (items) {
                    var filteredObj = items.filter(function (item, i) {
                        if (item.language_id == store.state.defaultLanguageId) {
                            return item;
                        }
                    });
                    if (filteredObj[0]) {
                        return filteredObj[0].title
                    }
                }
            },

            getUrl(items) {
                if (items) {
                    return items.slug
                }
            },

            clickHandler($event) {
                this.$emit('cmsListing', this.$route.params.slug);
            },
            footerAdj() {
                if (document.querySelector("footer") != null) {
                    var footerH = document.querySelector("footer").offsetHeight;
                    document.querySelector("footer").style.marginTop = -footerH + "px";
                    document.querySelector(".inner-pages").style.paddingBottom =
                        footerH + "px";
                }
            }
        },
        updated() {
            this.footerAdj();
        }
    };
</script>