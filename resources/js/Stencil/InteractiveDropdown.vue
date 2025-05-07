<template>
  <div
    ref="root"
    class="dropdown flex items-center w-full"
    :class="{ 'opacity-50 pointer-events-none': isDisabled }"
    @click="handleClick"
  >
    <button
      type="button"
      :aria-describedby="`dropdown-${id}`"
      class="w-full"
      :disabled="isDisabled"
    >
      <slot />
    </button>

    <div ref="content" :id="`dropdown-${id}`">
      <slot name="content" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, provide, watch, useSlots } from 'vue'
import tippy from 'tippy.js'
import { v4 as uuidv4 } from 'uuid'

const props = defineProps({
  placement: {
    type: String,
    default: 'bottom-start',
  },
  isContentInteractive: {
    type: Boolean,
    default: true,
  },
  isDisabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['opening'])

const id = uuidv4()
const root = ref(null)
const content = ref(null)
const dropdown = ref(undefined)

const slots = useSlots()

const createDropdown = () => {
  return tippy(root.value, {
    content: content.value,
    animation: 'shift-away-subtle',
    arrow: false,
    placement: props.placement,
    interactive: true,
    trigger: 'click',
    theme: 'dropdown',
    appendTo: () => document.body,
    delay: [0, 200],
    onShow: () => {
      emit('opening')
    },
  })
}

watch(
  () => [slots.content],
  () => {
    dropdown.value.destroy()
    dropdown.value = createDropdown()
  },
  { deep: true }
)

onMounted(() => {
  dropdown.value = createDropdown()
})

defineExpose({
  dropdown,
  close,
})

function close() {
  if (dropdown.value) {
    dropdown.value.hide()
  }
}

provide('dropdown', {
  instance: dropdown,
  close,
})

function handleClick(e) {
  if (props.isContentInteractive) {
    e.stopPropagation()
  }
  emit('opening')
}
</script>

<style>
@import 'tippy.js/dist/tippy.css';
@import 'tippy.js/dist/border.css';
@import 'tippy.js/animations/shift-away-subtle.css';

.tippy-box[data-theme~='dropdown'] {
  box-shadow:
    0 10px 15px -3px rgb(0 0 0 / 0.1),
    0 4px 6px -4px rgb(0 0 0 / 0.1);
}

.tippy-box[data-theme~='dropdown'] .tippy-content {
  @apply text-black rounded;
  background-color: #fff;
  padding: 0;
}
</style>
