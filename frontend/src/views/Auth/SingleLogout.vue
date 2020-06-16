<template></template>
<script>
import store from '../../store';

export default {
    data() { return {}; },
    async beforeCreate() {
      store.commit('logoutUser');
    },
};
</script>
