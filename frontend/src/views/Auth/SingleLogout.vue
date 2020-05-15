<template></template>
<script>
import store from '../../store';

export default {
    data() { return {}; },
    async beforeCreate() {
      const sloUrl = this.$route.query.slo;
      store.commit('logoutUser', { stay: Boolean(sloUrl) });
      window.location = sloUrl;
    },
};
</script>
