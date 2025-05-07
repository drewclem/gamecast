<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Typography as="label" variant="label" :for="id">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
        <span v-else class="text-xs text-gray-500"> (optional)</span>
      </Typography>
      <Dropzone :elementName="id" @fileSelected="handleFileSelected" @error="handleError" />
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { ref, watch } from 'vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import InputError from '@/Stencil/Forms/InputError.vue'
import Dropzone from '@/Stencil/Dropzone.vue'

const emit = defineEmits(['update:modelValue', 'error'])

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  modelValue: {
    type: [File, String, null],
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
})

// Handle file selection from Dropzone
const handleFileSelected = (file) => {
  emit('update:modelValue', file)
}

// Handle errors from Dropzone
const handleError = (error) => {
  emit('error', error)
}
</script>
