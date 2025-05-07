<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Typography as="label" variant="label" :for="id">
        {{ label }}
        <span v-if="required" class="text-red-600">*</span>
      </Typography>

      <ComboboxRoot v-model="selected" :multiple="isMultiple" class="relative">
        <ComboboxAnchor class="input flex items-center justify-between">
          <ComboboxInput :id="id" :name="id" class="input w-full" :placeholder="placeholder" />
          <ComboboxTrigger>
            <Icon
              icon="chevron-down"
              size="small"
              class="absolute right-0 top-1.5 mr-2 text-gray-500"
            />
          </ComboboxTrigger>
        </ComboboxAnchor>

        <ComboboxContent
          class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200"
        >
          <ComboboxViewport class="max-h-60 overflow-y-auto p-1">
            <ComboboxEmpty class="text-sm text-gray-500 p-2 text-center">
              No results found
            </ComboboxEmpty>

            <template v-if="hasGroups">
              <ComboboxGroup v-for="(group, groupName) in groupedOptions" :key="groupName">
                <ComboboxLabel class="px-3 py-1 text-xs font-medium text-gray-500">
                  {{ groupName }}
                </ComboboxLabel>

                <ComboboxItem
                  v-for="option in group"
                  :key="getOptionValue(option)"
                  :value="getOptionValue(option)"
                  class="relative flex items-center px-3 py-2 text-sm rounded-md cursor-pointer select-none"
                  :class="{
                    'bg-primary-50 text-primary-900': isSelected(option),
                    'hover:bg-gray-50': !isSelected(option),
                  }"
                >
                  <ComboboxItemIndicator class="absolute left-1.5">
                    <Icon icon="check" size="xsmall" class="text-primary-600" />
                  </ComboboxItemIndicator>
                  <span class="ml-6">{{ getOptionLabel(option) }}</span>
                </ComboboxItem>

                <ComboboxSeparator v-if="!isLastGroup(groupName)" class="h-px bg-gray-200 my-1" />
              </ComboboxGroup>
            </template>

            <template v-else>
              <ComboboxItem
                v-for="option in options"
                :key="getOptionValue(option)"
                :value="getOptionValue(option)"
                class="relative flex items-center px-1 py-2 text-sm rounded-md cursor-pointer select-none"
                :class="{
                  'bg-primary-50 text-primary-900': isSelected(option.id),
                  'hover:bg-gray-50': !isSelected(option),
                }"
              >
                <ComboboxItemIndicator class="absolute left-1.5">
                  <Icon icon="check" size="xsmall" class="text-primary-600" />
                </ComboboxItemIndicator>
                <span class="ml-6">{{ getOptionLabel(option) }}</span>
              </ComboboxItem>
            </template>
          </ComboboxViewport>
        </ComboboxContent>
      </ComboboxRoot>
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  ComboboxRoot,
  ComboboxAnchor,
  ComboboxTrigger,
  ComboboxInput,
  ComboboxContent,
  ComboboxViewport,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxLabel,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxSeparator,
} from 'radix-vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import Icon from '@/Stencil/Icon.vue'
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
    type: [String, Number, Array],
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
  isMultiple: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Select an option...',
  },
  options: {
    type: Array,
    required: true,
  },
  optionValue: {
    type: [String, Function],
    default: (value) => value,
  },
  optionLabel: {
    type: [String, Function],
    default: (value) => value,
  },
  optionGroup: {
    type: String,
    default: null,
  },
})

const selected = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const hasGroups = computed(() => !!props.optionGroup)

const groupedOptions = computed(() => {
  if (!props.optionGroup) return null

  return props.options.reduce((groups, option) => {
    const groupName = option[props.optionGroup] || 'Other'
    if (!groups[groupName]) {
      groups[groupName] = []
    }
    groups[groupName].push(option)
    return groups
  }, {})
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

function isSelected(option) {
  if (typeof selected.value === 'object') {
    return selected.value.includes(getOptionValue(option))
  }

  return getOptionValue(option) === selected.value
}

const isLastGroup = (groupName) => {
  const groups = Object.keys(groupedOptions.value)
  return groups[groups.length - 1] === groupName
}
</script>
