<template>
  <AvatarRoot
    class="inline-flex select-none items-center justify-center overflow-hidden rounded-full align-middle border border-gray-200"
    :class="sizeClasses"
  >
    <AvatarImage class="h-full w-full rounded-[inherit] object-cover" :src="src" :alt="alt" />
    <AvatarFallback
      class="text-grass11 leading-1 flex h-full w-full items-center justify-center bg-gray-50 text-[15px] font-medium"
      :delay-ms="600"
    >
      {{ fallbackInitials }}
    </AvatarFallback>
  </AvatarRoot>
</template>

<script setup>
import { computed } from 'vue'
import { AvatarFallback, AvatarImage, AvatarRoot } from 'radix-vue'

const props = defineProps({
  size: {
    type: String,
    default: 'default',
  },
  src: {
    type: String,
    default: '',
  },
  alt: {
    type: String,
    default: '',
  },
})

const fallbackInitials = computed(() => {
  if (props.alt) {
    const altSplit = props.alt.split(' ')
    if (altSplit.length > 1) {
      return altSplit[0].charAt(0) + altSplit[1].charAt(0)
    } else {
      return props.alt.charAt(0) + props.alt.charAt(1)
    }
  }
})

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'xxsmall':
      return 'h-4 w-4'
    case 'xsmall':
      return 'h-6 w-6'
    case 'small':
      return 'h-8 w-8'
    case 'medium':
      return 'h-10 w-10'
    case 'large':
      return 'h-12 w-12'
    case 'xlarge':
      return 'h-16 w-16'
    case 'xxlarge':
      return 'h-20 w-20'
    default:
      return 'h-[45px] w-[45px]'
  }
})
</script>
