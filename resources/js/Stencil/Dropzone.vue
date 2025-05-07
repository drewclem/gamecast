<template>
  <div class="flex items-start gap-4">
    <!-- File Preview -->
    <div
      v-if="selectedFile"
      class="w-48 flex-shrink-0 bg-gray-50 rounded-lg border border-gray-200 p-3"
    >
      <div class="flex items-center gap-3">
        <!-- PDF Icon -->
        <div class="text-red-500">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-6 h-6"
            viewBox="0 0 24 24"
            fill="currentColor"
          >
            <path
              d="M23 0H7C6.45 0 6 .45 6 1V6H1C.45 6 0 6.45 0 7V23C0 23.55.45 24 1 24H17C17.55 24 18 23.55 18 23V18H23C23.55 18 24 17.55 24 17V1C24 .45 23.55 0 23 0ZM16 22H2V8H16V22ZM22 16H18V7C18 6.45 17.55 6 17 6H8V2H22V16Z"
            />
          </svg>
        </div>

        <!-- File Details -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate">
            {{ selectedFile.name }}
          </p>
          <p class="text-xs text-gray-500">
            {{ formatFileSize(selectedFile.size) }}
          </p>
        </div>

        <!-- Remove Button -->
        <button @click="removeFile" class="text-gray-400 hover:text-gray-500">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Dropzone -->
    <div
      v-else
      class="flex-grow dropzone"
      @drag.prevent
      @dragstart.prevent
      @dragend.prevent
      @dragover.prevent
      @dragenter.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      :class="{
        'dropzone--active': isDragging,
        'dropzone--hasFile': selectedFile,
      }"
    >
      <div class="dropzone__content">
        <div v-if="isDragging">Drop your resume here</div>
        <div v-else>
          <span class="text-gray-600">Drag your resume here or</span>
          <span class="text-blue-500 hover:text-blue-600 ml-1">browse</span>
        </div>
        <p class="text-xs text-gray-500 mt-1">PDF files up to {{ formatFileSize(maxFileSize) }}</p>
        <input
          type="file"
          accept=".pdf,application/pdf"
          class="dropzone__input"
          @change="handleFileInput"
          ref="fileInput"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeUnmount } from 'vue'

const props = defineProps({
  maxFileSize: {
    type: Number,
    default: 10 * 1024 * 1024, // 10MB in bytes
  },
  modelValue: {
    type: [File, String, null],
    default: null,
  },
})

const emit = defineEmits(['fileSelected', 'error'])

const isDragging = ref(false)
const fileInput = ref(null)
const selectedFile = ref(null)

const handleDrop = (e) => {
  isDragging.value = false
  const files = [...e.dataTransfer.files]
  handleFiles(files)
}

const handleFileInput = (e) => {
  const files = [...e.target.files]
  handleFiles(files)
}

const handleFiles = (files) => {
  const file = files[0] // Get first file

  if (!validateFile(file)) return

  selectedFile.value = file
  emit('fileSelected', file)
}

const validateFile = (file) => {
  // Check file type
  if (file.type !== 'application/pdf') {
    emit('error', 'Please upload a PDF file')
    return false
  }

  // Check file size
  if (file.size > props.maxFileSize) {
    emit('error', `File must be smaller than ${formatFileSize(props.maxFileSize)}`)
    return false
  }

  return true
}

const removeFile = () => {
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
  emit('fileSelected', null)
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'

  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))

  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>

<style scoped>
.dropzone {
  @apply border border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer
    transition-colors duration-200 min-h-[120px] flex items-center justify-center relative;
}

.dropzone--active {
  @apply border-primary-500 bg-primary-50;
}

.dropzone--hasFile {
  @apply border-green-500 bg-green-50;
}

.dropzone__input {
  @apply absolute inset-0 w-full h-full opacity-0 cursor-pointer;
}

.dropzone__content {
  @apply text-gray-500 relative;
}
</style>
