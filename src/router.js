import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

let routes = [
    {
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
        path: '/auth/sso',
        name: 'sso',
        component: () =>
            import ('./views/Auth/SingleSignOn.vue')
    },
    {
        path: '/auth/slo',
        name: 'slo',
        component: () =>
            import ('./views/Auth/SingleLogout.vue')
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
        path: '/news',
        name: 'News',
        component: () =>
          import ('./views/News.vue')
    },
    {
        path: '/stories',
        name: 'Stories',
        component: () =>
          import ('./views/Stories.vue')
    },
    {
        path: '/news-detail/:newsId',
        name: 'NewsDetail',
        component: () =>
          import ('./views/NewsDetail.vue')
    },
    {
        path: '/story-detail/:storyId',
        name: 'StoryDetail',
        component: () =>
          import ('./views/StoryDetail.vue')
    },
    {
        path: '/story-preview/:storyId',
        name: 'StoryPreview',
        component: () =>
          import ('./views/StoryDetail.vue')
    },
    {
        path: '/share-story',
        name: 'ShareStory',
        component: () =>
          import ('./views/ShareStory.vue')
    },
    {
        path: '/edit-story/:storyId',
        name: 'EditStory',
        component: () =>
          import ('./views/ShareStory.vue')
    },
    {
        path: '/messages',
        name: 'DashboardMessage',
        component: () =>
          import ('./views/DashboardMessage.vue')
    },
    {
        path: '/comment-history',
        name: 'DashboardComments',
        component: () =>
          import ('./views/DashboardComments.vue')
    },
    {
        path: '/my-stories',
        name: 'DashboardStories',
        component: () =>
          import ('./views/DashboardStories.vue')
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