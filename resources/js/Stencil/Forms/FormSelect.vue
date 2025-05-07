<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Typography as="label" variant="label" :for="id" :class="{ 'sr-only': hideLabel }">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </Typography>
      <select
        ref="select"
        :id="id"
        :name="id"
        :class="inputClasses"
        :value="modelValue"
        @change="emit('update:modelValue', $event.target.value)"
      >
        <option v-if="placeholder" value="">
          {{ placeholder }}
        </option>
        <option
          v-for="option in options"
          :key="getOptionValue(option)"
          :value="getOptionValue(option)"
          :disabled="option.disabled"
          :selected="getOptionValue(option) === modelValue"
        >
          {{ getOptionLabel(option) }}
        </option>
      </select>
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
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
    type: [String, Number],
    required: true,
  },
  required: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
  placeholder: {
    type: String,
    default: 'Select an option',
  },
  options: {
    type: Array,
    required: true,
    default: () => [],
  },
  optionValue: {
    type: [String, Function],
    default: 'id',
  },
  optionLabel: {
    type: [String, Function],
    default: 'name',
  },
  hideLabel: {
    type: Boolean,
    default: false,
  },
  hideBorder: {
    type: Boolean,
    default: false,
  },
})

const select = ref(null)

const inputClasses = computed(() => {
  return props.hideBorder ? 'hide-border' : 'input'
})

const getOptionValue = (option) => {
  if (typeof props.optionValue === 'function') {
    return props.optionValue(option)
  }
  return typeof option === 'object' ? option[props.optionValue] : option
}

const getOptionLabel = (option) => {
  if (typeof props.optionLabel === 'function') {
    return props.optionLabel(option)
  }
  return typeof option === 'object' ? option[props.optionLabel] : option
}

onMounted(() => {
  if (select.value.hasAttribute('autofocus')) {
    select.value.focus()
  }
})

defineExpose({ focus: () => select.value.focus() })
</script>

<style>
.hide-border {
  @apply border-none focus:ring-primary-200 rounded-md;
}
</style>
