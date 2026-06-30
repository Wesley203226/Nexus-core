import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from './pages/Dashboard.vue'
import CatalogIndex from './pages/CatalogIndex.vue'
import ProductsIndex from './pages/ProductsIndex.vue'
import ReportsIndex from './pages/ReportsIndex.vue'
import SuppliersIndex from './pages/SuppliersIndex.vue'
import TypesIndex from './pages/TypesIndex.vue'

const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: Dashboard,
  },
  {
    path: '/catalog',
    name: 'catalog.index',
    component: CatalogIndex,
  },
  {
    path: '/products',
    name: 'products.index',
    component: ProductsIndex,
  },
  {
    path: '/products/new',
    redirect: { name: 'products.index' },
  },
  {
    path: '/products/:id/edit',
    redirect: { name: 'products.index' },
  },
  {
    path: '/suppliers',
    name: 'suppliers.index',
    component: SuppliersIndex,
  },
  {
    path: '/types',
    name: 'types.index',
    component: TypesIndex,
  },
  {
    path: '/reports',
    name: 'reports.index',
    component: ReportsIndex,
  },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
