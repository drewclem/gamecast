<script setup>
import { SwitchRoot, SwitchThumb } from 'radix-vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  name: {
    type: String,
    default: undefined,
  },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <SwitchRoot
    :id="name"
    :checked="modelValue"
    :disabled="disabled"
    :name="name"
    @update:checked="emit('update:modelValue', $event)"
    class="relative inline-flex h-4 w-8 items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-amber-500 data-[state=unchecked]:bg-gray-200"
  >
    <SwitchThumb
      class="block h-3 w-3 transform rounded-full bg-white transition-transform duration-100 will-change-transform data-[state=checked]:translate-x-4 data-[state=unchecked]:translate-x-0.5"
    />
  </SwitchRoot>
</template>
