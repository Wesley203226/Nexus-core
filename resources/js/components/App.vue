<template>
  <div class="app-frame">
    <header class="top-nav shell">
      <div class="brand-block">
        <div class="brand-mark">
          <Boxes class="h-5 w-5" />
        </div>
        <div>
          <p class="eyebrow">Nexus Core</p>
          <h1 class="brand-title">Painel operacional</h1>
        </div>
      </div>

      <button type="button" class="nav-toggle" @click="mobileMenuOpen = !mobileMenuOpen">
        <Menu v-if="!mobileMenuOpen" class="h-5 w-5" />
        <X v-else class="h-5 w-5" />
      </button>

      <nav :class="['nav-links', { 'nav-links-open': mobileMenuOpen }]">
        <RouterLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          :class="['nav-link', { 'nav-link-active': route.path === item.to }]"
        >
          <component :is="item.icon" class="h-4 w-4" />
          <span>{{ item.label }}</span>
        </RouterLink>
      </nav>

      <div class="nav-cta">
        <RouterLink to="/products" class="btn-primary">
          <Sparkles class="h-4 w-4" />
          <span>Gerenciar estoque</span>
        </RouterLink>
      </div>
    </header>

    <main class="shell page-shell">
      <RouterView />
    </main>

    <footer class="shell footer-bar">
      <p>Nexus Core reorganizado para produtos, fornecedores e tipos.</p>
      <p>Uploads, CRUDs e design unificados em uma unica interface.</p>
    </footer>

    <div class="toast-stack">
      <transition-group name="toast-move">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          :class="['toast', `toast-${notification.type}`]"
        >
          <div class="flex items-start gap-3">
            <div class="toast-icon">
              <CircleCheckBig v-if="notification.type === 'success'" class="h-4 w-4" />
              <TriangleAlert v-else class="h-4 w-4" />
            </div>

            <div class="flex-1">
              <p class="toast-title">
                {{ notification.type === 'success' ? 'Operacao concluida' : 'Ajuste necessario' }}
              </p>
              <p class="toast-message">{{ notification.message }}</p>
            </div>

            <button type="button" class="icon-btn" @click="removeNotification(notification.id)">
              <X class="h-4 w-4" />
            </button>
          </div>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { RouterLink, RouterView, useRoute } from 'vue-router'
import {
  Boxes,
  CircleCheckBig,
  LayoutDashboard,
  Menu,
  Package,
  Sparkles,
  Tags,
  TriangleAlert,
  Truck,
  X,
} from 'lucide-vue-next'
import {
  notifications,
  pushNotification,
  removeNotification,
} from '../composables/useNotifications'

const route = useRoute()
const mobileMenuOpen = ref(false)

const navItems = [
  { to: '/', label: 'Dashboard', icon: LayoutDashboard },
  { to: '/products', label: 'Itens', icon: Package },
  { to: '/suppliers', label: 'Fornecedores', icon: Truck },
  { to: '/types', label: 'Tipos', icon: Tags },
]

watch(
  () => route.fullPath,
  () => {
    mobileMenuOpen.value = false
  }
)

onMounted(() => {
  const successMessage = document.querySelector('meta[name="flash-success"]')?.getAttribute('content')
  const errorMessage = document.querySelector('meta[name="flash-error"]')?.getAttribute('content')

  if (successMessage) {
    pushNotification(successMessage, 'success')
  }

  if (errorMessage) {
    pushNotification(errorMessage, 'error')
  }
})
</script>

<style>
.toast-move-enter-active,
.toast-move-leave-active {
  transition: all 0.25s ease;
}

.toast-move-enter-from,
.toast-move-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
