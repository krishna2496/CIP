import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

let routes = [{
        path: '*',
        redirect: '/404'
    },
    {
        path: '/404',
        name: '404',
        component: () =>
            import ('./views/404.vue')
    },
    {
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
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        meta: {
            requiresAuth: true
        },
        component: () =>
            import ('./views/Dashboard.vue')
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