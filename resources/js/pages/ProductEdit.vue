<template>
  <div class="animate-in slide-in-from-bottom duration-1000">
    <!-- Static Header for Form -->
    <div class="z-form-header">
      <div class="flex items-center gap-4">
        <router-link to="/products" class="z-btn-secondary !p-2.5 !rounded-xl">
          <ArrowLeftIcon class="w-4 h-4" />
        </router-link>
        <div>
          <h1 class="text-xl font-black text-white uppercase tracking-tighter leading-none">Atualizar Node</h1>
          <p class="text-slate-500 text-[8px] font-black uppercase tracking-[0.2em] mt-1.5">Modificação de Contexto</p>
        </div>
      </div>
      <div class="flex gap-3">
        <button type="button" @click="submitForm" :disabled="submitting" class="z-btn !py-2.5 !px-6 !text-[9px]">
          <Loader2Icon v-if="submitting" class="w-4 h-4 animate-spin" />
          <span v-else>Sincronizar Alterações</span>
        </button>
      </div>
    </div>

    <div v-if="loading" class="flex flex-col items-center justify-center py-32 z-card max-w-2xl mx-auto">
      <div class="w-12 h-12 border-4 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin"></div>
      <p class="mt-6 text-[8px] font-black text-slate-500 uppercase tracking-[0.3em]">Recuperando Dados...</p>
    </div>

    <div v-else class="max-w-3xl mx-auto">
      <form @submit.prevent="submitForm" class="space-y-8">
        <!-- Section 1: Identificação -->
        <div class="z-glass-section space-y-8">
          <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <div class="w-2 h-6 bg-indigo-500 rounded-full"></div>
            <h2 class="text-sm font-black text-white uppercase tracking-widest">Identificação do Node</h2>
          </div>
          
          <div class="grid grid-cols-1 gap-8">
            <div class="flex flex-col gap-3">
              <label for="name" class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Nome do Produto</label>
              <input id="name" v-model="form.name" type="text" required class="z-input" placeholder="NOME DO PRODUTO..." />
            </div>

            <div class="flex flex-col gap-3">
              <label for="description" class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Descrição Técnica</label>
              <textarea id="description" v-model="form.description" rows="3" class="z-input" placeholder="DESCRIÇÃO DETALHADA..."></textarea>
            </div>
          </div>
        </div>

        <!-- Section 2: Parâmetros e Classe -->
        <div class="z-glass-section space-y-8">
          <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <div class="w-2 h-6 bg-indigo-500 rounded-full"></div>
            <h2 class="text-sm font-black text-white uppercase tracking-widest">Parâmetros Operacionais</h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="flex flex-col gap-3">
              <label for="quantity" class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Capacidade (Estoque)</label>
              <input id="quantity" v-model.number="form.quantity" type="number" required class="z-input" placeholder="00" />
            </div>

            <div class="flex flex-col gap-3">
              <label for="price" class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Avaliação Unitária (R$)</label>
              <input id="price" v-model.number="form.price" type="number" step="0.01" required class="z-input" placeholder="0.00" />
            </div>

            <div class="md:col-span-2 flex flex-col gap-3">
              <label for="type_id" class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">Classe de Protocolo</label>
              <div class="flex gap-3">
                <select id="type_id" v-model.number="form.type_id" required class="z-input appearance-none cursor-pointer">
                  <option v-for="type in types" :key="type.id" :value="type.id" class="bg-slate-900">{{ type.name.toUpperCase() }}</option>
                </select>
                <button type="button" @click="showTypeModal = true" class="z-btn !p-4">
                  <PlusIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- New Type Modal -->
    <div v-if="showTypeModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
      <div class="absolute inset-0 bg-black/80 backdrop-blur-md" @click="showTypeModal = false"></div>
      <div class="z-card max-w-md w-full p-12 relative animate-in zoom-in duration-300">
        <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-8">Novo Protocolo</h3>
        <div class="space-y-6">
          <div class="space-y-4">
            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2">Nome da Classe</label>
            <input
              v-model="newTypeName"
              type="text"
              class="z-input"
              placeholder="DIGITE A CLASSE..."
              @keyup.enter="createNewType"
            />
          </div>
          <div class="flex gap-4 pt-6">
            <button @click="showTypeModal = false" class="flex-1 z-btn-secondary py-4">Voltar</button>
            <button 
              @click="createNewType" 
              :disabled="!newTypeName || creatingType"
              class="flex-1 z-btn py-4"
            >
              <Loader2Icon v-if="creatingType" class="w-5 h-5 animate-spin" />
              <span>Registrar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { 
  ArrowLeft as ArrowLeftIcon,
  Plus as PlusIcon,
  Loader2 as Loader2Icon
} from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()
const types = ref([])
const loading = ref(true)
const submitting = ref(false)
const showTypeModal = ref(false)
const newTypeName = ref('')
const creatingType = ref(false)

const form = ref({
  name: '',
  description: '',
  quantity: '',
  price: '',
  type_id: ''
})

const errors = ref({})

const fetchProduct = async () => {
  try {
    const response = await axios.get(`/api/products/${route.params.id}`)
    const product = response.data
    form.value = {
      name: product.name,
      description: product.description || '',
      quantity: product.quantity,
      price: product.price,
      type_id: product.type_id
    }
  } catch (error) {
    console.error('Node retrieval failed:', error)
    router.push('/products')
  } finally {
    loading.value = false
  }
}

const fetchTypes = async () => {
  try {
    const response = await axios.get('/api/types')
    types.value = response.data
  } catch (error) {
    console.error('Protocol class sync failed:', error)
  }
}

const createNewType = async () => {
  if (!newTypeName.value) return
  creatingType.value = true
  try {
    const response = await axios.post('/api/types', { name: newTypeName.value })
    types.value.push(response.data)
    form.value.type_id = response.data.id
    showTypeModal.value = false
    newTypeName.value = ''
  } catch (error) {
    console.error('Registration failed:', error)
  } finally {
    creatingType.value = false
  }
}

const submitForm = async () => {
  submitting.value = true
  errors.value = {}
  try {
    await axios.put(`/api/products/${route.params.id}`, form.value)
    router.push('/products')
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const apiErrors = error.response.data.errors
      Object.keys(apiErrors).forEach(key => {
        errors.value[key] = apiErrors[key][0]
      })
    } else {
      console.error('Sync failed:', error)
    }
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  fetchTypes()
  fetchProduct()
})
</script>
