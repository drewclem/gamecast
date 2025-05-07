<script setup>
import { computed } from 'vue'

import Typography from '@/Stencil/Typography.vue'
import Stack from '@/Stencil/Stack.vue'
</script>

<template>
  <div class="md:grid md:grid-cols-3 md:gap-6 lg:gap-12">
    <Stack space="small">
      <Typography variant="h3">
        <slot name="title" />
      </Typography>
      <Typography class="opacity-75">
        <slot name="description" />
      </Typography>
    </Stack>

    <div class="mt-5 md:mt-0 md:col-span-2">
      <slot />
    </div>
  </div>
</template>
