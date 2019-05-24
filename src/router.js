import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import ForgotPassword from './views/ForgotPassword.vue'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
  {
    path: '/',
    name: ForgotPassword,
    component: () => import('./views/ForgotPassword.vue')
  },
  {
    path: '/home',
    name: Home,
    component: () => import('./views/Home.vue')
  },
  {
    path: '/about',
    name: 'about',
      // route level code-splitting
      // this generates a separate chunk (about.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import(/* webpackChunkName: "about" */ './views/About.vue')
    },
    {
      path: '/resetpassword',
      name: 'resetpassword',
      component: () => import('./views/ResetPassword.vue')
	},
	{
      path: '/forgotpassword',
      name: 'forgotpassword',
      component: () => import('./views/ForgotPassword.vue')
	}
    ]
  })
