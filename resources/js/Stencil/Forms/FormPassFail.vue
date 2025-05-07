<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Stack space="xsmall">
        <Typography as="label" variant="label" :for="id" :class="{ 'sr-only': hideLabel }">
          {{ label }}
          <span v-if="required" class="text-red-500">*</span>
        </Typography>
        <Typography v-if="helper" variant="body-small" class="opacity-60">{{ helper }}</Typography>
      </Stack>

      <div class="flex items-center gap-3">
        <button
          type="button"
          class="assessment-button upvote"
          :class="{ selected: modelValue === 1 }"
          @click="updateValue(1)"
          :aria-pressed="modelValue === 1"
        >
          <RiThumbUpFill v-if="modelValue === 1" class="w-4 h-4" />
          <RiThumbUpLine v-else class="w-4 h-4" />
          <span class="sr-only">Pass</span>
        </button>

        <button
          type="button"
          class="assessment-button downvote"
          :class="{ selected: modelValue === -1 }"
          @click="updateValue(-1)"
          :aria-pressed="modelValue === -1"
        >
          <RiThumbDownFill v-if="modelValue === -1" class="w-4 h-4" />
          <RiThumbDownLine v-else class="w-4 h-4" />
          <span class="sr-only">Fail</span>
        </button>
      </div>
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { RiThumbUpFill, RiThumbUpLine, RiThumbDownFill, RiThumbDownLine } from '@remixicon/vue'

import Typography from '@/Stencil/Typography.vue'
import Stack from '@/Stencil/Stack.vue'
import InputError from '@/Stencil/Forms/InputError.vue'

const props = defineProps({
  modelValue: {
    type: Number,
    default: 0,
  },
  label: {
    type: String,
    default: 'Assessment',
  },
  error: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

const updateValue = (value) => {
  // If already selected, clicking again deselects (sets to 0)
  const newValue = props.modelValue === value ? 0 : value
  emit('update:modelValue', newValue)
}
</script>

<style scoped>
.assessment-button {
  @apply flex items-center px-4 py-2 gap-2 border border-gray-200 rounded-md;
}

.assessment-button:hover {
  background-color: #f9fafb;
}

.assessment-button.upvote.selected[aria-pressed='true'] {
  background-color: #dcfce7;
  border-color: #10b981;
  color: #065f46;
}

.assessment-button.downvote.selected[aria-pressed='true'] {
  background-color: #fee2e2;
  border-color: #ef4444;
  color: #b91c1c;
}
</style>
