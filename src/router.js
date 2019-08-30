import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import TermsofUse from './views/Cms.vue'

Vue.use(Router)

let routes = [{ 
    path: '*',
    redirect: '/404'
},
{ 
    path: '/404',
    name: '404',
    component:  () =>
        import ('./views/404.vue')
},

{
    path: '/',
    name: 'login',
    component: () =>
        import ('./views/Auth/Login.vue')
},
{
    path: '/calendar',
    name: 'calendar',
    component: () =>
        import ('./views/calendar.vue')
},
{
    path: '/calendar2',
    name: 'calendar2',
    component: () =>
        import ('./views/calendar2.vue')
},
{
        path: '/volunteering-timesheet',
        name: 'Volunteering timesheet ',
        meta: {
            requiresAuth: true
        },
        component: () =>
            import ('./views/VolunteeringTimesheet.vue')
},
{
        path: '/volunteering-history',
        name: 'Volunteering history ',
        meta: {
            requiresAuth: true
        },
        component: () =>
            import ('./views/VolunteeringHistory.vue')
},{
        path: '/dashboard',
        name: 'dashboard',
        meta: {
            requiresAuth: true
        },
        component: () =>
            import ('./views/Dashboard.vue')
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
        path: '/my-account',
        name: 'myAccount',
        meta: {
            requiresAuth: true
        },
        component: () =>
            import ('./views/MyAccount.vue')
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
    
    beforeEnter: (to, from, next) => {
            to.meta.metaTags.map(tagDef => {
                const tag = document.createElement('meta');
                Object.keys(tagDef).forEach(key => {
                    tag.setAttribute(key, tagDef[key]);
                });
                return tag;
            })
            // Add the meta tags to the document head.
            .forEach(tag => document.head.appendChild(tag));
            next();
    },

    component: () =>
        import ('./views/MissionDetail.vue')
},
{
        path: '/policy/:policyPage',
        name: 'policy',
        meta: {
            requiresAuth: true
        },
        component: () =>
            import ('./views/Policy.vue')
},

];

export default new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})