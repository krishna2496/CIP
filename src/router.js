import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import TermsofUse from './views/Cms.vue'
import Error from './views/Error.vue'

Vue.use(Router)

let routes = [
                {
                  path: '/',
                  name: 'login',
                  component: () => import('./views/Login.vue')
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
                    path: '/:slug',
                    name: 'cms',
                    component: () => import('./views/Cms.vue')
                },
                {
                    path: '/error',
                    name: 'error',
                    component: () => import('./views/Error.vue')
                }
            ];

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})
