<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Typography as="legend" variant="label" :for="id">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </Typography>

      <RadioGroupRoot
        v-model="selected"
        class="flex flex-col gap-3"
        :aria-label="label"
        @update:modelValue="$emit('update:modelValue', $event)"
      >
        <div v-for="option in options" :key="getOptionValue(option)" class="flex items-center">
          <RadioGroupItem
            :id="getOptionValue(option)"
            :name="getOptionValue(option)"
            :value="getOptionValue(option)"
            class="relative w-4 h-4 rounded-full border-2 border-gray-200 hover:border-primary-default focus:outline-none focus:ring-2 focus:ring-primary-defaultcursor-pointer"
          >
            <RadioGroupIndicator class="absolute inset-0 flex items-center justify-center">
              <div class="w-2 h-2 rounded-full bg-primary-default"></div>
            </RadioGroupIndicator>
          </RadioGroupItem>
          <label class="text-[15px] leading-none pl-[15px]" :for="getOptionValue(option)">
            {{ getOptionLabel(option) }}
          </label>
        </div>
      </RadioGroupRoot>
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { ref } from 'vue'
import { RadioGroupRoot, RadioGroupItem, RadioGroupIndicator } from 'radix-vue'

import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import InputError from '@/Stencil/Forms/InputError.vue'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  modelValue: {
    type: [String, Number],
    required: true,
  },
  options: {
    type: Array,
    required: true,
    validator: (options) => {
      return options.every((option) => 'value' in option && 'label' in option)
    },
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
  label: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  orientation: {
    type: String,
    default: 'vertical',
    validator: (value) => ['vertical', 'horizontal'].includes(value),
  },
  wrapperClass: {
    type: String,
    default: '',
  },
  groupClass: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
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
const selected = ref(getOptionValue(props.modelValue))
</script>

<style scoped>
/* Optional: Add transitions for smoother state changes */
.radio-group-item {
  transition: all 150ms ease;
}

.radio-group-indicator {
  transition: transform 150ms ease;
}
</style>
