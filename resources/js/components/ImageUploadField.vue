<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between gap-3">
      <label class="field-label !mb-0">{{ label }}</label>
      <span v-if="hint" class="field-hint">{{ hint }}</span>
    </div>

    <div class="image-upload">
      <div class="image-preview">
        <img v-if="previewUrl" :src="previewUrl" alt="" />
        <div v-else class="image-preview-empty">
          <ImagePlus class="h-6 w-6" />
          <span>{{ emptyText }}</span>
        </div>
      </div>

      <div class="flex flex-wrap gap-3">
        <button type="button" class="btn-secondary" @click="openPicker">
          <Upload class="h-4 w-4" />
          <span>{{ buttonText }}</span>
        </button>

        <button
          v-if="previewUrl"
          type="button"
          class="btn-ghost-danger"
          @click="clearFile"
        >
          <Trash2 class="h-4 w-4" />
          <span>Remover</span>
        </button>
      </div>

      <input
        ref="fileInput"
        type="file"
        class="hidden"
        accept="image/*"
        @change="handleChange"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ImagePlus, Trash2, Upload } from 'lucide-vue-next'

defineProps({
  label: {
    type: String,
    required: true,
  },
  hint: {
    type: String,
    default: '',
  },
  previewUrl: {
    type: String,
    default: '',
  },
  buttonText: {
    type: String,
    default: 'Selecionar imagem',
  },
  emptyText: {
    type: String,
    default: 'Sem imagem selecionada',
  },
})

const emit = defineEmits(['select', 'clear'])
const fileInput = ref(null)

function openPicker() {
  fileInput.value?.click()
}

function handleChange(event) {
  const file = event.target.files?.[0]

  if (file) {
    emit('select', file)
  }
}

function clearFile() {
  if (fileInput.value) {
    fileInput.value.value = ''
  }

  emit('clear')
}
</script>
