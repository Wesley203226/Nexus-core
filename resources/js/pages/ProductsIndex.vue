<template>
  <div class="animate-in fade-in duration-1000">
    <!-- Static Header -->
    <div class="z-form-header">
      <div>
        <h1 class="text-xl font-black text-white uppercase tracking-tighter leading-none">Fluxo de Estoque</h1>
        <p class="text-slate-400 text-[8px] font-black uppercase tracking-[0.2em] mt-1.5">Gestão de Unidades</p>
      </div>
      <router-link to="/products/new" class="z-btn !py-2.5 !px-5 !text-[9px]">
        <PlusIcon class="w-4 h-4" />
        <span>Novo Item</span>
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-32 z-card max-w-4xl mx-auto">
      <div class="w-12 h-12 border-4 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin"></div>
      <p class="mt-6 text-[8px] font-black text-slate-400 uppercase tracking-[0.3em]">Sincronizando Dados...</p>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
      <div 
        v-for="product in products" 
        :key="product.id" 
        class="z-card p-6 group flex flex-col min-h-[280px] relative overflow-hidden"
      >
        <!-- Video Background -->
        <video 
          class="absolute inset-0 w-full h-full object-cover opacity-20 pointer-events-none"
          autoplay muted loop playsinline
        >
          <source src="/videos/12421669_3840_2160_30fps.mp4" type="video/mp4">
        </video>
        <!-- Color Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/30 via-slate-900/80 to-rose-900/30"></div>
        
        <!-- Content (relative + z-index) -->
        <div class="relative z-10">
        <div class="flex justify-between items-start mb-6">
          <div class="px-2 py-0.5 rounded-md bg-white/5 border border-white/5 text-[8px] font-black text-indigo-400 uppercase tracking-tighter">
            {{ product.type?.name || 'Geral' }}
          </div>
          <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <router-link :to="`/products/${product.id}/edit`" class="p-1.5 hover:bg-white/5 rounded-lg transition-colors">
              <Edit3Icon class="w-3.5 h-3.5 text-slate-400" />
            </router-link>
            <button @click="confirmDelete(product)" class="p-1.5 hover:bg-rose-500/10 rounded-lg transition-colors">
              <Trash2Icon class="w-3.5 h-3.5 text-rose-500" />
            </button>
          </div>
        </div>

        <h3 class="text-xl font-black text-white tracking-tight mb-2 group-hover:text-indigo-400 transition-colors">
          {{ product.name }}
        </h3>
        <p class="text-slate-400 text-xs leading-relaxed line-clamp-3 mb-6">
          {{ product.description || 'Nenhuma descrição técnica fornecida para esta unidade.' }}
        </p>

        <div class="mt-auto pt-6 border-t border-white/5 flex justify-between items-end">
          <div>
            <p class="text-[7px] font-black text-slate-500 uppercase tracking-widest mb-1">Avaliação</p>
            <p class="text-lg font-black text-white">R$ {{ formatPrice(product.price) }}</p>
          </div>
          <div class="text-right">
            <p class="text-[7px] font-black text-slate-500 uppercase tracking-widest mb-1">Capacidade</p>
            <p class="text-lg font-black" :class="product.quantity > 0 ? 'text-emerald-400' : 'text-rose-500'">
              {{ product.quantity }}
            </p>
          </div>
        </div>
        </div>
      </div>
    </div>

    <!-- Minimal Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
      <div class="absolute inset-0 bg-black/80 backdrop-blur-md" @click="showDeleteModal = false"></div>
      <div class="z-card max-w-md w-full p-10 relative animate-in zoom-in duration-300 border-rose-500/20">
        <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-4">Expurgar Unidade?</h3>
        <p class="text-slate-400 text-sm leading-relaxed mb-10">
          Esta ação desconectará permanentemente <span class="text-white font-bold">"{{ productToDelete?.name }}"</span> do Núcleo Nexus.
        </p>
        <div class="flex gap-4">
          <button @click="showDeleteModal = false" class="flex-1 z-btn-secondary py-4">Abortar</button>
          <button @click="deleteProduct" class="flex-1 z-btn bg-rose-600 py-4">Confirmar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import {
  Edit3 as Edit3Icon,
  Trash2 as Trash2Icon,
  Plus as PlusIcon
} from 'lucide-vue-next'

const products = ref([])
const loading = ref(true)
const showDeleteModal = ref(false)
const productToDelete = ref(null)

const fetchProducts = async () => {
  try {
    const response = await axios.get('/api/products')
    products.value = response.data
  } catch (error) {
    console.error('Node sync failed:', error)
  } finally {
    loading.value = false
  }
}

const formatPrice = (price) => {
  return parseFloat(price).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const deleteProduct = async () => {
  if (!productToDelete.value) return
  try {
    await axios.delete(`/api/products/${productToDelete.value.id}`)
    products.value = products.value.filter(p => p.id !== productToDelete.value.id)
    showDeleteModal.value = false
    productToDelete.value = null
  } catch (error) {
    console.error('Purge failed:', error)
  }
}

onMounted(fetchProducts)
</script>
