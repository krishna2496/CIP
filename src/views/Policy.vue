<template>
    <div class="cms-page inner-pages">
        <header @scroll="handleScroll">
            <ThePrimaryHeader></ThePrimaryHeader>
        </header>

    <main v-if="isPolicyDataSet">
        <b-container>
           
            <h1>
            {{footerItems.pages[0].title}}
            </h1>
            <b-row>
                <b-col lg="3" md="4" class="cms-nav">
                    <b-nav>
                        <b-nav-item v-for="(item,key) in footerItems.pages[0].sections" v-scroll-to="{ el: '#block-'+key , offset :getOffset}">
                            {{item.title}}
                        </b-nav-item>
                    </b-nav>
                </b-col>
                <b-col lg="9" md="8">
                    <div class="cms-content cms-accordian" id="cms-content">    
                    <div class="cms-content-block" v-for="(item,key) in footerItems.pages[0].sections" :id="'block-'+key">
                        <h2 v-b-toggle="'content-' + key" class="accordian-title">{{item.title}}</h2>
                        <b-collapse
                            :id="'content-'+key"
                            class="accordian-content"
                            accordion="my-accordion"
                            visible>
                            {{item.description}}
                        </b-collapse>
                    </div>
                    </div>
                </b-col>
            </b-row>
        </b-container>
    </main>
    <footer>
        <TheSecondaryFooter  v-if="isPolicyDataSet"></TheSecondaryFooter>
    </footer>
    </div>
</template>
<script>
import ThePrimaryHeader from "../components/Layouts/ThePrimaryHeader";
import TheSecondaryFooter from "../components/Layouts/TheSecondaryFooter";
import {policyDetail} from '../services/service';
import axios from "axios";
import store from '../store';

export default {
    components: {
        ThePrimaryHeader,
        TheSecondaryFooter
    },
    data() {
        return {
            footerItems: [],
            isPolicyDataSet : false,
            slug : this.$route.params.policyPage
        };
    },
    mounted() {},
    methods: {
        // left menu sticky function
        handleScroll() {
            var nav_ = document.querySelector(".cms-nav");
      var nav_top = nav_.offsetTop;
      var screen_height = document.body.clientHeight;
      var header_height = document.querySelector("header").offsetHeight;
      var footer_height = document.querySelector("footer").offsetHeight
      var window_top = window.pageYOffset + (header_height + 1);
      var nav_height = document.querySelector(".cms-nav .nav").offsetHeight;
      var nav_bottom = nav_height + nav_top;
      var footer_top = document.querySelector("footer").getBoundingClientRect()
        .top;

      var content_height = document.querySelector('.cms-content').offsetHeight - parseInt(window.getComputedStyle(document.querySelector('.cms-content'), null).getPropertyValue("padding-bottom"));
      var scroll_height = screen_height - nav_top - footer_height + header_height;
      if(screen.width > 767 && screen.width < 1025){
        if(content_height > scroll_height){
        if (window_top > nav_top) {
            nav_.classList.add("fixed");
        } else {
          nav_.classList.remove("fixed");
        }
        }
        else{
            nav_.classList.remove("fixed");
        }
      }

      if(screen.width > 1024){
        if (window_top > nav_top) {
          if (nav_bottom >= footer_top + 100) {
            nav_.classList.add("absolute");
            nav_.classList.remove("fixed");
          } else {
            nav_.classList.add("fixed");
            nav_.classList.remove("absolute");
          }
        } else {
          nav_.classList.remove("fixed");
        }
      }

            var link_list = document.querySelectorAll(".cms-nav .nav-item");
            var block_list = document.querySelectorAll(".cms-content-block");
            for (var i = 0; i < block_list.length; ++i) {
                if (block_list[i].getBoundingClientRect().top < header_height + 42) {
                    for (var j = 0; j < link_list.length; j++) {
                        var link_siblings = link_list[j].parentNode.childNodes;
                        for (var k = 0; k < link_siblings.length; ++k) {
                            link_siblings[k].childNodes[0].classList.remove("active");
                        }
                        link_siblings[i].childNodes[0].classList.add("active");
                    }
                }
            }
        },

        getOffset() {
            var header_height = document.querySelector("header").offsetHeight;
            return -header_height;
        },
        //List cms pages
        async policyListing(slug){
            // policyDetail


            await policyDetail(slug).then( response => {
                if(response.error == false){
                    this.footerItems = response.data;
                    this.isPolicyDataSet = true
                } else {
                    this.$router.push({ name: '404' });
                }
            }); 
        }
    },
    watch:{
        $route (to, from){
            this.footerItems = [];
            this.isPolicyDataSet = false;
            this.slug = this.$route.params.policyPage;
            this.policyListing(this.$route.params.policyPage);           
        }
    },
    created() {
        this.policyListing(this.slug);
        window.addEventListener("scroll", this.handleScroll);
        window.addEventListener("resize", this.handleScroll);
        window.addEventListener("resize", this.getOffset);
       
    },

    destroyed() {
        window.removeEventListener("scroll", this.handleScroll);
        window.removeEventListener("resize", this.handleScroll);
        window.removeEventListener("resize", this.getOffset);
    }
};
</script>
<style lang="scss">
</style>