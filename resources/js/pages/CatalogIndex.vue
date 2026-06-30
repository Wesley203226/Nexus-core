<template>
  <section class="page-stack">
    <PageHeader
      eyebrow="Area publica"
      title="Catalogo de produtos"
      description="Consulte os itens cadastrados, filtre por tipo e veja informacoes basicas de estoque e fornecedor."
    />

    <section class="panel section-stack">
      <div class="panel-header">
        <div>
          <p class="eyebrow">Busca publica</p>
          <h2 class="section-title">Encontre produtos no catalogo</h2>
        </div>
        <span class="chip chip-neutral">{{ filteredProducts.length }} resultado(s)</span>
      </div>

      <div class="control-grid">
        <div>
          <label class="field-label">Buscar produto</label>
          <input
            v-model="filters.search"
            type="text"
            class="input-field"
            placeholder="Nome, descricao ou fornecedor"
          />
        </div>

        <div>
          <label class="field-label">Tipo</label>
          <select v-model="filters.type" class="select-field">
            <option value="">Todos os tipos</option>
            <option v-for="type in types" :key="type.id" :value="String(type.id)">
              {{ type.name }}
            </option>
          </select>
        </div>
      </div>
    </section>

    <div v-if="loading" class="loading-state">Carregando catalogo...</div>

    <section v-else-if="filteredProducts.length" class="catalog-grid">
      <article v-for="product in filteredProducts" :key="product.id" class="catalog-card">
        <div class="catalog-card-image">
          <img v-if="product.photo_url" :src="product.photo_url" alt="" />
          <span v-else>{{ getInitials(product.name) }}</span>
        </div>

        <div class="section-stack">
          <div>
            <p class="eyebrow">{{ product.type?.name || 'Sem tipo' }}</p>
            <h2 class="section-title catalog-title">{{ product.name }}</h2>
            <p class="item-subtitle">{{ product.description || 'Produto sem descricao cadastrada.' }}</p>
          </div>

          <div class="catalog-meta">
            <span :class="['chip', Number(product.quantity) <= 5 ? 'chip-warning' : 'chip-success']">
              {{ product.quantity }} unidade(s)
            </span>
            <span class="chip chip-neutral">{{ formatCurrency(product.price) }}</span>
          </div>

          <p class="item-subtitle">
            Fornecedor: <strong class="strong-text">{{ product.supplier?.name || 'Nao informado' }}</strong>
          </p>
        </div>
      </article>
    </section>

    <div v-else class="empty-state">
      <p class="empty-title">Nenhum produto encontrado.</p>
      <p class="empty-description">Ajuste os filtros para visualizar outros itens do catalogo.</p>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import PageHeader from '../components/PageHeader.vue'
import { pushNotification } from '../composables/useNotifications'
import { formatCurrency, getInitials } from '../utils/formatters'
import { getApiMessage } from '../utils/http'

const loading = ref(true)
const products = ref([])
const types = ref([])

const filters = reactive({
  search: '',
  type: '',
})

const filteredProducts = computed(() => {
  return products.value.filter((product) => {
    const searchTerm = filters.search.trim().toLowerCase()
    const matchesSearch =
      !searchTerm ||
      product.name.toLowerCase().includes(searchTerm) ||
      (product.description || '').toLowerCase().includes(searchTerm) ||
      (product.supplier?.name || '').toLowerCase().includes(searchTerm)

    const matchesType = !filters.type || String(product.type_id) === filters.type

    return matchesSearch && matchesType
  })
})

async function fetchCatalog() {
  loading.value = true

  try {
    const [productsResponse, typesResponse] = await Promise.all([
      axios.get('/api/products'),
      axios.get('/api/types'),
    ])

    products.value = productsResponse.data
    types.value = typesResponse.data
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel carregar o catalogo.'), 'error')
  } finally {
    loading.value = false
  }
}

onMounted(fetchCatalog)
</script>
