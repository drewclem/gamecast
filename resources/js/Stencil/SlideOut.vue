<template>
  <DialogRoot :open="isOpen" modal @update:open="close">
    <DialogPortal>
      <!-- Overlay -->
      <DialogOverlay
        :class="[
          'fixed inset-0 z-50 bg-black/25',
          'data-[state=open]:animate-in data-[state=open]:fade-in',
          'data-[state=closed]:animate-out data-[state=closed]:fade-out',
        ]"
        @click="emit('close')"
      />

      <!-- Content -->
      <DialogContent
        class="fixed inset-y-0 right-0 z-50 flex flex-col bg-white shadow-xl w-screen sm:w-3/4 lg:w-1/2 data-[state=open]:animate-in data-[state=open]:slide-in-from-right data-[state=closed]:animate-out data-[state=closed]:slide-out-to-right"
        :class="widthClasses"
      >
        <!-- Header -->
        <div
          class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 sm:px-6"
        >
          <DialogTitle class="text-lg font-semibold leading-6 text-gray-900">
            {{ title }}
          </DialogTitle>
          <DialogClose class="rounded-md text-gray-300 hover:text-red-500 focus:outline-none">
            <span class="sr-only">Close panel</span>
            <Icon icon="x" size="small" aria-hidden="true" />
          </DialogClose>
        </div>

        <!-- Content -->
        <div class="relative flex-1 overflow-y-auto" :class="paddingClasses">
          <slot />
        </div>

        <!-- Optional footer -->
        <div
          v-if="$slots.footer"
          class="sticky bottom-0 border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
          :class="paddingClasses"
        >
          <slot name="footer" />
        </div>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>

<script setup>
import { computed } from 'vue'
import {
  DialogRoot,
  DialogPortal,
  DialogOverlay,
  DialogContent,
  DialogTitle,
  DialogClose,
} from 'radix-vue'
import Icon from '@/Stencil/Icon.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  padding: {
    type: String,
    default: 'medium',
  },
  width: {
    type: String,
    default: 'default',
    validator: (value) => ['small', 'default', 'wide'].includes(value),
  },
})

const paddingClasses = computed(() => {
  if (props.padding === 'none') {
    return 'p-0'
  }

  return 'px-4 py-6 sm:px-6'
})

const widthClasses = computed(() => {
  switch (props.width) {
    case 'small':
      return 'xl:w-1/3'
    case 'wide':
      return 'xl:w-3/4'
    default:
      return 'xl:w-2/5'
  }
})

const emit = defineEmits(['close'])

function close() {
  emit('close')
}
</script>

<style>
@tailwind utilities;

@layer utilities {
  .animate-in {
    animation-duration: 300ms;
    animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  }

  .animate-out {
    animation-duration: 200ms;
    animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  }

  .fade-in {
    animation-name: fade-in;
  }

  .fade-out {
    animation-name: fade-out;
  }

  .slide-in-from-right {
    animation-name: slide-in-from-right;
  }

  .slide-out-to-right {
    animation-name: slide-out-to-right;
  }

  @keyframes fade-in {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  @keyframes fade-out {
    from {
      opacity: 1;
    }
    to {
      opacity: 0;
    }
  }

  @keyframes slide-in-from-right {
    from {
      transform: translateX(100%);
    }
    to {
      transform: translateX(0);
    }
  }

  @keyframes slide-out-to-right {
    from {
      transform: translateX(0);
    }
    to {
      transform: translateX(100%);
    }
  }
}
</style>
