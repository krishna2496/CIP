<template>
    <div class="cms-page inner-pages">
        <header @scroll="handleScroll">
            <TopHeader></TopHeader>
        </header>

    <main v-if="isDynamicFooterItemsSet">
    <b-container>
    <h1>
    {{footerItems.title}}</h1>
    <b-row>
    <b-col lg="3" md="4" class="cms-nav">
    <b-nav>
    <b-nav-item 
    v-for="(item,key) in footerItems.description" 
    v-scroll-to="{ el: '#block-'+key , offset : -63 , duration : 2000 }">
    {{item.title}}
    </b-nav-item>
    </b-nav>
    </b-col>
    <b-col lg="9" md="8">
    <div class="cms-content cms-accordian" id="cms-content">    
    <div class="cms-content-block" 
        v-for="(item,key) in footerItems.description" 
        :id="'block-'+key">
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
    <PrimaryFooter></PrimaryFooter>
    </footer>
    </div>
</template>
<script>
import TopHeader from "../components/Header/TopHeader";
import PrimaryFooter from "../components/Footer/PrimaryFooter";
import axios from "axios";
import store from '../store';

export default {
    components: {
    TopHeader,
    PrimaryFooter
},
data() {
    return {
        footerItems: [],
        isDynamicFooterItemsSet : false
    };
},
mounted() {},
methods: {
// left menu sticky function
    handleScroll() {
        var nav_ = document.querySelector(".cms-nav"); 
        var header_height = document.querySelector("header").offsetHeight;
        var nav_top = nav_.offsetTop;
        var window_top = window.pageYOffset + header_height;
        var nav_height = document.querySelector(".cms-nav .nav").offsetHeight;
        var nav_bottom = nav_height + nav_top;
        var footer_top = document.querySelector("footer").getBoundingClientRect().top;

        if (window_top > nav_top) {
        if (nav_bottom >= footer_top) {
            nav_.classList.add("absolute");
            nav_.classList.remove("fixed");
        } else {
            nav_.classList.add("fixed");
            nav_.classList.remove("absolute");
        }
        } else {
            nav_.classList.remove("fixed");
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
    }
},

created() {
    window.addEventListener("scroll", this.handleScroll);

    axios.get(process.env.VUE_APP_API_ENDPOINT+"cms")
    .then((response) => {
    if (response.data.data) {
    let dataList = [];
    response.data.data.forEach(function(value,key){
    value.forEach(function(objectValue,objectKey){
    if (objectValue.language_id == store.state.defaultLanguageId) {
        dataList.push(value[objectKey]);
    }
    })
    })
    this.footerItems = dataList[0]
    this.isDynamicFooterItemsSet = true
    }
    }).catch(error => {
    console.log(error)
    })

},

destroyed() {
window.removeEventListener("scroll", this.handleScroll);
}
};
</script>
<style lang="scss">
</style>