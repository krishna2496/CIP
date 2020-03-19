<template></template>
<script>
import store from '../../store';
import {
  databaseConnection,
  getUserDetail,
  loadLocaleMessages,
  policy,
  tenantSetting,
} from '../../services/service';
import constants from '../../constant';

export default {
    data() { return {}; },
    methods: {
      async createConnection(){
        await databaseConnection([]);
        await tenantSetting();
      },
    },
    async created() {
        await this.createConnection();

        let domain = '';
        let imagePath = '';
        let currentUrl = (((window.location.origin).split('.')));
        if (currentUrl[0]) {
            domain = ((currentUrl[0]).split('//'));
            if(domain[1]) {
                // imagePath = constants.IMAGE_PATH + domain[1];
                imagePath = constants.IMAGE_PATH + "tatva";
                store.commit('setImagePath', imagePath);
            }
        }

        let redirect = '/';
        if (this.$route.query.token) {
          const token = this.$route.query.token;
          store.commit('setToken', token);
          let userDetail = await getUserDetail();
          await loadLocaleMessages(store.state.defaultLanguage);
          store.commit('loginUser', userDetail.data);
          await policy().then(response => {
            if (response.error == false) {
              if(response.data.length > 0) {
                store.commit('policyPage',response.data)
              } else {
                store.commit('policyPage',null)
              }
            } else {
              store.commit('policyPage',null)
            }
          });
          redirect = 'home';
        }

        this.$router.replace({
          name: redirect,
        });
    },
};
</script>
