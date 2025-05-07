<template>
  <TabsRoot :default-value="defaultTab" class="border border-gray-200 rounded-lg card-shadow">
    <TabsList class="relative shrink-0 flex border-b border-gray-200" :aria-label="ariaLabel">
      <TabsIndicator
        class="absolute left-0 h-[2px] bottom-0 bg-primary-default w-[var(--radix-tabs-indicator-size)] translate-x-[var(--radix-tabs-indicator-position)] transition-all duration-150"
      />

      <TabsTrigger
        v-for="(tab, index) in tabs"
        :key="tab.value"
        :value="tab.value"
        class="bg-white px-3 py-2 flex-1 flex items-center justify-center select-none hover:text-primary data-[state=active]:text-primary-default outline-none cursor-pointer focus-visible:relative focus-visible:shadow-[0_0_0_2px] focus-visible:card-shadow"
        :class="{
          'rounded-tl-md': index === 0,
          'rounded-tr-md': index === tabs.length - 1,
        }"
        @click.prevent
      >
        <span class="flex items-center space-x-2">
          <Icon :icon="tab.icon" v-if="tab.icon" size="small" />
          <Typography class="font-semibold">{{ tab.label }}</Typography>
        </span>
        <span
          v-if="tab.count !== undefined"
          class="ml-2 px-2 py-0.5 text-xs rounded-full bg-mauve3 text-mauve11"
        >
          {{ tab.count }}
        </span>
      </TabsTrigger>
    </TabsList>

    <TabsContent
      v-for="tab in tabs"
      :key="tab.value"
      :value="tab.value"
      class="grow p-5 bg-white rounded-b-md outline-none focus:card-shadow data-[state=inactive]:hidden"
    >
      <TransitionGroup name="tab-transition" mode="out-in">
        <component :is="tab.component" v-bind="tab.props || {}" />
      </TransitionGroup>
    </TabsContent>
  </TabsRoot>
</template>

<script setup>
import { TransitionGroup } from 'vue'
import { TabsRoot, TabsList, TabsTrigger, TabsContent, TabsIndicator } from 'radix-vue'

import Typography from '@/Stencil/Typography.vue'
import Icon from '@/Stencil/Icon.vue'

defineProps({
  tabs: {
    type: Array,
    required: true,
  },
  defaultTab: {
    type: String,
    default: '',
  },
  ariaLabel: {
    type: String,
    default: 'Navigation tabs',
  },
})
</script>

<style scoped>
.tab-transition-enter-active,
.tab-transition-leave-active {
  transition: all 150ms ease;
}

.tab-transition-enter-from {
  opacity: 0;
  transform: translateY(4px);
}

.tab-transition-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

.tab-transition-enter-to,
.tab-transition-leave-from {
  opacity: 1;
  transform: translateY(0);
}
</style>
