<template>
  <div :class="colors" class="flex gap-4 items-center border p-3 rounded-md">
    <div>
      <Icon v-if="hasIcon" :icon="icon" size="small" :class="iconColor" />
    </div>
    <Stack space="xxsmall">
      <Typography v-if="title" variant="h4">{{ title }}</Typography>

      <Typography v-if="message">{{ message }}</Typography>
    </Stack>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Icon from '@/Stencil/Icon.vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'

const props = defineProps({
  hasIcon: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: null,
  },
  message: {
    type: String,
    default: null,
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => {
      return ['info', 'success', 'warning', 'error', 'other'].includes(value)
    },
  },
})

const colors = computed(() => {
  switch (props.type) {
    case 'info':
      return 'bg-blue-100 text-blue-600 border-blue-500'
    case 'success':
      return 'bg-primary-100 text-primary-600 border-primary-500'
    case 'warning':
      return 'bg-amber-100 text-amber-600 border-amber-500'
    case 'error':
      return 'bg-red-100 text-red-600 border-red-500'
    default:
      return 'bg-gray-100 text-gray-600 border-gray-500'
  }
})

const iconColor = computed(() => {
  switch (props.type) {
    case 'info':
      return 'text-blue-500'
    case 'success':
      return 'text-primary-500'
    case 'warning':
      return 'text-amber-500'
    case 'error':
      return 'text-red-500'
    default:
      return 'text-gray-500'
  }
})

const icon = computed(() => {
  switch (props.type) {
    case 'info':
      return 'info'
    case 'success':
      return 'success'
    case 'warning':
      return 'warning'
    case 'error':
      return 'error'
    default:
      return 'info'
  }
})
</script>
