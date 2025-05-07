<script setup>
import { useFlash } from '@/Utils/useFlash'
import { watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Icon from '@/Stencil/Icon.vue'

const page = usePage()

const { flashes, flashSuccess, flashError, removeFlash } = useFlash()

function addFlashMessages(pageProps) {
  const flash = pageProps.flash
  if (!flash) {
    return
  }
  if (flash.success) {
    flashSuccess(flash.success)
  }
  if (flash.error) {
    flashError(flash.error)
  }
}

onMounted(() => {
  addFlashMessages(page.props)
})

watch(
  () => page.props,
  (pageProps) => {
    if (pageProps.errors) {
      const errorCount = Object.keys(pageProps.errors).length

      if (errorCount > 0) {
        let msg = 'There is one form error'
        if (errorCount > 1) {
          msg = `There are ${errorCount} form errors.`
        }

        flashError(msg)
      }
    }

    addFlashMessages(pageProps)
  }
)

function getIconColor(flash) {
  if (flash.type === 'success') {
    return 'text-primary-600'
  }
  if (flash.type === 'info') {
    return 'text-blue-600'
  }
  if (flash.type === 'error') {
    return 'text-red-600'
  }
}
</script>

<template>
  <div class="fixed top-0 right-0 z-50 m-6">
    <transition-group name="slide-fade">
      <div
        v-for="(flash, index) in flashes"
        :key="index"
        class="relative p-3 pr-12 mb-4 rounded-lg shadow-xl border"
        :class="{
          'text-primary-600  bg-primary-50 border-primary-300': flash.type === 'success',
          'text-blue-600  bg-blue-50 border-blue-300': flash.type === 'info',
          'text-red-600  bg-red-50 border-red-300': flash.type === 'error',
        }"
        style="min-width: 240px"
      >
        <button
          class="absolute top-0 right-0 mt-1 px-3 py-2 text-2xl opacity-75 cursor-pointer hover:opacity-100 focus:outline-none focus:opacity-100"
          type="button"
          @click="removeFlash(flash.id)"
        >
          <Icon icon="x" size="xsmall" :color="getIconColor(flash)" />
        </button>

        <div class="flex items-center text-sm font-medium">
          {{ flash.message }}
        </div>
      </div>
    </transition-group>
  </div>
</template>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.4s;
}

.slide-fade-enter-from,
.slide-fade-enter,
.slide-fade-leave-to {
  transform: translateX(400px);
  opacity: 0;
}
</style>
