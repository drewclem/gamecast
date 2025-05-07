<template>
  <nav v-if="paginator.meta?.links?.length > 3" class="flex justify-center p-3 lg:p-4 border-t">
    <div class="flex items-center space-x-5 px-4 sm:px-0">
      <div class="flex -mt-px">
        <component
          :is="renderedComponent"
          :preserve-scroll="useInertiaLinks && preserveScroll"
          :preserve-state="useInertiaLinks && preserveState"
          :href="previousPageLink.url"
          :class="{
            'pointer-events-none opacity-25': !previousPageLink.url,
          }"
          class="inline-flex items-center font-semibold border h-6 w-6 text-primary-500 opacity-50 border-primary-500 hover:opacity-100 rounded-full p-1"
          @click.stop="(e) => $emit('click', e, paginator.meta?.current_page - 1)"
        >
          <RiArrowLeftDoubleLine class="w-4 h-4" />
          <span class="sr-only">Previous</span>
        </component>
      </div>

      <div class="hidden md:-mt-px md:flex space-x-3">
        <template v-for="numberedLink in numberedLinks" :key="numberedLink.label">
          <component
            :is="renderedComponent"
            v-if="numberedLink.label !== '...'"
            :preserve-scroll="useInertiaLinks && preserveScroll"
            :preserve-state="useInertiaLinks && preserveState"
            :href="numberedLink.url"
            class="border-primary-500 text-primary-500 border rounded-full font-semibold p-1 text-xs w-6 h-6 flex justify-center items-center"
            :class="[
              numberedLink.active
                ? 'opacity-100 text-primary-500 border '
                : 'inline-flex items-center opacity-60 hover:opacity-100',
            ]"
            @click.stop="(e) => $emit('click', e, numberedLink.label)"
          >
            {{ numberedLink.label }}
          </component>
          <span
            v-else
            class="inline-flex items-center justify-center font-semibold border text-primary-500 h-6 w-6 text-xs opacity-50 border-primary-500 hover:opacity-100 rounded-full p-1"
          >
            ...
          </span>
        </template>
      </div>

      <div class="flex w-0 -mt-px">
        <component
          :is="renderedComponent"
          :preserve-scroll="useInertiaLinks && preserveScroll"
          :preserve-state="useInertiaLinks && preserveState"
          :href="nextPageLink.url"
          :class="{
            'pointer-events-none opacity-25': !nextPageLink.url,
          }"
          class="inline-flex items-center font-semibold border h-6 w-6 text-primary-500 opacity-50 border-primary-500 hover:opacity-100 rounded-full p-1"
          @click.stop="(e) => $emit('click', e, paginator.meta?.current_page + 1)"
        >
          <RiArrowRightDoubleLine class="w-4 h-4" />
          <span class="sr-only"> Next </span>
        </component>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

import { RiArrowLeftDoubleLine, RiArrowRightDoubleLine } from '@remixicon/vue'

defineEmits(['click'])

const props = defineProps({
  paginator: {
    type: Object,
    required: true,
  },
  useInertiaLinks: { type: Boolean, default: true },
  preserveState: { type: Boolean, default: false },
  preserveScroll: { type: Boolean, default: false },
})
const previousPageLink = computed(() => {
  return props.paginator.meta.links[0]
})

const nextPageLink = computed(() => {
  return props.paginator.meta.links[props.paginator.meta.links.length - 1]
})

const numberedLinks = computed(() => {
  return props.paginator.meta.links.slice(1, props.paginator.meta.links.length - 1)
})

const renderedComponent = computed(() => (props.useInertiaLinks ? Link : 'a'))
</script>
