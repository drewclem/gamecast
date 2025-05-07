<!-- ColorPicker.vue -->
<template>
  <div class="card-shadow rounded-full overflow-hidden h-16 w-16">
    <input
      type="color"
      :value="modelValue"
      @input="updateValue($event.target.value)"
      class="h-16 w-16"
    />
  </div>
</template>

<script setup>
import { DropdownMenuRoot, DropdownMenuTrigger, DropdownMenuContent } from 'radix-vue'
import { RiArrowDownSLine } from '@remixicon/vue'

defineProps({
  modelValue: {
    type: String,
    required: true,
  },
  triggerClass: {
    type: String,
    default: '',
  },
  contentClass: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

function updateValue(color) {
  emit('update:modelValue', color)
}
</script>

<style scoped>
/* Improve the native color picker appearance */
input[type='color'] {
  -webkit-appearance: none;
  appearance: none;
  padding: 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type='color']::-webkit-color-swatch-wrapper {
  padding: 0;
}

input[type='color']::-webkit-color-swatch {
  border: none;
  border-radius: 4px;
}
</style>
