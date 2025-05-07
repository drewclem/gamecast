<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Typography as="label" variant="label" :for="id">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
        <span v-else class="text-xs text-gray-500"> (optional)</span>
      </Typography>

      <ImageUpload
        :modelValue="modelValue"
        :withSaveAction="withSaveAction"
        @imageSelected="handleFileSelected"
        @error="handleError"
      >
        <template #action>
          <slot name="action" />
        </template>
      </ImageUpload>
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import InputError from '@/Stencil/Forms/InputError.vue'
import ImageUpload from '@/Stencil/ImageUpload.vue'

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
  withSaveAction: {
    type: Boolean,
    default: false,
  },
})

const handleFileSelected = (file) => {
  emit('update:modelValue', file)
}

const handleError = (error) => {
  emit('error', error)
}
</script>
