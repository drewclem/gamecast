<template>
  <component :is="as" class="flex flex-col" :class="spaceClasses">
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  space: {
    type: String,
    default: 'default',
    validator: (value) => {
      return ['none', 'xxsmall', 'xsmall', 'small', 'default', 'medium', 'large'].includes(value)
    },
  },
  as: {
    type: String,
    default: 'div',
  },
})

const spaceClasses = computed(() => {
  return {
    'space-y-0': props.space === 'none',
    'space-y-0.5': props.space === 'xxsmall',
    'space-y-1': props.space === 'xsmall',
    'space-y-2': props.space === 'small',
    'space-y-4': props.space === 'medium',
    'space-y-6': props.space === 'default',
    'space-y-8': props.space === 'large',
    'space-y-12': props.space === 'xlarge',
    'space-y-16': props.space === 'xxlarge',
  }
})
</script>
