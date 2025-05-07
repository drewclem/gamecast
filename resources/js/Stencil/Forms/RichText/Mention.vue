<template>
  <div>
    <Typography>
      {{ mention.name }}
    </Typography>
  </div>
</template>

<script setup>
import Typography from '@/Stencil/Typography.vue'

defineProps({
  mention: {
    type: Object,
    required: true,
  },
})
</script>
