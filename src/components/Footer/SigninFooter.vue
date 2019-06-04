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
    //Get title according to language 
    getTitle(items){
        var filteredObj  = items.filter(function (item,i) { 

                if(item.language_id == store.state.defaultLanguageId){
                    return item;
                }
        });
        return filteredObj[0].title
    },

    //Get url according to language 
    getUrl(items){
        var filteredObj  = items.filter(function (item,i) { 

                if(item.language_id == store.state.defaultLanguageId){
                    return item;
                }
        });
        return filteredObj[0].page_id
    }
}

};

</script>

<style lang="scss" scoped>

.signin-footer {
position: relative;
margin: 0 auto;
.browser-ios & {
margin-bottom: 20px;
@include sm-max {
margin-bottom: 30px;
}
}
.list-group {
flex-wrap: wrap;
justify-content: center;
a {
display: inline-block;
width: auto;
color: $black-primary;
font-size: 14px;
font-weight: 300;
line-height: 18px;
background: transparent;
}
.list-group-item {
@include sm-max {
margin: 0 12px 10px;
}
margin: 0 20px 20px;
}
}
.copyright-text {
text-align: center;
p {
margin: 0;
color: $gray-primary;
font-size: 14px;
font-weight: 300;
line-height: 18px;

a {
text-decoration: underline;
&:hover,
&:focus {
&:after {
display: none;
}
}
}
}
}
}
</style>


