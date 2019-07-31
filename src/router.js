import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import TermsofUse from './views/Cms.vue'

Vue.use(Router)

let routes = [{
    path: '/',
    name: 'login',
    component: () =>
        import ('./views/Auth/Login.vue')
},
{
    path: '/home',
    name: 'home',
    meta: {
        requiresAuth: true
    },
    component: () =>
        import ('./views/Home.vue')
},
{
    path: '/home/:searchParamsType/:searchParams',
    name: 'exploreMission',
    meta: {
        requiresAuth: true
    },
    component: () =>
        import ('./views/Home.vue')
},
{
    path: '/reset-password/:token',
    name: 'resetPassword',
    component: () =>
        import ('./views/Auth/ResetPassword.vue')
},
{
    path: '/forgot-password',
    name: 'forgotPassword',
    component: () =>
        import ('./views/Auth/ForgotPassword.vue')
}, 
{
    path: '/:slug',
    name: 'cms',
    component: () =>
        import ('./views/Cms.vue')
},
{
    path: '/home/:searchParamsType',
    name: 'exploreMissions',
    meta: {
        requiresAuth: true
    },
    component: () =>
        import ('./views/Home.vue')
},
{
    path: '/mission-detail/:misisonId',
    name: 'missionDetail',
   
    meta: {
        requiresAuth: true,
        metaTags: [
            {
              property: 'og:url',
              content: 'http://www.nytimes.com/2015/02/19/arts/international/when-great-minds-dont-think-alike.html'
            },
            {
              property: 'og:title',
              content: 'When Great Minds Don’t Think Alike'
            },
            {
              property: 'og:image',
              content: 'http://static01.nyt.com/images/2015/02/19/arts/international/19iht-btnumbers19A/19iht-btnumbers19A-facebookJumbo-v2.jpg'
            },
            {
              property: 'og:type',
              content: 'article'
            },
            {
              property: 'og:description',
              content: 'How much does culture influence creative thinking'
            }
        ],
    },
    component: () =>
        import ('./views/MissionDetail.vue')
},

];

export default new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})