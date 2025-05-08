<script setup>
import { computed, watch } from 'vue'
import Typography from '@/Stencil/Typography.vue'

const props = defineProps({
  seconds: {
    type: Number,
    default: 0,
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value),
  },
  isRunning: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['timeUp'])

// Watch for timer hitting 0
watch(
  () => props.seconds,
  (newValue) => {
    if (newValue === 0 && props.isRunning) {
      emit('timeUp')
    }
  }
)

const formattedTime = computed(() => {
  const mins = Math.floor(props.seconds / 60)
  const secs = props.seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
})

const sizeClasses = computed(() => ({
  small: 'text-2xl',
  medium: 'text-4xl',
  large: 'text-6xl',
}))

const colorClasses = computed(() => {
  if (!props.isRunning) return 'text-gray-400'
  if (props.seconds <= 10) return 'text-red-600'
  if (props.seconds <= 30) return 'text-yellow-600'
  return 'text-green-600'
})

const pulseClass = computed(() => {
  if (props.isRunning && props.seconds <= 10) return 'animate-pulse'
  return ''
})
</script>

<template>
  <div class="flex items-center justify-center">
    <Typography
      :variant="size === 'large' ? 'h1' : 'h2'"
      :class="[
        sizeClasses[size],
        colorClasses,
        'font-mono font-bold transition-all duration-300',
        pulseClass,
      ]"
    >
      {{ formattedTime }}
    </Typography>
  </div>
</template>
