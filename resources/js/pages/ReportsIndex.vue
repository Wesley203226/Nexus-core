<template>
  <section class="page-stack">
    <PageHeader
      eyebrow="Relatorios"
      title="Analise consolidada do estoque"
      description="Acompanhe valores, quantidades, fornecedores, tipos e produtos que precisam de atencao em um resumo gerado pela API."
    >
      <template #actions>
        <button type="button" class="btn-primary" :disabled="loading" @click="fetchReport">
          <RefreshCcw class="h-4 w-4" />
          <span>Atualizar</span>
        </button>
      </template>
    </PageHeader>

    <div class="stats-grid">
      <MetricCard
        title="Valor total"
        :value="formatCurrency(summary.inventory_value)"
        description="Preco unitario multiplicado pela quantidade em estoque."
        badge="Financeiro"
        tone="accent"
      />
      <MetricCard
        title="Itens"
        :value="String(summary.products_count || 0)"
        description="Produtos cadastrados no sistema."
        badge="Estoque"
        tone="neutral"
      />
      <MetricCard
        title="Estoque baixo"
        :value="String(summary.low_stock_count || 0)"
        description="Produtos com cinco unidades ou menos."
        badge="Alerta"
        tone="warning"
      />
      <MetricCard
        title="Fornecedores ativos"
        :value="String(summary.active_suppliers_count || 0)"
        description="Parceiros disponiveis para operacao."
        badge="Rede"
        tone="success"
      />
    </div>

    <div class="split-grid">
      <section class="panel section-stack">
        <div class="panel-header">
          <div>
            <p class="eyebrow">Categorias</p>
            <h2 class="section-title">Resumo por tipo</h2>
          </div>
          <span class="chip chip-neutral">{{ productsByType.length }} registro(s)</span>
        </div>

        <div v-if="loading" class="loading-state">Carregando relatorio...</div>
        <div v-else-if="productsByType.length" class="report-table">
          <article v-for="type in productsByType" :key="type.id" class="report-row">
            <div>
              <p class="item-title">{{ type.name }}</p>
              <p class="item-subtitle">{{ type.products_count }} produto(s), {{ type.stock_quantity }} unidade(s)</p>
            </div>
            <p class="item-title">{{ formatCurrency(type.stock_value) }}</p>
          </article>
        </div>
        <div v-else class="empty-state">
          <p class="empty-title">Sem tipos cadastrados.</p>
          <p class="empty-description">Cadastre tipos para gerar a analise por categoria.</p>
        </div>
      </section>

      <section class="panel section-stack">
        <div class="panel-header">
          <div>
            <p class="eyebrow">Fornecedores</p>
            <h2 class="section-title">Resumo por fornecedor</h2>
          </div>
          <span class="chip chip-neutral">{{ productsBySupplier.length }} registro(s)</span>
        </div>

        <div v-if="loading" class="loading-state">Consolidando fornecedores...</div>
        <div v-else-if="productsBySupplier.length" class="report-table">
          <article v-for="supplier in productsBySupplier" :key="supplier.id" class="report-row">
            <div>
              <p class="item-title">{{ supplier.name }}</p>
              <p class="item-subtitle">
                {{ supplier.products_count }} produto(s), {{ supplier.stock_quantity }} unidade(s)
              </p>
            </div>
            <div class="text-right">
              <span :class="['chip', supplier.is_active ? 'chip-success' : 'chip-danger']">
                {{ supplier.is_active ? 'Ativo' : 'Inativo' }}
              </span>
              <p class="item-title report-value">{{ formatCurrency(supplier.stock_value) }}</p>
            </div>
          </article>
        </div>
        <div v-else class="empty-state">
          <p class="empty-title">Sem fornecedores cadastrados.</p>
          <p class="empty-description">Cadastre fornecedores para acompanhar a rede de parceiros.</p>
        </div>
      </section>
    </div>

    <section class="panel section-stack">
      <div class="panel-header">
        <div>
          <p class="eyebrow">Atencao</p>
          <h2 class="section-title">Produtos com estoque baixo</h2>
        </div>
        <span class="chip chip-warning">{{ lowStockProducts.length }} alerta(s)</span>
      </div>

      <div v-if="loading" class="loading-state">Buscando produtos criticos...</div>
      <div v-else-if="lowStockProducts.length" class="data-list">
        <article v-for="product in lowStockProducts" :key="product.id" class="simple-list-item">
          <div>
            <p class="item-title">{{ product.name }}</p>
            <p class="item-subtitle">
              {{ product.type?.name || 'Sem tipo' }} / {{ product.supplier?.name || 'Sem fornecedor' }}
            </p>
          </div>
          <div class="text-right">
            <span class="chip chip-warning">{{ product.quantity }} un.</span>
            <p class="item-subtitle">{{ formatCurrency(product.price) }}</p>
          </div>
        </article>
      </div>
      <div v-else class="empty-state">
        <p class="empty-title">Nenhum produto em estoque baixo.</p>
        <p class="empty-description">Os produtos cadastrados estao acima do limite de alerta.</p>
      </div>
    </section>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import { RefreshCcw } from 'lucide-vue-next'
import MetricCard from '../components/MetricCard.vue'
import PageHeader from '../components/PageHeader.vue'
import { pushNotification } from '../composables/useNotifications'
import { formatCurrency } from '../utils/formatters'
import { getApiMessage } from '../utils/http'

const loading = ref(true)
const report = ref({
  summary: {},
  products_by_type: [],
  products_by_supplier: [],
  low_stock_products: [],
})

const summary = computed(() => report.value.summary || {})
const productsByType = computed(() => report.value.products_by_type || [])
const productsBySupplier = computed(() => report.value.products_by_supplier || [])
const lowStockProducts = computed(() => report.value.low_stock_products || [])

async function fetchReport() {
  loading.value = true

  try {
    const response = await axios.get('/api/reports/inventory')
    report.value = response.data
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel carregar o relatorio.'), 'error')
  } finally {
    loading.value = false
  }
}

onMounted(fetchReport)
</script>
