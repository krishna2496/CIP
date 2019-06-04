<template>
  <div>	{{name}}</div>
</template>

<script>
import axios from "axios";
export default {
	
	data() {
		return {
			name : "sdfsdf",
		    footerItems: [],
        	isDynamicFooterItemsSet : false
		};
	},

	computed: {},

	methods: {},
	
	created() {

		axios.get(process.env.VUE_APP_API_ENDPOINT+"cms")
    	.then((response) => {
        if (response.data.data) {
            
            response.data.data.map(function(value, key) {
            	if(value[key].language_id == 1){
            		footerItems.push(value[key]);
		   		}
		    });

            console.log(footerItems[0].description);
            this.isDynamicFooterItemsSet = true

        }}).catch(error => {
            console.log(error)
        })
	},

};
</script>
<style lang="scss"></style>
