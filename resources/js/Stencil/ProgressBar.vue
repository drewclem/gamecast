<template>
  <ProgressRoot
    :modelValue="value"
    :max="max"
    style="transform: translateZ(0)"
    class="relative overflow-hidden rounded-full w-full sm:w-[300px] h-4 sm:h-5 border-2"
    :class="{
      'border-primary-300 bg-primary-100': percentage <= 50,
      'border-yellow-300 bg-yellow-100': percentage > 50 && percentage < 90,
      'border-red-300 bg-red-100': percentage >= 90,
    }"
  >
    <ProgressIndicator
      :class="{
        'bg-primary-default': percentage <= 50,
        'bg-yellow-500': percentage > 50 && percentage < 90,
        'bg-red-500': percentage >= 90,
      }"
      class="rounded-full w-full h-full transition-transform duration-500 ease-[cubic-bezier(0.65, 0, 0.35, 1)]"
      :style="`transform: translateX(-${100 - (value / max) * 100}%)`"
    />
  </ProgressRoot>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { ProgressRoot, ProgressIndicator } from 'radix-vue'

const props = defineProps({
  value: {
    type: Number,
    default: 0,
  },
  max: {
    type: Number,
    required: true,
  },
})

const percentage = computed(() => {
  return (props.value / props.max) * 100
})
</script>
