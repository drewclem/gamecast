<template>
  <span
    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium card-shadow"
    :class="{
      'bg-red-100 text-red-800': variant === 'danger',
      'bg-primary-100 text-primary-800': variant === 'success',
      'bg-blue-100 text-blue-800': variant === 'info',
      'bg-amber-100 text-amber-800': variant === 'warning',
      'bg-purple-100 text-purple-800': variant === 'pending_payment',
    }"
  >
    <span class="h-1.5 w-1.5 mr-1 rounded-full" :class="color"></span>
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  variant: {
    type: String,
    default: 'info',
  },
})

const color = computed(() => {
  switch (props.variant) {
    case 'danger':
      return 'bg-red-500'
    case 'success':
      return 'bg-primary-500'
    case 'warning':
      return 'bg-amber-500'
    case 'pending_payment':
      return 'bg-purple-500'
    default:
      return 'bg-blue-500'
  }
})
</script>
