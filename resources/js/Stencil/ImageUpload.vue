<template>
  <div class="w-full">
    <div class="flex items-start gap-4">
      <!-- Upload Circle -->
      <div
        class="relative border border-dashed rounded-full aspect-square w-24 flex-shrink-0"
        :class="{
          'border-primary-500 bg-primary-50': isDragging,
          'border-gray-300 hover:border-gray-400': !isDragging,
          'p-6': !preview,
          'p-0 border': preview,
        }"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
      >
        <!-- Hidden File Input -->
        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
          :class="{ hidden: preview }"
          @change="handleFileInput"
        />

        <!-- Upload State -->
        <div v-if="!preview" class="h-full flex items-center justify-center">
          <RiUploadCloudLine v-if="!isDragging" class="w-8 h-8 text-gray-400" />
          <RiImageLine v-else class="w-8 h-8 text-gray-400" />
        </div>

        <!-- Preview State -->
        <template v-else>
          <div class="relative w-full h-full">
            <img
              :src="preview"
              alt="Avatar Preview"
              class="w-full h-full rounded-full object-cover"
            />

            <!-- Remove Button -->
            <button
              v-if="isDirty"
              @click.stop="removeImage"
              class="absolute -top-2 -right-1 p-1 bg-white card-shadow border border-gray-200 rounded-full shadow-lg hover:bg-gray-100 transition-colors z-50"
            >
              <RiCloseLine class="w-4 h-4 text-red-500" />
            </button>

            <!-- Change Image Overlay -->
            <div
              class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity bg-black/50 rounded-full"
              @click.stop="$refs.fileInput.click()"
            >
              <button class="p-2 bg-white rounded-full hover:bg-gray-50">
                <RiImageEditLine class="w-4 h-4" />
              </button>
            </div>
          </div>
        </template>
      </div>

      <!-- Text Content -->
      <Stack class="mt-2" space="small">
        <h3 class="font-medium text-gray-900">
          {{ preview ? 'Profile picture uploaded' : 'Upload profile picture' }}
        </h3>
        <Stack space="xsmall">
          <p class="text-sm text-gray-500">
            {{
              isDragging
                ? 'Drop your image here'
                : preview
                  ? 'Click the image to change your profile picture'
                  : 'Drop your image here, or click to browse'
            }}
          </p>
          <p class="text-xs text-gray-400">Supports JPG or PNG up to 5MB</p>

          <!-- Error Message -->
          <p v-if="error" class="mt-2 text-sm text-red-600 flex items-center gap-1">
            <ri-error-warning-line class="w-4 h-4" />
            {{ error }}
          </p>
        </Stack>

        <template v-if="withSaveAction">
          <transition name="fade">
            <div>
              <slot name="action" />
            </div>
          </transition>
        </template>
      </Stack>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import {
  RiUploadCloudLine,
  RiImageLine,
  RiCloseLine,
  RiImageEditLine,
  RiErrorWarningLine,
} from '@remixicon/vue'

import Button from '@/Stencil/Button.vue'
import Stack from '@/Stencil/Stack.vue'

const props = defineProps({
  modelValue: {
    type: [String, File],
    default: null,
  },
  maxSize: {
    type: Number,
    default: 5 * 1024 * 1024, // 5MB for avatars
  },
  withSaveAction: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['imageSelected', 'error'])

const preview = ref(props.modelValue)
const isDragging = ref(false)
const error = ref(null)
const fileInput = ref(null)

const isDirty = computed(() => props.modelValue !== preview.value)

const validateFile = (file) => {
  if (!file) return false

  // Check file type - more strict for avatars
  const allowedTypes = ['image/jpeg', 'image/png']
  if (!allowedTypes.includes(file.type)) {
    error.value = 'Please upload a JPG or PNG file'
    return false
  }

  // Check file size
  if (file.size > props.maxSize) {
    error.value = `File size must be less than ${props.maxSize / 1024 / 1024}MB`
    return false
  }

  error.value = null
  return true
}

const handleFile = (file) => {
  if (!validateFile(file)) return

  const reader = new FileReader()
  reader.onload = (e) => {
    preview.value = e.target.result
    emit('imageSelected', file)
  }
  reader.readAsDataURL(file)
}

const handleFileInput = (event) => {
  const file = event.target.files[0]
  handleFile(file)
}

const handleDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer.files[0]
  handleFile(file)
}

const removeImage = () => {
  preview.value = null
  emit('imageSelected', null)
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
