import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';

const metaGuest = {
  auth: false,
  layout: 'guest'
}
const meta = {
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
    path: '/categories',
    children: [
      {
        path: '',
        name: 'categories.index',
        meta,
        component: () => import('@/views/categories/index.vue')
      },
      {
        path: 'create',
        name: 'categories.create',
        meta,
        component: () => import('@/views/categories/CategoryFormView.vue'),
      },
      {
        path: ':id/edit',
        name: 'categories.edit',
        meta,
        component: () => import('@/views/categories/CategoryFormView.vue'),
      },
    ]
  },
  {
    path: '/products',
    children: [
      {
        path: '',
        name: 'products.index',
        meta,
        component: () => import('@/views/products/ProductListView.vue')
      },
      {
        path: 'create',
        name: 'products.create',
        meta,
        component: () => import('@/views/products/ProductFormView.vue'),
      },
      {
        path: ':id/edit',
        name: 'products.edit',
        meta,
        component: () => import('@/views/products/ProductFormView.vue'),
      },
    ]
  },
  {
    path: '/customers',
    meta,
    children: [
      {
        path: '',
        name: 'customers.index',
        component: () => import('@/views/customers/CustomerListView.vue'),
      },
      {
        path: 'create',
        name: 'customers.create',
        component: () => import('@/views/customers/CustomerFormView.vue'),
      },
      {
        path: ':id/edit',
        name: 'customers.edit',
        component: () => import('@/views/customers/CustomerFormView.vue'),
      },
    ],
  },
  {
    path: '/sales',
    meta,
    children: [
      {
        path: 'history',
        name: 'sales.history',
        component: () => import('@/views/sales/SalesHistoryView.vue'),
      },
    ],
  },
  {
    path: '/cash-register',
    meta,
    children: [
      {
        path: '',
        name: 'cash-register.shift',
        component: () => import('@/views/cash-register/CashShiftView.vue'),
      },
      {
        path: 'history',
        name: 'cash-register.history',
        component: () => import('@/views/cash-register/ShiftHistoryView.vue'),
      },
    ],
  },
  {
    path: '/receivables',
    meta,
    children: [
      {
        path: '',
        name: 'receivables.index',
        component: () => import('@/views/receivables/ReceivablesView.vue'),
      },
    ],
  },
  {
    path: '/pos',
    name: 'pos.terminal',
    component: () => import('@/views/pos/PosTerminalView.vue'),
    meta
  },
  {
  path: '/purchasing',
  meta,
  children: [
    {
      path: 'suppliers',
      name: 'purchasing.suppliers',
      component: () => import('@/views/purchasing/SuppliersView.vue'),
    },
    {
      path: 'purchases',
      name: 'purchasing.purchases',
      component: () => import('@/views/purchasing/PurchasesView.vue'),
    },
  ],
},
  {
    path: '/settings',
    name: 'settings',
    meta,
    component: () => import('@/views/Settings.vue')
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;