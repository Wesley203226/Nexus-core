<template>
  <section class="page-stack">
    <PageHeader
      eyebrow="Cadastro de itens"
      title="Itens com cadastro, foto e vinculo por fornecedor"
      description="Cadastre produtos, ajuste estoque, relacione tipos e fornecedores e mantenha a foto do item atualizada."
    >
      <template #actions>
        <button type="button" class="btn-primary" @click="openCreateModal">
          <Plus class="h-4 w-4" />
          <span>Novo item</span>
        </button>
      </template>
    </PageHeader>

    <div class="stats-grid">
      <MetricCard
        title="Produtos"
        :value="String(products.length)"
        description="Total de itens cadastrados na base."
        badge="Catalogo"
        tone="accent"
      />
      <MetricCard
        title="Estoque baixo"
        :value="String(lowStockCount)"
        description="Produtos com cinco unidades ou menos."
        badge="Atencao"
        tone="warning"
      />
      <MetricCard
        title="Com fornecedor"
        :value="String(productsWithSupplier)"
        description="Itens ligados a um fornecedor ativo."
        badge="Rede"
        tone="success"
      />
      <MetricCard
        title="Valor total"
        :value="inventoryValue"
        description="Preco unitario multiplicado pelo saldo de estoque."
        badge="Financeiro"
        tone="neutral"
      />
    </div>

    <section class="panel section-stack">
      <div class="panel-header">
        <div>
          <p class="eyebrow">Filtro rapido</p>
          <h2 class="section-title">Busque e organize os itens</h2>
        </div>
        <span class="chip chip-neutral">{{ filteredProducts.length }} resultado(s)</span>
      </div>

      <div class="control-grid">
        <div>
          <label class="field-label">Buscar item</label>
          <input
            v-model="filters.search"
            type="text"
            class="input-field"
            placeholder="Digite nome, descricao ou fornecedor"
          />
        </div>

        <div>
          <label class="field-label">Filtrar por tipo</label>
          <select v-model="filters.type" class="select-field">
            <option value="">Todos os tipos</option>
            <option v-for="type in types" :key="type.id" :value="String(type.id)">
              {{ type.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="field-label">Filtrar por fornecedor</label>
          <select v-model="filters.supplier" class="select-field">
            <option value="">Todos os fornecedores</option>
            <option v-for="supplier in suppliers" :key="supplier.id" :value="String(supplier.id)">
              {{ supplier.name }}
            </option>
          </select>
        </div>
      </div>
    </section>

    <section class="panel section-stack">
      <div class="panel-header">
        <div>
          <p class="eyebrow">Listagem principal</p>
          <h2 class="section-title">Itens cadastrados</h2>
        </div>
      </div>

      <div v-if="loading" class="loading-state">Carregando itens...</div>

      <div v-else-if="filteredProducts.length" class="data-list">
        <article v-for="product in filteredProducts" :key="product.id" class="data-row">
          <div class="item-visual">
            <div class="thumb">
              <img v-if="product.photo_url" :src="product.photo_url" alt="" />
              <span v-else>{{ getInitials(product.name) }}</span>
            </div>

            <div class="min-w-0">
              <p class="item-title">{{ product.name }}</p>
              <p class="item-subtitle">{{ product.description || 'Sem descricao cadastrada.' }}</p>
              <p class="item-meta">
                Atualizado em {{ formatDate(product.updated_at) }}
              </p>
            </div>
          </div>

          <div>
            <p class="mini-label">Tipo</p>
            <span class="chip chip-neutral">{{ product.type?.name || 'Sem tipo' }}</span>
          </div>

          <div>
            <p class="mini-label">Fornecedor</p>
            <p class="item-subtitle strong-text">
              {{ product.supplier?.name || 'Nao informado' }}
            </p>
          </div>

          <div>
            <p class="mini-label">Estoque</p>
            <span :class="['chip', Number(product.quantity) <= 5 ? 'chip-warning' : 'chip-success']">
              {{ product.quantity }} un.
            </span>
          </div>

          <div>
            <p class="mini-label">Preco</p>
            <p class="item-title">{{ formatCurrency(product.price) }}</p>
          </div>

          <div class="row-actions">
            <button type="button" class="icon-btn" @click="openEditModal(product)">
              <Pencil class="h-4 w-4" />
            </button>
            <button type="button" class="icon-btn danger" @click="confirmDelete(product)">
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </article>
      </div>

      <div v-else class="empty-state">
        <p class="empty-title">Nenhum item encontrado.</p>
        <p class="empty-description">
          Ajuste os filtros ou crie um novo item para iniciar o estoque.
        </p>
      </div>
    </section>

    <AppModal
      :open="formModalOpen"
      :title="editingId ? 'Editar item' : 'Novo item'"
      eyebrow="Formulario"
      description="Preencha os dados do item, selecione o tipo, vincule um fornecedor e envie a foto do produto."
      size="xl"
      @close="closeFormModal"
    >
      <form class="section-stack" @submit.prevent="saveProduct">
        <div class="form-grid">
          <div class="full-span">
            <ImageUploadField
              label="Foto do item"
              hint="PNG ou JPG com ate 4 MB."
              :preview-url="photoPreview"
              button-text="Selecionar foto"
              empty-text="Nenhuma foto selecionada"
              @select="handlePhotoSelected"
              @clear="clearPhoto"
            />
          </div>

          <div>
            <label class="field-label">Nome do item</label>
            <input v-model="form.name" type="text" class="input-field" placeholder="Ex.: Notebook Dell" />
            <p v-if="firstError('name')" class="field-error">{{ firstError('name') }}</p>
          </div>

          <div>
            <label class="field-label">Preco unitario</label>
            <input
              v-model="form.price"
              type="number"
              min="0"
              step="0.01"
              class="input-field"
              placeholder="0.00"
            />
            <p v-if="firstError('price')" class="field-error">{{ firstError('price') }}</p>
          </div>

          <div>
            <label class="field-label">Quantidade em estoque</label>
            <input
              v-model="form.quantity"
              type="number"
              min="0"
              step="1"
              class="input-field"
              placeholder="0"
            />
            <p v-if="firstError('quantity')" class="field-error">{{ firstError('quantity') }}</p>
          </div>

          <div>
            <label class="field-label">Tipo</label>
            <select v-model="form.type_id" class="select-field">
              <option value="">Selecione um tipo</option>
              <option v-for="type in types" :key="type.id" :value="String(type.id)">
                {{ type.name }}
              </option>
            </select>
            <p v-if="firstError('type_id')" class="field-error">{{ firstError('type_id') }}</p>
          </div>

          <div class="full-span">
            <label class="field-label">Fornecedor</label>
            <select v-model="form.supplier_id" class="select-field">
              <option value="">Sem fornecedor</option>
              <option v-for="supplier in suppliers" :key="supplier.id" :value="String(supplier.id)">
                {{ supplier.name }}
              </option>
            </select>
            <p v-if="firstError('supplier_id')" class="field-error">{{ firstError('supplier_id') }}</p>
          </div>

          <div class="full-span">
            <label class="field-label">Descricao</label>
            <textarea
              v-model="form.description"
              class="textarea-field"
              rows="4"
              placeholder="Resumo do produto, uso interno e observacoes"
            />
            <p v-if="firstError('description')" class="field-error">{{ firstError('description') }}</p>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" @click="closeFormModal">Cancelar</button>
          <button type="submit" class="btn-primary" :disabled="saving">
            <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
            <span>{{ editingId ? 'Salvar alteracoes' : 'Cadastrar item' }}</span>
          </button>
        </div>
      </form>
    </AppModal>

    <AppModal
      :open="deleteModalOpen"
      title="Remover item"
      eyebrow="Confirmacao"
      description="Esta acao remove o item e, se existir, tambem apaga a foto salva no storage."
      size="sm"
      @close="deleteModalOpen = false"
    >
      <div class="section-stack">
        <p class="item-subtitle">
          Deseja realmente remover <strong>{{ productToDelete?.name }}</strong>?
        </p>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" @click="deleteModalOpen = false">Cancelar</button>
          <button type="button" class="btn-danger" :disabled="deleting" @click="deleteProduct">
            <LoaderCircle v-if="deleting" class="h-4 w-4 animate-spin" />
            <span>Excluir item</span>
          </button>
        </div>
      </div>
    </AppModal>
  </section>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { LoaderCircle, Pencil, Plus, Trash2 } from 'lucide-vue-next'
import AppModal from '../components/AppModal.vue'
import ImageUploadField from '../components/ImageUploadField.vue'
import MetricCard from '../components/MetricCard.vue'
import PageHeader from '../components/PageHeader.vue'
import { pushNotification } from '../composables/useNotifications'
import { formatCurrency, formatDate, getInitials } from '../utils/formatters'
import { getApiMessage, getValidationErrors } from '../utils/http'

const loading = ref(true)
const saving = ref(false)
const deleting = ref(false)
const products = ref([])
const types = ref([])
const suppliers = ref([])

const formModalOpen = ref(false)
const deleteModalOpen = ref(false)
const editingId = ref(null)
const productToDelete = ref(null)
const photoPreview = ref('')
const existingPhotoUrl = ref('')
const fieldErrors = ref({})

const filters = reactive({
  search: '',
  type: '',
  supplier: '',
})

const form = reactive({
  name: '',
  description: '',
  quantity: '',
  price: '',
  type_id: '',
  supplier_id: '',
  photo: null,
  remove_photo: false,
})

const lowStockCount = computed(() =>
  products.value.filter((product) => Number(product.quantity) <= 5).length
)

const productsWithSupplier = computed(() =>
  products.value.filter((product) => product.supplier_id).length
)

const inventoryValue = computed(() => {
  const total = products.value.reduce((sum, product) => {
    return sum + Number(product.price) * Number(product.quantity)
  }, 0)

  return formatCurrency(total)
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
    const matchesSupplier = !filters.supplier || String(product.supplier_id) === filters.supplier

    return matchesSearch && matchesType && matchesSupplier
  })
})

function resetForm() {
  form.name = ''
  form.description = ''
  form.quantity = ''
  form.price = ''
  form.type_id = ''
  form.supplier_id = ''
  form.photo = null
  form.remove_photo = false
  fieldErrors.value = {}
  existingPhotoUrl.value = ''
  setPhotoPreview('')
}

function openCreateModal() {
  editingId.value = null
  resetForm()
  formModalOpen.value = true
}

function openEditModal(product) {
  editingId.value = product.id
  fieldErrors.value = {}
  form.name = product.name
  form.description = product.description || ''
  form.quantity = String(product.quantity)
  form.price = String(product.price)
  form.type_id = String(product.type_id)
  form.supplier_id = product.supplier_id ? String(product.supplier_id) : ''
  form.photo = null
  form.remove_photo = false
  existingPhotoUrl.value = product.photo_url || ''
  setPhotoPreview(product.photo_url || '')
  formModalOpen.value = true
}

function closeFormModal() {
  formModalOpen.value = false
  editingId.value = null
  resetForm()
}

function confirmDelete(product) {
  productToDelete.value = product
  deleteModalOpen.value = true
}

function handlePhotoSelected(file) {
  form.photo = file
  form.remove_photo = false
  setPhotoPreview(URL.createObjectURL(file))
}

function clearPhoto() {
  form.photo = null
  form.remove_photo = Boolean(existingPhotoUrl.value)
  setPhotoPreview('')
}

function setPhotoPreview(url) {
  if (photoPreview.value.startsWith('blob:')) {
    URL.revokeObjectURL(photoPreview.value)
  }

  photoPreview.value = url
}

function firstError(field) {
  const value = fieldErrors.value[field]

  if (Array.isArray(value)) {
    return value[0]
  }

  return value
}

async function fetchData() {
  loading.value = true

  try {
    const [productsResponse, typesResponse, suppliersResponse] = await Promise.all([
      axios.get('/api/products'),
      axios.get('/api/types'),
      axios.get('/api/suppliers'),
    ])

    products.value = productsResponse.data
    types.value = typesResponse.data
    suppliers.value = suppliersResponse.data
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel carregar os itens.'), 'error')
  } finally {
    loading.value = false
  }
}

function buildPayload() {
  const payload = new FormData()

  payload.append('name', form.name)
  payload.append('description', form.description || '')
  payload.append('quantity', form.quantity || '0')
  payload.append('price', form.price || '0')
  payload.append('type_id', form.type_id)
  payload.append('supplier_id', form.supplier_id || '')

  if (form.photo) {
    payload.append('photo', form.photo)
  }

  if (form.remove_photo) {
    payload.append('remove_photo', '1')
  }

  return payload
}

async function saveProduct() {
  saving.value = true
  fieldErrors.value = {}

  try {
    const payload = buildPayload()

    if (editingId.value) {
      payload.append('_method', 'PUT')
      await axios.post(`/api/products/${editingId.value}`, payload)
      pushNotification('Item atualizado com sucesso.')
    } else {
      await axios.post('/api/products', payload)
      pushNotification('Item cadastrado com sucesso.')
    }

    closeFormModal()
    await fetchData()
  } catch (error) {
    fieldErrors.value = getValidationErrors(error)

    if (!Object.keys(fieldErrors.value).length) {
      pushNotification(getApiMessage(error, 'Nao foi possivel salvar o item.'), 'error')
    }
  } finally {
    saving.value = false
  }
}

async function deleteProduct() {
  if (!productToDelete.value) {
    return
  }

  deleting.value = true

  try {
    await axios.delete(`/api/products/${productToDelete.value.id}`)
    pushNotification('Item removido com sucesso.')
    deleteModalOpen.value = false
    productToDelete.value = null
    await fetchData()
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel remover o item.'), 'error')
  } finally {
    deleting.value = false
  }
}

onMounted(fetchData)

onBeforeUnmount(() => {
  setPhotoPreview('')
})
</script>
