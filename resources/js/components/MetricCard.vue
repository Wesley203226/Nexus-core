<template>
  <article class="metric-card">
    <div class="metric-header">
      <p class="metric-title">{{ title }}</p>
      <span :class="['metric-badge', toneClass]">{{ badge }}</span>
    </div>

    <p class="metric-value">{{ value }}</p>
    <p class="metric-description">{{ description }}</p>
  </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  value: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
  badge: {
    type: String,
    default: 'Resumo',
  },
  tone: {
    type: String,
    default: 'neutral',
  },
})

const toneClass = computed(() => `metric-badge-${props.tone}`)
</script>
