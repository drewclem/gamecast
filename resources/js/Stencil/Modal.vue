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
        :class="[
          'fixed z-50 flex flex-col bg-white shadow-xl rounded-lg',
          'top-[50%] left-[50%] -translate-x-[50%] -translate-y-[50%]',
          'w-[90vw] max-h-[85vh] md:w-full md:max-w-lg',
          'data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-top',
          'data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-bottom',
        ]"
      >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6">
          <DialogTitle class="text-lg font-semibold leading-6 text-gray-900">
            {{ title }}
          </DialogTitle>
          <DialogClose class="rounded-md text-red-400 hover:text-red-500 focus:outline-none">
            <span class="sr-only">Close panel</span>
            <Icon icon="x" size="small" aria-hidden="true" />
          </DialogClose>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
          <slot />
        </div>

        <!-- Optional footer -->
        <div v-if="$slots.footer" class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
          <slot name="footer" />
        </div>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>

<script setup>
import {
  DialogRoot,
  DialogPortal,
  DialogOverlay,
  DialogContent,
  DialogTitle,
  DialogClose,
} from 'radix-vue'
import Icon from '@/Stencil/Icon.vue'

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
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

  .slide-in-from-top {
    animation-name: slide-in-from-top;
  }

  .slide-out-to-bottom {
    animation-name: slide-out-to-bottom;
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

  @keyframes slide-in-from-top {
    from {
      opacity: 0;
      transform: translate(-50%, -60%);
    }
    to {
      opacity: 1;
      transform: translate(-50%, -50%);
    }
  }

  @keyframes slide-out-to-bottom {
    from {
      opacity: 1;
      transform: translate(-50%, -50%);
    }
    to {
      opacity: 0;
      transform: translate(-50%, -40%);
    }
  }
}
</style>
