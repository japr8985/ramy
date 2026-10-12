import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';

const metaGuest ={
  auth: false,
  layout: 'guest'
}
const meta ={
  auth: true,
  layout: 'main'
}
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'login',
    component: () => import('@/views/login/LoginView.vue'),
    meta: metaGuest
  },
  {
    path: '/home',
    name: 'home',
    meta,
    component: () => import('@/views/HomeView.vue')
  },
  {
    path: '/settings',
    name: 'settings',
    meta,
    component: () => import('@/views/Settings.vue')
  }
];

const router = createRouter({
  history: createWebHistory('/vue'),
  routes,
});

export default router;