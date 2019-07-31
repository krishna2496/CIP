<template>
    <div>dasdasd
  <AddThis 
    publicId="ra-somehash" 
    data-url="THE URL"
    data-title="THE TITLE"
    data-description="THE DESCRIPTION"
    data-media="THE IMAGE"
  />
    </div>
</template>

<script>
import AddThis from 'vue-simple-addthis-share'

export default {
    components: {
        AddThis,
    },

    name: "social",

    data() {
        return {
        
        };
    }
};
</script>