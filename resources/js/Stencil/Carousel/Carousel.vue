<template>
  <div class="swiper relative bg-background">
    <div ref="swiperRef" class="swiper-container">
      <ul
        class="swiper-wrapper"
        :class="{ 'aspect-[3/2]': aspectRatio === '3/2', 'aspect-[16/9]': aspectRatio === '16/9' }"
      >
        <slot />
      </ul>
    </div>

    <div v-if="hasNavigation" class="swiper-navigation hidden lg:block">
      <div ref="prevButtonRef" class="swiper-button-prev">
        <span class="sr-only">Previous</span>
        <div class="bg-black bg-opacity-40 p-1 rounded-full border border-white">
          <Icon name="chevron-left" size="small" color="white" />
        </div>
      </div>
      <div ref="nextButtonRef" class="swiper-button-next">
        <span class="sr-only">Next</span>
        <div class="bg-black bg-opacity-40 p-1 rounded-full border border-white">
          <Icon name="chevron-right" size="small" color="white" />
        </div>
      </div>
    </div>

    <div class="relative z-10 flex justify-center" :class="paginationStyles" v-if="hasPagination">
      <div ref="paginationRef" class="swiper-pagination" @click.stop />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUpdated, onBeforeUnmount } from 'vue'
import Swiper from 'swiper'
import { Navigation, Pagination, A11y, Autoplay } from 'swiper/modules'
import Icon from '@/Stencil/Icon.vue'
const emits = defineEmits(['update:activeIndex'])

const props = defineProps({
  activeIndex: {
    type: Number,
    default: 0,
  },
  mobileSlidesPerView: {
    type: Number,
    default: 1,
  },
  slidesPerView: {
    type: Number,
    default: 1,
  },
  hasNavigation: {
    type: Boolean,
    default: false,
  },
  hasPagination: {
    type: Boolean,
    default: false,
  },
  loop: {
    type: Boolean,
    default: false,
  },
  paginationClickable: {
    type: Boolean,
    default: true,
  },
  paginationStyles: {
    type: String,
    default: '',
  },
  aspectRatio: {
    type: String,
    default: null,
  },
})

const swiper = ref(null)
const swiperRef = ref(null)
const prevButtonRef = ref(null)
const nextButtonRef = ref(null)
const paginationRef = ref(null)
const innerActiveIndex = ref(null)

onMounted(() => {
  innerActiveIndex.value = props.activeIndex

  swiper.value = new Swiper(swiperRef.value, {
    modules: [Navigation, Pagination, A11y, Autoplay],
    initialSlide: props.activeIndex,
    slidesPerView: props.slidesPerView,
    watchOverflow: true,
    centeredSlides: true,
    loop: props.loop,
    navigation: {
      prevEl: prevButtonRef.value,
      nextEl: nextButtonRef.value,
    },
    pagination: {
      el: paginationRef.value,
      clickable: props.paginationClickable,
    },
  })

  swiper.value.on('slideChange', () => {
    innerActiveIndex.value = swiper.value.activeIndex

    emits('update:activeIndex', innerActiveIndex.value)
  })
})

onUpdated(() => {
  swiper.value.update()
})

onBeforeUnmount(() => {
  swiper.value.destroy()
})
</script>

<style>
@import 'swiper/swiper-bundle.css';

:root {
  --swiper-pagination-bullet-height: 6px;
  --swiper-pagination-bullet-width: 6px;
  --swiper-pagination-color: #44c08e;
}

.swiper-button-prev::after,
.swiper-button-next::after {
  content: '';
}

.swiper {
  z-index: 0;
}

.swiper-pagination-bullet {
  box-shadow: 0 4 2 -1 rgba(0, 0, 0, 0.06), 0 4 6 -1 rgba(0, 0, 0, 0.1);
}
</style>
