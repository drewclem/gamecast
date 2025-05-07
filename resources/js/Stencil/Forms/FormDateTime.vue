<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Typography as="label" variant="label" :for="id">
        {{ label }}
        <span v-if="required" class="text-red-600">*</span>
      </Typography>

      <div class="grid gap-2" :class="gridCols">
        <!-- Date input -->
        <div>
          <input
            :id="id"
            type="date"
            class="input w-full"
            :name="`${id}_date`"
            v-model="dateValue"
            :required="required"
            :disabled="disabled"
          />
        </div>

        <!-- Time input (conditional) -->
        <div v-if="showTime">
          <input
            :id="`${id}_time`"
            type="time"
            class="input w-full"
            :name="`${id}_time`"
            v-model="timeValue"
            :required="required"
            :disabled="disabled"
          />
        </div>
      </div>
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import InputError from '@/Stencil/Forms/InputError.vue'

const emit = defineEmits(['update:modelValue'])

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
    type: [Date, String, null],
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
  showTime: {
    type: Boolean,
    default: false,
  },
})

// Internal state
const dateValue = ref('')
const timeValue = ref('')

// Grid columns based on visible elements
const gridCols = computed(() => {
  return props.showTime ? 'grid-cols-1 md:grid-cols-2' : 'grid-cols-1'
})

// Initialize from modelValue
onMounted(() => {
  if (props.modelValue) {
    updateFromModelValue(props.modelValue)
  }
})

// Watch for external changes
watch(
  () => props.modelValue,
  (newValue) => {
    if (newValue) {
      updateFromModelValue(newValue)
    } else {
      resetValues()
    }
  }
)

// Watch internal state changes to emit updates
watch(
  [dateValue, timeValue],
  () => {
    if (dateValue.value) {
      emitDateTime()
    } else {
      emit('update:modelValue', null)
    }
  },
  { deep: true }
)

// Convert from Date object or ISO string to component values
function updateFromModelValue(value) {
  const date = new Date(value)

  // Format date as YYYY-MM-DD for input[type="date"]
  dateValue.value = formatDateForInput(date)

  if (props.showTime) {
    // Format time as HH:MM for input[type="time"]
    timeValue.value = formatTimeForInput(date)
  }
}

// Helper to format date for input
function formatDateForInput(date) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

// Helper to format time for input
function formatTimeForInput(date) {
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${hours}:${minutes}`
}

// Emit the combined date/time value
function emitDateTime() {
  if (!dateValue.value) return

  // Parse date components
  const [year, month, day] = dateValue.value.split('-')

  // Default time is midnight
  let hours = 0
  let minutes = 0

  // Parse time if provided
  if (props.showTime && timeValue.value) {
    const [timeHours, timeMinutes] = timeValue.value.split(':')
    hours = parseInt(timeHours, 10)
    minutes = parseInt(timeMinutes, 10)
  }

  // Simple date without timezone consideration
  const dateObj = new Date(
    parseInt(year, 10),
    parseInt(month, 10) - 1,
    parseInt(day, 10),
    hours,
    minutes
  )

  emit('update:modelValue', dateObj)
}

// Reset all values
function resetValues() {
  dateValue.value = ''
  timeValue.value = ''
}
</script>
