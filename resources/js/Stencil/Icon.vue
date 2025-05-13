<template>
  <component :is="remixIcon" :class="iconSizeClasses" />
</template>

<script setup>
import { computed } from 'vue'

import {
  RiAddLine,
  RiLoaderLine,
  RiUploadLine,
  RiInformationLine,
  RiCheckboxCircleLine,
  RiErrorWarningLine,
  RiAlarmWarningLine,
  RiArrowDownSLine,
  RiCheckLine,
  RiMailLine,
  RiPhoneLine,
  RiCloseLine,
  RiPencilLine,
  RiDeleteBinLine,
  RiDraggable,
  RiSettings5Line,
  RiTeamLine,
  RiThumbDownLine,
  RiThumbUpLine,
  RiAlertLine,
  RiSearchLine,
  RiExternalLinkLine,
  RiUserLine,
  RiRefreshLine,
  RiMapPinLine,
} from '@remixicon/vue'

const props = defineProps({
  icon: {
    type: String,
    required: true,
  },
  color: {
    type: String,
    default: 'text-black',
  },
  size: {
    type: String,
    default: 'default',
    validator: (value) => {
      return [
        'xxsmall',
        'xsmall',
        'small',
        'default',
        'medium',
        'large',
        'xlarge',
        'xxlarge',
      ].includes(value)
    },
  },
})

const iconSizeClasses = computed(() => {
  switch (props.size) {
    case 'xxsmall':
      return 'h-4 w-4'
    case 'xsmall':
      return 'h-5 w-5'
    case 'small':
      return 'h-6 w-6'
    case 'default':
      return 'h-8 w-8'
    case 'medium':
      return 'h-10 w-10'
    case 'large':
      return 'h-12 w-12'
    case 'xlarge':
      return 'h-16 w-16'
    case 'xxlarge':
      return 'h-20 w-20'
    default:
      return 'h-[45px] w-[45px]'
  }
})

const remixIcon = computed(() => {
  switch (props.icon) {
    case 'check':
      return RiCheckLine
    case 'chevron-down':
      return RiArrowDownSLine
    case 'info':
      return RiInformationLine
    case 'success':
      return RiCheckboxCircleLine
    case 'user':
      return RiUserLine
    case 'warning':
      return RiAlarmWarningLine
    case 'error':
      return RiErrorWarningLine
    case 'danger':
      return RiAlertLine
    case 'loader':
      return RiLoaderLine
    case 'upload':
      return RiUploadLine
    case 'add':
      return RiAddLine
    case 'email':
      return RiMailLine
    case 'phone':
      return RiPhoneLine
    case 'edit':
      return RiPencilLine
    case 'delete':
      return RiDeleteBinLine
    case 'drag':
      return RiDraggable
    case 'x':
      return RiCloseLine
    case 'team':
      return RiTeamLine
    case 'settings':
      return RiSettings5Line
    case 'upvote':
      return RiThumbUpLine
    case 'downvote':
      return RiThumbDownLine
    case 'search':
      return RiSearchLine
    case 'external-link':
      return RiExternalLinkLine
    case 'refresh':
      return RiRefreshLine
    case 'map-pin':
      return RiMapPinLine
    default:
      return null
  }
})
</script>
