<template>
  <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <!-- Header with controls -->
    <div class="px-2 py-1 border-b flex items-center justify-end">
      <div class="flex items-center gap-4">
        <!-- Zoom Controls -->
        <div class="flex items-center gap-2">
          <button
            @click="zoom -= 0.1"
            :disabled="zoom <= 0.5"
            class="p-2 hover:bg-gray-100 rounded-full disabled:opacity-50"
          >
            <RiSubtractLine class="w-4 h-4" />
          </button>
          <span class="text-sm">{{ Math.round(zoom * 100) }}%</span>
          <button
            @click="zoom += 0.1"
            :disabled="zoom >= 2"
            class="p-2 hover:bg-gray-100 rounded-full disabled:opacity-50"
          >
            <RiAddLine class="w-4 h-4" />
          </button>
        </div>

        <!-- Download button -->
        <a :href="pdfUrl" download class="p-2 hover:bg-gray-100 rounded-full">
          <RiDownloadLine class="w-4 h-4" />
        </a>
      </div>
    </div>

    <!-- PDF Viewer -->
    <div class="relative bg-gray-50 p-4">
      <div
        class="overflow-auto max-h-[800px] flex justify-center"
        :style="{
          transform: `scale(${zoom})`,
          transformOrigin: 'top center',
        }"
      >
        <VuePdfEmbed :source="pdfUrl" :scale="3" class="card-shadow border border-gray-200" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import VuePdfEmbed from 'vue-pdf-embed'
import {
  RiArrowLeftSLine,
  RiArrowRightSLine,
  RiAddLine,
  RiSubtractLine,
  RiDownloadLine,
} from '@remixicon/vue'

import Typography from '@/Stencil/Typography.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Document',
  },
  pdfUrl: {
    type: String,
    required: true,
  },
})

const currentPage = ref(1)
const pageCount = ref(1)
const loading = ref(true)
const zoom = ref(1.9)

const onLoad = (pdf) => {
  console.log(pdf)
  loading.value = false
  pageCount.value = pdf.pagesCount
}
</script>
