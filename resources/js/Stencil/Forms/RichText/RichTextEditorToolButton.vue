<template>
  <Toggle
    type="button"
    v-model:pressed="toggleState"
    aria-label="Toggle"
    @click="emit('click')"
    class="editor-btn data-[state=on]:bg-gray-100 rounded border border-transparent data-[state=on]:border-gray-300 p-4"
  >
    <span>
      <slot />
    </span>
  </Toggle>
</template>

<script setup>
import { ref } from 'vue'
import { Toggle } from 'radix-vue'

const emit = defineEmits(['click'])
const toggleState = ref(false)
</script>

<style scoped lang="postcss">
.editor-btn {
  @apply flex justify-center items-center h-5 w-5 hover:bg-gray-100;
}
</style>
