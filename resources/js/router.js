import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from './pages/Dashboard.vue'
import ProductsIndex from './pages/ProductsIndex.vue'
import ProductCreate from './pages/ProductCreate.vue'
import ProductEdit from './pages/ProductEdit.vue'

const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: Dashboard
  },
  {
    path: '/products',
    name: 'products.index',
    component: ProductsIndex
  },
  {
    path: '/products/new',
    name: 'products.create',
    component: ProductCreate
  },
  {
    path: '/products/:id/edit',
    name: 'products.edit',
    component: ProductEdit,
    props: true
  }
]

export default createRouter({
  history: createWebHistory(),
  routes
})