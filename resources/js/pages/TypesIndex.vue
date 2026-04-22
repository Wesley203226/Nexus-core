<template>
  <section class="page-stack">
    <PageHeader
      eyebrow="Cadastro de tipos"
      title="Tipos e categorias para organizar o estoque"
      description="Crie, edite e mantenha os tipos do catalogo. A remocao fica protegida quando ainda existem produtos vinculados."
    >
      <template #actions>
        <button type="button" class="btn-primary" @click="openCreateModal">
          <Plus class="h-4 w-4" />
          <span>Novo tipo</span>
        </button>
      </template>
    </PageHeader>

    <div class="stats-grid">
      <MetricCard
        title="Tipos"
        :value="String(types.length)"
        description="Categorias disponiveis para os produtos."
        badge="Catalogo"
        tone="accent"
      />
      <MetricCard
        title="Com produtos"
        :value="String(typesInUse)"
        description="Tipos atualmente utilizados no estoque."
        badge="Uso"
        tone="success"
      />
      <MetricCard
        title="Sem uso"
        :value="String(typesWithoutProducts)"
        description="Tipos prontos para receber novos itens."
        badge="Reserva"
        tone="neutral"
      />
      <MetricCard
        title="Produtos vinculados"
        :value="String(totalProductsLinked)"
        description="Soma do relacionamento entre tipos e itens."
        badge="Relacao"
        tone="neutral"
      />
    </div>

    <section class="panel section-stack">
      <div class="panel-header">
        <div>
          <p class="eyebrow">Busca rapida</p>
          <h2 class="section-title">Filtre os tipos cadastrados</h2>
        </div>
        <span class="chip chip-neutral">{{ filteredTypes.length }} resultado(s)</span>
      </div>

      <div class="control-grid">
        <div>
          <label class="field-label">Buscar tipo</label>
          <input
            v-model="filters.search"
            type="text"
            class="input-field"
            placeholder="Nome ou descricao"
          />
        </div>
      </div>
    </section>

    <section class="panel section-stack">
      <div class="panel-header">
        <div>
          <p class="eyebrow">Listagem principal</p>
          <h2 class="section-title">Tipos cadastrados</h2>
        </div>
      </div>

      <div v-if="loading" class="loading-state">Carregando tipos...</div>

      <div v-else-if="filteredTypes.length" class="type-grid">
        <article v-for="type in filteredTypes" :key="type.id" class="type-card">
          <div class="panel-header !mb-4">
            <div>
              <p class="item-title">{{ type.name }}</p>
              <p class="item-subtitle">{{ type.description || 'Sem descricao cadastrada.' }}</p>
            </div>

            <span class="chip chip-neutral">{{ type.products_count }} item(ns)</span>
          </div>

          <div class="row-actions">
            <button type="button" class="btn-secondary" @click="openEditModal(type)">
              <Pencil class="h-4 w-4" />
              <span>Editar</span>
            </button>
            <button
              type="button"
              class="btn-ghost-danger"
              :disabled="type.products_count > 0"
              :title="type.products_count > 0 ? 'Remova os produtos desse tipo antes de excluir.' : 'Excluir tipo'"
              @click="confirmDelete(type)"
            >
              <Trash2 class="h-4 w-4" />
              <span>Excluir</span>
            </button>
          </div>
        </article>
      </div>

      <div v-else class="empty-state">
        <p class="empty-title">Nenhum tipo encontrado.</p>
        <p class="empty-description">Crie tipos para organizar melhor os produtos do estoque.</p>
      </div>
    </section>

    <AppModal
      :open="formModalOpen"
      :title="editingId ? 'Editar tipo' : 'Novo tipo'"
      eyebrow="Formulario"
      description="Defina o nome do tipo e uma descricao curta para facilitar a identificacao."
      size="lg"
      @close="closeFormModal"
    >
      <form class="section-stack" @submit.prevent="saveType">
        <div class="form-grid">
          <div class="full-span">
            <label class="field-label">Nome do tipo</label>
            <input v-model="form.name" type="text" class="input-field" placeholder="Ex.: Eletronicos" />
            <p v-if="firstError('name')" class="field-error">{{ firstError('name') }}</p>
          </div>

          <div class="full-span">
            <label class="field-label">Descricao</label>
            <textarea
              v-model="form.description"
              rows="4"
              class="textarea-field"
              placeholder="Uso interno, recorte de produtos e observacoes"
            />
            <p v-if="firstError('description')" class="field-error">{{ firstError('description') }}</p>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" @click="closeFormModal">Cancelar</button>
          <button type="submit" class="btn-primary" :disabled="saving">
            <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
            <span>{{ editingId ? 'Salvar alteracoes' : 'Cadastrar tipo' }}</span>
          </button>
        </div>
      </form>
    </AppModal>

    <AppModal
      :open="deleteModalOpen"
      title="Remover tipo"
      eyebrow="Confirmacao"
      description="Somente tipos sem produtos vinculados podem ser removidos."
      size="sm"
      @close="deleteModalOpen = false"
    >
      <div class="section-stack">
        <p class="item-subtitle">
          Deseja remover <strong>{{ typeToDelete?.name }}</strong>?
        </p>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" @click="deleteModalOpen = false">Cancelar</button>
          <button type="button" class="btn-danger" :disabled="deleting" @click="deleteType">
            <LoaderCircle v-if="deleting" class="h-4 w-4 animate-spin" />
            <span>Excluir tipo</span>
          </button>
        </div>
      </div>
    </AppModal>
  </section>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { LoaderCircle, Pencil, Plus, Trash2 } from 'lucide-vue-next'
import AppModal from '../components/AppModal.vue'
import MetricCard from '../components/MetricCard.vue'
import PageHeader from '../components/PageHeader.vue'
import { pushNotification } from '../composables/useNotifications'
import { getApiMessage, getValidationErrors } from '../utils/http'

const loading = ref(true)
const saving = ref(false)
const deleting = ref(false)
const types = ref([])

const formModalOpen = ref(false)
const deleteModalOpen = ref(false)
const editingId = ref(null)
const typeToDelete = ref(null)
const fieldErrors = ref({})

const filters = reactive({
  search: '',
})

const form = reactive({
  name: '',
  description: '',
})

const typesInUse = computed(() => types.value.filter((type) => Number(type.products_count) > 0).length)
const typesWithoutProducts = computed(() => types.value.filter((type) => Number(type.products_count) === 0).length)
const totalProductsLinked = computed(() =>
  types.value.reduce((sum, type) => sum + Number(type.products_count || 0), 0)
)

const filteredTypes = computed(() => {
  return types.value.filter((type) => {
    const searchTerm = filters.search.trim().toLowerCase()

    return (
      !searchTerm ||
      type.name.toLowerCase().includes(searchTerm) ||
      (type.description || '').toLowerCase().includes(searchTerm)
    )
  })
})

function resetForm() {
  form.name = ''
  form.description = ''
  fieldErrors.value = {}
}

function openCreateModal() {
  editingId.value = null
  resetForm()
  formModalOpen.value = true
}

function openEditModal(type) {
  editingId.value = type.id
  form.name = type.name
  form.description = type.description || ''
  fieldErrors.value = {}
  formModalOpen.value = true
}

function closeFormModal() {
  formModalOpen.value = false
  editingId.value = null
  resetForm()
}

function confirmDelete(type) {
  typeToDelete.value = type
  deleteModalOpen.value = true
}

function firstError(field) {
  const value = fieldErrors.value[field]

  if (Array.isArray(value)) {
    return value[0]
  }

  return value
}

async function fetchTypes() {
  loading.value = true

  try {
    const response = await axios.get('/api/types')
    types.value = response.data
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel carregar os tipos.'), 'error')
  } finally {
    loading.value = false
  }
}

async function saveType() {
  saving.value = true
  fieldErrors.value = {}

  try {
    if (editingId.value) {
      await axios.put(`/api/types/${editingId.value}`, {
        name: form.name,
        description: form.description,
      })
      pushNotification('Tipo atualizado com sucesso.')
    } else {
      await axios.post('/api/types', {
        name: form.name,
        description: form.description,
      })
      pushNotification('Tipo cadastrado com sucesso.')
    }

    closeFormModal()
    await fetchTypes()
  } catch (error) {
    fieldErrors.value = getValidationErrors(error)

    if (!Object.keys(fieldErrors.value).length) {
      pushNotification(getApiMessage(error, 'Nao foi possivel salvar o tipo.'), 'error')
    }
  } finally {
    saving.value = false
  }
}

async function deleteType() {
  if (!typeToDelete.value) {
    return
  }

  deleting.value = true

  try {
    await axios.delete(`/api/types/${typeToDelete.value.id}`)
    pushNotification('Tipo removido com sucesso.')
    deleteModalOpen.value = false
    typeToDelete.value = null
    await fetchTypes()
  } catch (error) {
    pushNotification(getApiMessage(error, 'Nao foi possivel remover o tipo.'), 'error')
  } finally {
    deleting.value = false
  }
}

onMounted(fetchTypes)
</script>
