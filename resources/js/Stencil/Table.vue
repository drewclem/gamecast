<template>
  <div class="overflow-hidden overflow-x-auto rounded-md">
    <table class="min-w-full">
      <thead>
        <slot name="columns" />
      </thead>

      <tbody class="divide-y" :class="{ 'bg-white': hasBackground }">
        <slot name="rows" />
      </tbody>
    </table>

    <div v-if="$slots.empty" class="flex justify-center items-center p-6">
      <slot name="empty" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  hasBackground: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped>
table {
  @apply rounded-md overflow-hidden;
  table-layout: fixed;
}

table thead {
  @apply bg-gray-50 rounded text-left;
}

table thead :deep(tr th) {
  @apply font-medium uppercase opacity-50 text-xs;
}

table thead :deep(tr th),
table tbody :deep(tr td) {
  @apply py-4 px-6 whitespace-nowrap;
}

table tbody :deep(tr td) {
  @apply text-sm overflow-hidden text-ellipsis whitespace-nowrap;
}

table tbody :deep(tr td[grow]) {
  max-width: 65ch;
  min-width: content;
}
</style>
