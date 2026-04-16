import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from './pages/Dashboard.vue'
import ProductsIndex from './pages/ProductsIndex.vue'
import ProductCreate from './pages/ProductCreate.vue'
import ProductEdit from './pages/ProductEdit.vue'
import SuppliersIndex from './pages/SuppliersIndex.vue'

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
  },
  {
    path: '/suppliers',
    name: 'suppliers.index',
    component: SuppliersIndex
  }
]

export default createRouter({
  history: createWebHistory(),
  routes
})