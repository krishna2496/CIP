<template>
  <div>
    <i class="logo-wrap">
        <img :src="logo">
    </i>
  </div>
</template>
<script>
export default {
  name: "logo",
  components: {},
  props: {},
  data() {
    return { 
      logo : store.state.logo
    };
  },
  methods: {}
};
</script>