<template>
  <Stack space="small" :class="{ 'cursor-not-allowed': isDisabled }">
    <Stack space="xsmall">
      <Typography as="label" variant="label" :for="id" :class="{ 'sr-only': hideLabel }">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </Typography>
      <Typography v-if="helper" variant="body-small" class="opacity-60">{{ helper }}</Typography>
    </Stack>
    <input
      v-model="internalValue"
      v-if="showValue"
      class="slider-input"
      :name="id"
      :id="id"
      type="number"
    />
    <SliderRoot
      class="relative flex items-center select-none touch-none"
      :max="max"
      :min="min"
      :modelValue="[internalValue]"
      :step="step"
      @update:modelValue="handleUpdate"
    >
      <SliderTrack class="bg-gray-100 relative grow rounded-full h-1 shadow-inner">
        <SliderRange
          class="absolute rounded-full h-full"
          :class="showFill ? 'bg-primary-default' : null"
        />
      </SliderTrack>
      <SliderThumb
        class="block w-5 h-5 bg-white shadow-md border border-gray-300 rounded-full hover:bg-violet3 focus:outline-none focus:shadow-lg"
        aria-label="Volume"
      />
    </SliderRoot>
  </Stack>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { SliderRange, SliderRoot, SliderThumb, SliderTrack } from 'radix-vue'

import Typography from '@/Stencil/Typography.vue'
import Stack from '@/Stencil/Stack.vue'

const props = defineProps({
  label: {
    type: String,
    default: null,
  },
  helper: {
    type: String,
    default: null,
  },
  modelValue: {
    type: Number,
    default: 0,
  },
  min: {
    type: Number,
    default: 0,
  },
  max: {
    type: Number,
    default: 100,
  },
  step: {
    type: Number,
    default: 1,
  },
  showFill: {
    type: Boolean,
    default: false,
  },
  showValue: {
    type: Boolean,
    default: false,
  },
  isDisabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

// Create an internal value that tracks the prop
const internalValue = ref(props.modelValue)

// Watch for external changes
watch(
  () => props.modelValue,
  (newValue) => {
    internalValue.value = newValue
  }
)

// Handle updates from the slider
const handleUpdate = (values) => {
  if (Array.isArray(values) && values.length > 0) {
    const newValue = values[0]
    internalValue.value = newValue
    emit('update:modelValue', newValue)
  }
}
</script>

<style scoped>
.slider-input {
  @apply border-none p-0 text-lg focus:ring-0;
  appearance: none;
}
</style>
