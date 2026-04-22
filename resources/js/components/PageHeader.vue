<template>
  <div class="page-header">
    <div class="space-y-3">
      <p v-if="eyebrow" class="eyebrow">{{ eyebrow }}</p>
      <h1 class="page-title">{{ title }}</h1>
      <p v-if="description" class="page-description">{{ description }}</p>
    </div>

    <div v-if="$slots.actions" class="action-row">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  eyebrow: {
    type: String,
    default: '',
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
})
</script>
