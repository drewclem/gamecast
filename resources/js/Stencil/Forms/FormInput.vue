<template>
  <Stack space="xsmall" class="relative" :class="{ 'opacity-50': isDisabled }">
    <Stack space="small">
      <Stack space="xxsmall">
        <Typography as="label" variant="label" :for="id" :class="{ 'sr-only': hideLabel }">
          {{ label }}
          <span v-if="required" class="text-red-500">*</span>
        </Typography>
        <Typography v-if="helper" variant="body-small" class="opacity-60">{{ helper }}</Typography>
      </Stack>
      <input
        ref="input"
        :id="id"
        :name="id"
        :type="inputType"
        class="input"
        :class="{ 'max-w-[450px]': prose }"
        :value="modelValue"
        :disabled="isDisabled"
        :required="required"
        :placeholder="placeholder"
        @input="emit('update:modelValue', $event.target.value)"
      />

      <template v-if="inputType === 'search' && !modelValue">
        <Icon
          icon="search"
          size="xsmall"
          class="absolute right-2 bottom-0 -translate-y-2.5 transform text-gray-400"
        />
      </template>
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import InputError from '@/Stencil/Forms/InputError.vue'
import Icon from '@/Stencil/Icon.vue'
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
  required: {
    type: Boolean,
    default: false,
  },
  inputType: {
    type: String,
    default: 'text',
    validator(value) {
      return ['text', 'email', 'password', 'search'].includes(value)
    },
  },
  error: {
    type: String,
    default: null,
  },
  isDisabled: {
    type: Boolean,
    default: false,
  },
  prose: {
    type: Boolean,
    default: false,
  },
  hideLabel: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: null,
  },
  helper: {
    type: String,
    default: null,
  },
})

const input = ref(null)

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus()
  }
})

defineExpose({ focus: () => input.value.focus() })
</script>
