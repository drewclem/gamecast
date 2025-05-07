<template>
  <div v-show="error">
    <Typography as="p" class="text-xs text-red-500">
      {{ error }}
    </Typography>
  </div>
</template>

<script setup>
import Typography from '@/Stencil/Typography.vue'

defineProps({
  error: {
    type: String,
  },
})
</script>
