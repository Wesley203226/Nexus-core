<template>
  <section class="page-stack">
    <PageHeader
      eyebrow="Cadastro de fornecedores"
      title="Fornecedores com foto de perfil e contatos organizados"
      description="Mantenha os parceiros cadastrados com status, contato principal, documento, observacoes e foto de perfil."
    >
      <template #actions>
        <button type="button" class="btn-primary" @click="openCreateModal">
          <Plus class="h-4 w-4" />
          <span>Novo fornecedor</span>
        </button>
      </template>
    </PageHeader>

    <div class="stats-grid">
      <MetricCard
        title="Fornecedores"
        :value="String(suppliers.length)"
        description="Total de parceiros cadastrados."
        badge="Base"
        tone="accent"
      />
      <MetricCard
        title="Ativos"
        :value="String(activeSuppliers)"
        description="Fornecedores prontos para operacao."
        badge="Status"
        tone="success"
      />
      <MetricCard
        title="Com foto"
        :value="String(suppliersWithPhoto)"
        description="Cadastros com imagem de perfil."
        badge="Perfil"
        tone="neutral"
      />
      <MetricCard
        title="Produtos vinculados"
        :value="String(totalLinkedProducts)"
        description="Itens atualmente ligados a fornecedores."
        badge="Relacionamento"
        tone="neutral"
      />
    </div>

    <section class="panel section-stack">
      <div class="panel-header">
        <div>
          <p class="eyebrow">Busca e status</p>
          <h2 class="section-title">Encontre o fornecedor certo</h2>
        </div>
        <span class="chip chip-neutral">{{ filteredSuppliers.length }} resultado(s)</span>
      </div>

      <div class="control-grid">
        <div>
          <label class="field-label">Buscar</label>
          <input
            v-model="filters.search"
            type="text"
            class="input-field"
            placeholder="Nome, responsavel, email ou documento"
          />
        </div>

        <div>
          <label class="field-label">Status</label>
          <select v-model="filters.status" class="select-field">
            <option value="">Todos</option>
            <option value="active">Ativos</option>
            <option value="inactive">Inativos</option>
          </select>
        </div>
      </div>
    </section>

    <section class="panel section-stack">
      <div class="panel-header">
        <div>
          <p class="eyebrow">Listagem principal</p>
          <h2 class="section-title">Fornecedores cadastrados</h2>
        </div>
      </div>

      <div v-if="loading" class="loading-state">Carregando fornecedores...</div>

      <div v-else-if="filteredSuppliers.length" class="data-list">
        <article v-for="supplier in filteredSuppliers" :key="supplier.id" class="data-row supplier-row">
          <div class="item-visual">
            <div class="avatar">
              <img v-if="supplier.profile_photo_url" :src="supplier.profile_photo_url" alt="" />
              <span v-else>{{ getInitials(supplier.name) }}</span>
            </div>

            <div class="min-w-0">
              <p class="item-title">{{ supplier.name }}</p>
              <p class="item-subtitle">
                {{ supplier.contact_name || 'Sem responsavel principal' }}
              </p>
              <p class="item-meta">
                {{ supplier.email || 'Sem email' }} · {{ supplier.phone || 'Sem telefone' }}
              </p>
            </div>
          </div>

          <div>
            <p class="mini-label">Documento</p>
            <p class="item-subtitle strong-text">{{ supplier.document || 'Nao informado' }}</p>
          </div>

          <div>
            <p class="mini-label">Itens vinculados</p>
            <span class="chip chip-neutral">{{ supplier.products_count }} item(ns)</span>
          </div>

          <div>
            <p class="mini-label">Status</p>
            <span :class="['chip', supplier.is_active ? 'chip-success' : 'chip-danger']">
              {{ supplier.is_active ? 'Ativo' : 'Inativo' }}
            </span>
          </div>

          <div class="row-actions">
            <button type="button" class="icon-btn" @click="openEditModal(supplier)">
              <Pencil class="h-4 w-4" />
            </button>
            <button type="button" class="icon-btn danger" @click="confirmDelete(supplier)">
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </article>
      </div>

      <div v-else class="empty-state">
        <p class="empty-title">Nenhum fornecedor encontrado.</p>
        <p class="empty-description">Crie um novo cadastro ou ajuste os filtros da listagem.</p>
      </div>
    </section>

    <AppModal
      :open="formModalOpen"
      :title="editingId ? 'Editar fornecedor' : 'Novo fornecedor'"
      eyebrow="Formulario"
      description="Cadastre os dados do parceiro, defina o status e envie uma foto de perfil."
      size="xl"
      @close="closeFormModal"
    >
      <form class="section-stack" @submit.prevent="saveSupplier">
        <div class="form-grid">
          <div class="full-span">
            <ImageUploadField
              label="Foto de perfil"
              hint="PNG ou JPG com ate 3 MB."
              :preview-url="photoPreview"
              button-text="Selecionar foto"
              empty-text="Nenhuma foto enviada"
              @select="handlePhotoSelected"
              @clear="clearPhoto"
            />
          </div>

          <div>
            <label class="field-label">Nome do fornecedor</label>
            <input v-model="form.name" type="text" class="input-field" placeholder="Ex.: Tech Supply" />
            <p v-if="firstError('name')" class="field-error">{{ firstError('name') }}</p>
          </div>

          <div>
            <label class="field-label">Responsavel</label>
            <input
              v-model="form.contact_name"
              type="text"
              class="input-field"
              placeholder="Nome do contato principal"
            />
            <p v-if="firstError('contact_name')" class="field-error">{{ firstError('contact_name') }}</p>
          </div>

          <div>
            <label class="field-label">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="input-field"
              placeholder="contato@empresa.com"
            />
            <p v-if="firstError('email')" class="field-error">{{ firstError('email') }}</p>
          </div>

          <div>
            <label class="field-label">Telefone</label>
            <input v-model="form.phone" type="text" class="input-field" placeholder="(11) 99999-9999" />
            <p v-if="firstError('phone')" class="field-error">{{ firstError('phone') }}</p>
          </div>

          <div>
            <label class="field-label">Documento</label>
            <input v-model="form.document" type="text" class="input-field" placeholder="CNPJ ou CPF" />
            <p v-if="firstError('document')" class="field-error">{{ firstError('document') }}</p>
          </div>

          <div>
            <label class="field-label">Status</label>
            <select v-model="form.is_active" class="select-field">
              <option value="1">Ativo</option>
              <option value="0">Inativo</option>
            </select>
            <p v-if="firstError('is_active')" class="field-error">{{ firstError('is_active') }}</p>
          </div>

          <div class="full-span">
            <label class="field-label">Observacoes</label>
            <textarea
              v-model="form.notes"
              rows="4"
              class="textarea-field"
              placeholder="Anotacoes internas sobre prazo, condicoes ou produtos fornecidos"
            />
            <p v-if="firstError('notes')" class="field-error">{{ firstError('notes') }}</p>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" @click="closeFormModal">Cancelar</button>
          <button type="submit" class="btn-primary" :disabled="saving">
            <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
            <span>{{ editingId ? 'Salvar alteracoes' : 'Cadastrar fornecedor' }}</span>
          </button>
        </div>
      </form>
    </AppModal>

    <AppModal
      :open="deleteModalOpen"
      title="Remover fornecedor"
      eyebrow="Confirmacao"
      description="O fornecedor sera apagado e os produtos vinculados ficarao sem relacao de fornecedor."
      size="sm"
      @close="deleteModalOpen = false"
    >
      <div class="section-stack">
        <p class="item-subtitle">
          Deseja remover <strong>{{ supplierToDelete?.name }}</strong>?
        </p>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" @click="deleteModalOpen = false">Cancelar</button>
          <button type="button" class="btn-danger" :disabled="deleting" @click="deleteSupplier">
            <LoaderCircle v-if="deleting" class="h-4 w-4 animate-spin" />
            <span>Excluir fornecedor</span>
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
import { getInitials } from '../utils/formatters'
import { getApiMessage, getValidationErrors } from '../utils/http'

const loading = ref(true)
const saving = ref(false)
const deleting = ref(false)
const suppliers = ref([])

const formModalOpen = ref(false)
const deleteModalOpen = ref(false)
const editingId = ref(null)
const supplierToDelete = ref(null)
const fieldErrors = ref({})
const photoPreview = ref('')
const existingPhotoUrl = ref('')

const filters = reactive({
  search: '',
  status: '',
})

const form = reactive({
  name: '',
  contact_name: '',
  email: '',
  phone: '',
  document: '',
  notes: '',
  is_active: '1',
  profile_photo: null,
  remove_photo: false,
})

const activeSuppliers = computed(() => suppliers.value.filter((supplier) => supplier.is_active).length)
const suppliersWithPhoto = computed(() => suppliers.value.filter((supplier) => supplier.profile_photo_url).length)
const totalLinkedProducts = computed(() =>
  suppliers.value.reduce((sum, supplier) => sum + Number(supplier.products_count || 0), 0)
)

const filteredSuppliers = computed(() => {
  return suppliers.value.filter((supplier) => {
    const searchTerm = filters.search.trim().toLowerCase()
    const matchesSearch =
      !searchTerm ||
      supplier.name.toLowerCase().includes(searchTerm) ||
      (supplier.contact_name || '').toLowerCase().includes(searchTerm) ||
      (supplier.email || '').toLowerCase().includes(searchTerm) ||
      (supplier.document || '').toLowerCase().includes(searchTerm)

    const matchesStatus =
      !filters.status ||
      (filters.status === 'active' && supplier.is_active) ||
      (filters.status === 'inactive' && !supplier.is_active)

    return matchesSearch && matchesStatus
  })
})

function resetForm() {
  form.name = ''
  form.contact_name = ''
  form.email = ''
  form.phone = ''
  form.document = ''
  form.notes = ''
  form.is_active = '1'
  form.profile_photo = null
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

function openEditModal(supplier) {
  editingId.value = supplier.id
  fieldErrors.value = {}
  form.name = supplier.name
  form.contact_name = supplier.contact_name || ''
  form.email = supplier.email || ''
  form.phone = supplier.phone || ''
  form.document = supplier.document || ''
  form.notes = supplier.notes || ''
  form.is_active = supplier.is_active ? '1' : '0'
  form.profile_photo = null
  form.remove_photo = false
  existingPhotoUrl.value = supplier.profile_photo_url || ''
  setPhotoPreview(supplier.profile_photo_url || '')
  formModalOpen.value = true
}

function closeFormModal() {
  formModalOpen.value = false
  editingId.value = null
  resetForm()
}

function confirmDelete(supplier) {
  supplierToDelete.value = supplier
  deleteModalOpen.value = true
}

function handlePhotoSelected(file) {
  form.profile_photo = file
  form.remove_photo = false
  setPhotoPreview(URL.createObjectURL(file))
}

function clearPhoto() {
  form.profile_photo = null
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

async function fetchSuppliers() {
  loading.value = true

  try {
    const response = await axios.get('/api/suppliers')
    suppliers.value = response.data
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel carregar os fornecedores.'), 'error')
  } finally {
    loading.value = false
  }
}

function buildPayload() {
  const payload = new FormData()

  payload.append('name', form.name)
  payload.append('contact_name', form.contact_name || '')
  payload.append('email', form.email || '')
  payload.append('phone', form.phone || '')
  payload.append('document', form.document || '')
  payload.append('notes', form.notes || '')
  payload.append('is_active', form.is_active)

  if (form.profile_photo) {
    payload.append('profile_photo', form.profile_photo)
  }

  if (form.remove_photo) {
    payload.append('remove_photo', '1')
  }

  return payload
}

async function saveSupplier() {
  saving.value = true
  fieldErrors.value = {}

  try {
    const payload = buildPayload()

    if (editingId.value) {
      payload.append('_method', 'PUT')
      await axios.post(`/api/suppliers/${editingId.value}`, payload)
      pushNotification('Fornecedor atualizado com sucesso.')
    } else {
      await axios.post('/api/suppliers', payload)
      pushNotification('Fornecedor cadastrado com sucesso.')
    }

    closeFormModal()
    await fetchSuppliers()
  } catch (error) {
    fieldErrors.value = getValidationErrors(error)

    if (!Object.keys(fieldErrors.value).length) {
      pushNotification(getApiMessage(error, 'Nao foi possivel salvar o fornecedor.'), 'error')
    }
  } finally {
    saving.value = false
  }
}

async function deleteSupplier() {
  if (!supplierToDelete.value) {
    return
  }

  deleting.value = true

  try {
    await axios.delete(`/api/suppliers/${supplierToDelete.value.id}`)
    pushNotification('Fornecedor removido com sucesso.')
    deleteModalOpen.value = false
    supplierToDelete.value = null
    await fetchSuppliers()
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel remover o fornecedor.'), 'error')
  } finally {
    deleting.value = false
  }
}

onMounted(fetchSuppliers)

onBeforeUnmount(() => {
  setPhotoPreview('')
})
</script>
