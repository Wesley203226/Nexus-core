<template>
  <section class="page-stack">
    <PageHeader
      eyebrow="Visao geral"
      title="Controle de estoque com foco em operacao"
      description="Acompanhe o total de itens, fornecedores ativos, tipos cadastrados e os pontos que ainda pedem atencao."
    >
      <template #actions>
        <RouterLink to="/products" class="btn-primary">
          <Package class="h-4 w-4" />
          <span>Itens</span>
        </RouterLink>
        <RouterLink to="/suppliers" class="btn-secondary">
          <Truck class="h-4 w-4" />
          <span>Fornecedores</span>
        </RouterLink>
        <RouterLink to="/types" class="btn-secondary">
          <Tags class="h-4 w-4" />
          <span>Tipos</span>
        </RouterLink>
      </template>
    </PageHeader>

    <div class="stats-grid">
      <MetricCard
        title="Itens cadastrados"
        :value="String(products.length)"
        description="Volume total de produtos disponiveis no cadastro."
        badge="Estoque"
        tone="accent"
      />
      <MetricCard
        title="Valor estimado"
        :value="inventoryValue"
        description="Soma de preco unitario multiplicado pelo saldo de estoque."
        badge="Financeiro"
        tone="neutral"
      />
      <MetricCard
        title="Fornecedores ativos"
        :value="String(activeSuppliers)"
        description="Parceiros prontos para atender novos pedidos."
        badge="Rede"
        tone="success"
      />
      <MetricCard
        title="Tipos cadastrados"
        :value="String(types.length)"
        description="Categorias usadas para organizar os itens."
        badge="Catalogo"
        tone="neutral"
      />
    </div>

    <div class="chart-grid">
      <section class="panel section-stack">
        <div class="panel-header">
          <div>
            <p class="eyebrow">Graficos</p>
            <h2 class="section-title">Produtos por tipo</h2>
          </div>
          <span class="chip chip-neutral">{{ types.length }} tipo(s)</span>
        </div>

        <div v-if="loading" class="loading-state">Preparando grafico...</div>
        <div v-else class="chart-frame">
          <Doughnut :data="typeChartData" :options="doughnutOptions" />
        </div>
      </section>

      <section class="panel section-stack">
        <div class="panel-header">
          <div>
            <p class="eyebrow">Financeiro</p>
            <h2 class="section-title">Valor de estoque por tipo</h2>
          </div>
          <span class="chip chip-neutral">{{ inventoryValue }}</span>
        </div>

        <div v-if="loading" class="loading-state">Calculando valores...</div>
        <div v-else class="chart-frame">
          <Bar :data="valueChartData" :options="barOptions" />
        </div>
      </section>
    </div>

    <div class="dashboard-grid">
      <section class="panel section-stack">
        <div class="panel-header">
          <div>
            <p class="eyebrow">Acompanhamento</p>
            <h2 class="section-title">Itens com estoque baixo</h2>
          </div>
          <span class="chip chip-warning">{{ lowStockProducts.length }} alerta(s)</span>
        </div>

        <div v-if="loading" class="loading-state">Carregando indicadores...</div>

        <div v-else-if="lowStockProducts.length" class="data-list">
          <article
            v-for="product in lowStockProducts"
            :key="product.id"
            class="simple-list-item"
          >
            <div class="item-visual">
              <div class="avatar">
                <img v-if="product.photo_url" :src="product.photo_url" alt="" />
                <span v-else>{{ getInitials(product.name) }}</span>
              </div>
              <div class="min-w-0">
                <p class="item-title">{{ product.name }}</p>
                <p class="item-subtitle">
                  {{ product.type?.name || 'Sem tipo' }} · {{ product.supplier?.name || 'Sem fornecedor' }}
                </p>
              </div>
            </div>

            <div class="text-right">
              <p class="item-title">{{ product.quantity }} un.</p>
              <p class="item-subtitle">{{ formatCurrency(product.price) }}</p>
            </div>
          </article>
        </div>

        <div v-else class="empty-state">
          <p class="empty-title">Nenhum item em risco agora.</p>
          <p class="empty-description">O estoque esta equilibrado para os produtos cadastrados.</p>
        </div>
      </section>

      <section class="panel section-stack">
        <div class="panel-header">
          <div>
            <p class="eyebrow">Rede de parceiros</p>
            <h2 class="section-title">Fornecedores recentes</h2>
          </div>
          <span class="chip chip-neutral">{{ suppliers.length }} total</span>
        </div>

        <div v-if="loading" class="loading-state">Sincronizando fornecedores...</div>

        <div v-else-if="recentSuppliers.length" class="data-list">
          <article
            v-for="supplier in recentSuppliers"
            :key="supplier.id"
            class="simple-list-item"
          >
            <div class="item-visual">
              <div class="avatar">
                <img v-if="supplier.profile_photo_url" :src="supplier.profile_photo_url" alt="" />
                <span v-else>{{ getInitials(supplier.name) }}</span>
              </div>
              <div class="min-w-0">
                <p class="item-title">{{ supplier.name }}</p>
                <p class="item-subtitle">{{ supplier.contact_name || 'Sem responsavel informado' }}</p>
              </div>
            </div>

            <div class="text-right">
              <span :class="['chip', supplier.is_active ? 'chip-success' : 'chip-danger']">
                {{ supplier.is_active ? 'Ativo' : 'Inativo' }}
              </span>
            </div>
          </article>
        </div>

        <div v-else class="empty-state">
          <p class="empty-title">Nenhum fornecedor cadastrado ainda.</p>
          <p class="empty-description">Crie o primeiro fornecedor para completar o fluxo de compras.</p>
        </div>
      </section>
    </div>

    <div class="split-grid">
      <section class="panel section-stack">
        <div class="panel-header">
          <div>
            <p class="eyebrow">Movimentacao</p>
            <h2 class="section-title">Ultimos itens adicionados</h2>
          </div>
          <RouterLink to="/products" class="text-link">Abrir listagem</RouterLink>
        </div>

        <div v-if="loading" class="loading-state">Montando historico recente...</div>

        <div v-else-if="recentProducts.length" class="data-list">
          <article
            v-for="product in recentProducts"
            :key="product.id"
            class="simple-list-item"
          >
            <div class="min-w-0">
              <p class="item-title">{{ product.name }}</p>
              <p class="item-subtitle">
                {{ formatDateTime(product.created_at) }} · {{ product.type?.name || 'Sem tipo' }}
              </p>
            </div>

            <div class="text-right">
              <p class="item-title">{{ formatCurrency(product.price) }}</p>
              <p class="item-subtitle">{{ product.quantity }} un.</p>
            </div>
          </article>
        </div>

        <div v-else class="empty-state">
          <p class="empty-title">Sem movimentacao registrada.</p>
          <p class="empty-description">Quando novos itens forem criados, eles aparecerao aqui.</p>
        </div>
      </section>

      <section class="panel section-stack">
        <div class="panel-header">
          <div>
            <p class="eyebrow">Organizacao</p>
            <h2 class="section-title">Tipos mais usados</h2>
          </div>
          <RouterLink to="/types" class="text-link">Editar tipos</RouterLink>
        </div>

        <div v-if="loading" class="loading-state">Lendo catalogo...</div>

        <div v-else-if="types.length" class="type-summary-grid">
          <article
            v-for="type in highlightedTypes"
            :key="type.id"
            class="type-summary-card"
          >
            <p class="type-summary-name">{{ type.name }}</p>
            <p class="type-summary-description">
              {{ type.description || 'Sem descricao cadastrada.' }}
            </p>
            <span class="chip chip-neutral">{{ type.products_count }} item(ns)</span>
          </article>
        </div>

        <div v-else class="empty-state">
          <p class="empty-title">Voce ainda nao cadastrou tipos.</p>
          <p class="empty-description">Use os tipos para manter o estoque mais organizado.</p>
        </div>
      </section>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import {
  ArcElement,
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  Tooltip,
} from 'chart.js'
import { Bar, Doughnut } from 'vue-chartjs'
import { RouterLink } from 'vue-router'
import { Package, Tags, Truck } from 'lucide-vue-next'
import MetricCard from '../components/MetricCard.vue'
import PageHeader from '../components/PageHeader.vue'
import { pushNotification } from '../composables/useNotifications'
import { formatCurrency, formatDateTime, getInitials } from '../utils/formatters'
import { getApiMessage } from '../utils/http'

ChartJS.register(ArcElement, BarElement, CategoryScale, LinearScale, Legend, Tooltip)

const loading = ref(true)
const products = ref([])
const suppliers = ref([])
const types = ref([])

const chartColors = ['#f28c28', '#2bb38a', '#f3b543', '#ef6a62', '#7dd3fc', '#c084fc']

const inventoryValue = computed(() => {
  const total = products.value.reduce((sum, product) => {
    return sum + Number(product.price) * Number(product.quantity)
  }, 0)

  return formatCurrency(total)
})

const activeSuppliers = computed(() => suppliers.value.filter((supplier) => supplier.is_active).length)

const lowStockProducts = computed(() =>
  [...products.value]
    .filter((product) => Number(product.quantity) <= 5)
    .sort((left, right) => Number(left.quantity) - Number(right.quantity))
    .slice(0, 5)
)

const recentSuppliers = computed(() => suppliers.value.slice(0, 5))
const recentProducts = computed(() => products.value.slice(0, 6))

const highlightedTypes = computed(() =>
  [...types.value]
    .sort((left, right) => Number(right.products_count) - Number(left.products_count))
    .slice(0, 6)
)

const typeChartData = computed(() => ({
  labels: types.value.map((type) => type.name),
  datasets: [
    {
      data: types.value.map((type) => Number(type.products_count || 0)),
      backgroundColor: chartColors,
      borderColor: 'rgba(12, 18, 25, 0.95)',
      borderWidth: 2,
    },
  ],
}))

const valueChartData = computed(() => {
  const labels = types.value.map((type) => type.name)
  const values = types.value.map((type) => {
    return products.value
      .filter((product) => Number(product.type_id) === Number(type.id))
      .reduce((sum, product) => sum + Number(product.price) * Number(product.quantity), 0)
  })

  return {
    labels,
    datasets: [
      {
        label: 'Valor em estoque',
        data: values,
        backgroundColor: '#f28c28',
        borderRadius: 10,
      },
    ],
  }
})

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        color: '#f7f2eb',
        boxWidth: 12,
      },
    },
  },
}

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
    tooltip: {
      callbacks: {
        label: (context) => formatCurrency(context.raw),
      },
    },
  },
  scales: {
    x: {
      ticks: {
        color: '#96a5b8',
      },
      grid: {
        color: 'rgba(255, 255, 255, 0.06)',
      },
    },
    y: {
      ticks: {
        color: '#96a5b8',
        callback: (value) => formatCurrency(value),
      },
      grid: {
        color: 'rgba(255, 255, 255, 0.06)',
      },
    },
  },
}

async function fetchDashboard() {
  loading.value = true

  try {
    const [productsResponse, suppliersResponse, typesResponse] = await Promise.all([
      axios.get('/api/products'),
      axios.get('/api/suppliers'),
      axios.get('/api/types'),
    ])

    products.value = productsResponse.data
    suppliers.value = suppliersResponse.data
    types.value = typesResponse.data
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel carregar o dashboard.'), 'error')
  } finally {
    loading.value = false
  }
}

onMounted(fetchDashboard)
</script>
