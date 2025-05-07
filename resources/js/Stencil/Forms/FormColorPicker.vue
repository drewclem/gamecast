<template>
  <Stack space="xsmall">
    <Stack space="small">
      <Typography as="label" variant="label" :for="id">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </Typography>
      <div class="card-shadow rounded-full overflow-hidden h-16 w-16">
        <input
          :id="id"
          type="color"
          :value="modelValue"
          @input="updateValue($event.target.value)"
          class="h-16 w-16 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        />
      </div>
    </Stack>
    <InputError :error="error" />
  </Stack>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import InputError from '@/Stencil/Forms/InputError.vue'

defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  modelValue: {
    type: String,
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
})

const emit = defineEmits(['update:modelValue'])

function updateValue(color) {
  emit('update:modelValue', color)
}
</script>

<style scoped>
input[type='color'] {
  -webkit-appearance: none;
  appearance: none;
  padding: 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type='color']::-webkit-color-swatch-wrapper {
  padding: 0;
}

input[type='color']::-webkit-color-swatch {
  border: none;
  border-radius: 4px;
}
</style>
