<template>
  <Card padding="none">
    <header class="p-4 border-b border-gray-200">
      <Stack space="xxsmall">
        <Typography variant="h2">{{ title }} </Typography>
        <Typography v-if="description" class="opacity-60">
          {{ description }}
        </Typography>
      </Stack>
    </header>

    <form class="px-4 py-6">
      <slot />
    </form>

    <footer class="flex border-t border-gray-200 p-4">
      <Button class="ml-auto" @click="emit('confirm')" :loading="loading" :disabled="disabled">{{
        confirmText
      }}</Button>
    </footer>
  </Card>
</template>

<script setup>
import Card from '@/Stencil/Card.vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import Button from '@/Stencil/Button.vue'

const emit = defineEmits(['confirm'])

defineProps({
  title: {
    type: String,
    default: null,
  },
  description: {
    type: String,
    default: null,
  },
  confirmText: {
    type: String,
    default: 'Save',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})
</script>
