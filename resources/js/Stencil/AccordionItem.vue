<template>
  <AccordionItem :value="value" :disabled="disabled" class="AccordionItem">
    <AccordionHeader>
      <AccordionTrigger class="AccordionTrigger">
        <div class="flex items-center justify-between w-full">
          <div class="flex text-left items-center space-x-2">
            <slot name="title" />
            <div>
              <slot name="badge" />
            </div>
          </div>

          <Icon icon="chevron-down" size="xsmall" class="text-primary-500 AccordionChevron" />
        </div>
      </AccordionTrigger>
    </AccordionHeader>
    <AccordionContent class="AccordionContent">
      <slot />
    </AccordionContent>
  </AccordionItem>
</template>

<script setup>
import { AccordionItem, AccordionHeader, AccordionTrigger, AccordionContent } from 'radix-vue'

import Icon from '@/Stencil/Icon.vue'
import Typography from '@/Stencil/Typography.vue'
import Card from '@/Stencil/Card.vue'

defineProps({
  value: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped></style>
