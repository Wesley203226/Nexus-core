<template>
  <transition name="modal-fade">
    <div v-if="open" class="modal-backdrop" @click.self="$emit('close')">
      <div :class="['modal-card', sizeClass]">
        <div class="modal-header">
          <div>
            <p v-if="eyebrow" class="eyebrow">{{ eyebrow }}</p>
            <h2 class="modal-title">{{ title }}</h2>
            <p v-if="description" class="modal-description">{{ description }}</p>
          </div>

          <button type="button" class="icon-btn" @click="$emit('close')">
            <X class="h-4 w-4" />
          </button>
        </div>

        <div class="modal-body">
          <slot />
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { computed } from 'vue'
import { X } from 'lucide-vue-next'

const props = defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
  eyebrow: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: 'lg',
  },
})

defineEmits(['close'])

const sizeClass = computed(() => {
  if (props.size === 'sm') return 'modal-sm'
  if (props.size === 'xl') return 'modal-xl'

  return 'modal-lg'
})
</script>

<style>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
