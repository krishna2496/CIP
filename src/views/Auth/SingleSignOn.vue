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
        document.body.classList.add("loader-enable");
        await this.createConnection();

        let redirect = '/';
        if (this.$route.query.token) {
            const token = this.$route.query.token;
            store.commit('setToken', token);
            let userDetail = await getUserDetail();
            document.body.classList.add("loader-enable");
            await loadLocaleMessages(store.state.defaultLanguage);
            userDetail = userDetail.data;
            userDetail.timezone =  userDetail.timezone.timezone;
            store.commit('loginUser', userDetail);
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
