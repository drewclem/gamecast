<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Stack space="xsmall">
        <Typography as="label" variant="label" :for="id" :class="{ 'sr-only': hideLabel }">
          {{ label }}
          <span v-if="required" class="text-red-500">*</span>
        </Typography>
        <Typography v-if="helper" variant="body-small" class="opacity-60">{{ helper }}</Typography>
      </Stack>
      <textarea
        ref="textarea"
        :id="id"
        :name="id"
        :rows="rows"
        :value="modelValue"
        class="input"
        @input="emit('update:modelValue', $event.target.value)"
      />
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import InputError from '@/Stencil/Forms/InputError.vue'

const emit = defineEmits(['update:modelValue'])

defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  modelValue: {
    type: String,
    required: true,
  },
  rows: {
    type: Number,
    default: 4,
  },
  error: {
    type: String,
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
  helper: {
    type: String,
    default: null,
  },
})

const textarea = ref(null)

onMounted(() => {
  if (textarea.value.hasAttribute('autofocus')) {
    textarea.value.focus()
  }
})

defineExpose({ focus: () => textarea.value.focus() })
</script>
