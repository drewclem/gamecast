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

      <SwitchRoot
        :checked="modelValue"
        @update:checked="emit('update:modelValue', $event)"
        class="w-[42px] h-[25px] focus-within:outline-2 focus-within:outline-primary-300 flex bg-gray-200 shadow-sm rounded-full relative data-[state=checked]:bg-primary-default cursor-default"
      >
        <SwitchThumb
          class="block w-[21px] h-[21px] my-auto bg-white shadow-sm rounded-full transition-transform duration-100 translate-x-0.5 will-change-transform data-[state=checked]:translate-x-[19px]"
        />
      </SwitchRoot>
    </Stack>
  </Stack>
</template>

<script setup>
import { SwitchRoot, SwitchThumb } from 'radix-vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'

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
    type: Boolean,
    required: true,
  },

  hideLabel: {
    type: Boolean,
    default: false,
  },
  helper: {
    type: String,
    default: null,
  },
})

const emit = defineEmits(['update:modelValue'])
</script>
