<template>
  <AccordionRoot
    class="AccordionRoot card-shadow"
    :type="type"
    :default-value="defaultValue"
    :collapsible="collapsible"
    :disabled="disabled"
    :orientation="orientation"
    :as="as"
  >
    <slot />
  </AccordionRoot>
</template>

<script setup>
import { AccordionRoot } from 'radix-vue'

defineProps({
  type: {
    type: String,
    default: 'single',
  },
  defaultValue: {
    type: String,
    default: null,
  },
  collapsible: {
    type: Boolean,
    default: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  orientation: {
    type: String,
    default: 'vertical',
  },
  as: {
    type: String,
    default: 'div',
  },
})
</script>

<style>
.AccordionRoot {
  @apply border border-gray-200 rounded-md bg-white;
}
.AccordionChevron {
  transition: transform 150ms;
}

.AccordionTrigger {
  @apply w-full px-4 py-2 font-semibold;
}

.AccordionItem:not(:last-child) .AccordionTrigger,
.AccordionTrigger[data-state='open'] {
  @apply border-b border-gray-200;
}

.AccordionTrigger[data-state='open'] > .AccordionChevron {
  transform: rotate(180deg);
}

.AccordionContent {
  @apply overflow-hidden px-6 py-3 border-b border-gray-200;
}
.AccordionContent[data-state='open'] {
  animation: slideDown 150ms ease-in-out;
}
.AccordionContent[data-state='closed'] {
  animation: slideUp 150ms ease-in-out;
}

@keyframes slideDown {
  from {
    height: 0;
  }
  to {
    height: var(--radix-accordion-content-height);
  }
}

@keyframes slideUp {
  from {
    height: var(--radix-accordion-content-height);
  }
  to {
    height: 0;
  }
}
</style>
