import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import Login from './views/Login.vue'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/lang',
      name: 'lang',
      component: () => import('./components/HelloI18n')
    },
    {
      path: '/home',
      name: 'home',
      meta: { requiresAuth: true },
      component: () => import('./views/Home.vue')
    },
    {
      path: '/about',
      name: 'about',
        component: () => import('./views/About.vue')
    },
    {
        path: '/reset-password/:token',
        name: 'resetPassword',
        component: () => import('./views/ResetPassword.vue')
    },
    {
        path: '/forgot-password',
        name: 'forgotPassword',
        component: () => import('./views/ForgotPassword.vue')
    },
    {
        path: '/cms/:pageId',
        name: 'footerCmsPage',
        component: () => import('./views/footerCmsPage.vue')
    }
  ]
})
