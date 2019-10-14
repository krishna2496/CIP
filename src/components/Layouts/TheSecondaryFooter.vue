<template>
    <div class="primary-footer">
        <b-container>
            
            <div class="cookies-block" v-bind:class="{
                'hidden' : isCookieHidden
            }">
                <div class="container">
                    <div class="text-wrap" v-html="cookiePolicyText">
                       
                    </div>
                    <b-button class="btn-bordersecondary">
                        <span>{{ languageData.label.i_agree }}</span>
                    </b-button>
                </div>
                <i class="close" title="Close">
                    <img :src="$store.state.imagePath+'/assets/images/cross-ic-white.svg'" alt="cross-ic" />
                </i>
            </div>

            <b-row>
                <b-col md="6" class="footer-menu">
                    <b-list-group v-if="isDynamicFooterItemsSet">
                        <b-list-group-item v-for="(item, key) in footerItems" v-bind:key=key
                            :to="{ path: '/'+item.slug}" :title="getTitle(item)" @click.native="clickHandler">
                            {{getTitle(item)}}
                        </b-list-group-item>
                    </b-list-group>
                </b-col>
                <b-col md="6" class="copyright-text">
                    <p>© {{year}} Optimy.com. {{ languageData.label.all_rights_reserved }}.</p>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    import store from '../../store';
    import {
        cmsPages,
        cookieAgreement
    } from "../../services/service";

    export default {
        components: {},
        name: "TheSecondaryFooter",
        data() {
            return {
                footerItems: [],
                isDynamicFooterItemsSet: false,
                year: new Date().getFullYear(),
                languageData: [],
                isCookieHidden : true,
                cookiePolicyText : ''
            };
        },
        created() {
            var _this = this;
            this.languageData = JSON.parse(store.state.languageLabel);
            // Fetching footer CMS pages
            this.getPageListing();
            this.footerAdj();
            
            if(store.state.cookieAgreementDate == '' || store.state.cookieAgreementDate == null) {
                this.isCookieHidden = false;
            }

            setTimeout(function () {
                var closeCookies = document.querySelector('.cookies-block .close');
                var agreeBtn = document.querySelector('.cookies-block .btn');
                var cookiesBlock = document.querySelector('.cookies-block');

                agreeBtn.addEventListener('click', () => {
                    cookiesBlock.classList.add('hidden')
                    _this.agreeCookie();
                })

                closeCookies.addEventListener('click', () => {
                    cookiesBlock.classList.add('hidden')
                    _this.hideCookieBlock();
                })
            })

            let cookiePolicyTextArray = JSON.parse(store.state.cookiePolicyText)
            if(cookiePolicyTextArray) {
                cookiePolicyTextArray.filter((data,index) => {
                    if(data.lang == store.state.defaultLanguage.toLowerCase()) {
                        this.cookiePolicyText = data.message
                    }
                })
            }
        
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
                    let filteredObj = items.filter((item) => {
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

            clickHandler() {
                this.$emit('cmsListing', this.$route.params.slug);
            },

            footerAdj() {
                if (document.querySelector("footer") != null) {
                    let footerH = document.querySelector("footer").offsetHeight;
                    document.querySelector("footer").style.marginTop = -footerH + "px";
                    document.querySelector(".inner-pages").style.paddingBottom =
                        footerH + "px";
                }
            },
            
            agreeCookie() {
                let data = {
                    "agreement": true
                }
                cookieAgreement(data).then(response => {
                    this.hideCookieBlock();
                })
            },

            hideCookieBlock() {
                this.$store.commit('removeCookieBlock');
            }
        },
        updated() {
            this.footerAdj();
        }
    };
</script>