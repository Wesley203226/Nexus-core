<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-white">Fornecedores</h1>
      <button @click="showModal=true" class="z-btn">Novo</button>
    </div>
    <div class="z-card rounded-xl">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-white/10">
            <th class="px-6 py-4 text-left text-slate-400">Nome</th>
            <th class="px-6 py-4 text-left text-slate-400">E-mail</th>
            <th class="px-6 py-4 text-left text-slate-400">Status</th>
            <th class="px-6 py-4 text-right text-slate-400">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in suppliers" :key="s.id" class="border-b border-white/5 hover:bg-white/5">
            <td class="px-6 py-4 text-white">{{ s.name }}</td>
            <td class="px-6 py-4 text-slate-300">{{ s.email }}</td>
            <td class="px-6 py-4" :class="s.is_active ? 'text-emerald-300' : 'text-rose-400'">{{ s.is_active ? 'Ativo' : 'Inativo' }}</td>
            <td class="px-6 py-4 text-right"><button @click="del(s.id)" class="text-red-400">Deletar</button></td>
          </tr>
          <tr v-if="!suppliers.length"><td colspan="4" class="px-6 py-10 text-center text-slate-500">Nenhum</td></tr>
        </tbody>
      </table>
    </div>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" @click.self="showModal=false">
      <div class="z-card rounded-2xl p-6 w-full max-w-lg">
        <h2 class="text-xl font-bold text-white mb-4">Novo Fornecedor</h2>
        <input v-model="form.name" placeholder="Nome" class="z-input w-full mb-3" />
        <input v-model="form.email" placeholder="Email" class="z-input w-full mb-3" />
        <input v-model="form.password" type="password" placeholder="Senha" class="z-input w-full mb-4" />
        <div class="flex gap-3">
          <button @click="save" class="z-btn flex-1">Salvar</button>
          <button @click="showModal=false" class="z-btn-secondary flex-1">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const suppliers = ref([])
const showModal = ref(false)
const form = ref({ name: '', email: '', password: '' })

const fetch_data = async () => {
  try {
    const res = await axios.get('/api/suppliers')
    suppliers.value = res.data || []
  } catch (e) {
    console.log('Erro:', e)
  }
}

const save = async () => {
  try {
    await axios.post('/api/suppliers', form.value)
    form.value = { name: '', email: '', password: '' }
    showModal.value = false
    await fetch_data()
  } catch (e) {
    console.log('Erro:', e)
  }
}

const del = async (id) => {
  try {
    await axios.delete(`/api/suppliers/${id}`)
    await fetch_data()
  } catch (e) {
    console.log('Erro:', e)
  }
}

onMounted(() => fetch_data())
</script>
