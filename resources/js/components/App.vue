<template>
  <div id="app-container" class="min-h-screen relative selection:bg-indigo-500/30 z-10">
    <!-- Floating Minimal Nav -->
    <nav class="z-nav">
      <router-link to="/" class="flex items-center gap-2 group no-underline">
        <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-500 shadow-lg shadow-indigo-500/20">
          <ShoppingCartIcon class="w-4 h-4 text-white" />
        </div>
        <div class="flex flex-col leading-none">
          <span class="text-white font-black tracking-tighter text-sm uppercase">Nexus</span>
        </div>
      </router-link>
      
      <div class="h-4 w-[1px] bg-white/10 mx-1"></div>
      
      <div class="flex items-center gap-6">
        <router-link to="/" class="z-link" active-class="z-link-active">Início</router-link>
        <router-link to="/products" class="z-link" active-class="z-link-active">Estoque</router-link>
        <router-link to="/products/new" class="z-link" active-class="z-link-active">Cadastrar</router-link>
      </div>
    </nav>

    <!-- Main Content Portal -->
    <div class="z-portal">
      <!-- Flash Messages -->
      <transition name="fade">
        <div v-if="flashMessage" class="fixed top-24 left-1/2 -translate-x-1/2 z-[60] w-full max-w-md px-4">
          <div
            :class="[
              'z-glass p-4 rounded-2xl flex items-center gap-4 shadow-2xl border-l-4 animate-in slide-in-from-top duration-500',
              flashMessage.type === 'success' ? 'border-indigo-500' : 'border-rose-500'
            ]"
          >
            <CheckCircleIcon v-if="flashMessage.type === 'success'" class="h-6 w-6 text-indigo-400" />
            <AlertCircleIcon v-else class="h-6 w-6 text-rose-400" />
            <p class="text-sm font-bold text-white uppercase tracking-tight">{{ flashMessage.message }}</p>
            <button @click="clearFlashMessage" class="ml-auto p-1 hover:bg-white/5 rounded-full">
              <XIcon class="h-4 h-4 text-slate-500" />
            </button>
          </div>
        </div>
      </transition>

      <!-- Dynamic Page Content -->
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>

      <!-- Minimal Footer -->
      <footer class="mt-20 py-10 border-t border-white/5 flex justify-between items-center text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em]">
        <div>Human-Agentic OS // 2026</div>
        <div class="flex gap-8">
          <span class="hover:text-white transition-colors cursor-pointer">Protocol</span>
          <span class="hover:text-white transition-colors cursor-pointer">Security</span>
          <span class="hover:text-white transition-colors cursor-pointer">Terminal</span>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { 
  ShoppingCart as ShoppingCartIcon, 
  CheckCircle as CheckCircleIcon,
  AlertCircle as AlertCircleIcon,
  X as XIcon
} from 'lucide-vue-next'

// Flash message state
const flashMessage = ref(null)

// Clear flash message
const clearFlashMessage = () => {
  flashMessage.value = null
}

onMounted(() => {
  const successMessage = document.querySelector('meta[name="flash-success"]')?.getAttribute('content')
  const errorMessage = document.querySelector('meta[name="flash-error"]')?.getAttribute('content')

  if (successMessage) {
    flashMessage.value = { type: 'success', message: successMessage }
    setTimeout(clearFlashMessage, 5000)
  } else if (errorMessage) {
    flashMessage.value = { type: 'error', message: errorMessage }
    setTimeout(clearFlashMessage, 5000)
  }
})
</script>

<style>
.page-enter-active, .page-leave-active {
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
.page-enter-from {
  opacity: 0;
  transform: translateY(30px) scale(0.98);
}
.page-leave-to {
  opacity: 0;
  transform: translateY(-30px) scale(1.02);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>