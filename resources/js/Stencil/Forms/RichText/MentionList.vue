<template>
  <div class="dropdown-menu">
    <template v-if="items && items.length">
      <button
        :class="{ 'is-selected': index === selectedIndex }"
        v-for="(item, index) in items"
        :key="index"
        @click="selectItem(index)"
      >
        {{ item.name }}
      </button>
    </template>
    <div class="item" v-else>
      <Typography>No results</Typography>
    </div>
  </div>
</template>

<script>
import Typography from '@/Stencil/Typography.vue'
export default {
  components: { Typography },
  props: {
    items: {
      type: Array,
      required: true,
    },

    command: {
      type: Function,
      required: true,
    },
  },

  data() {
    return {
      selectedIndex: 0,
    }
  },

  watch: {
    items() {
      this.selectedIndex = 0
    },
  },

  methods: {
    onKeyDown({ event }) {
      if (event.key === 'ArrowUp') {
        this.upHandler()
        return true
      }

      if (event.key === 'ArrowDown') {
        this.downHandler()
        return true
      }

      if (event.key === 'Enter') {
        this.enterHandler()
        return true
      }

      return false
    },

    upHandler() {
      this.selectedIndex = (this.selectedIndex + this.items.length - 1) % this.items.length
    },

    downHandler() {
      this.selectedIndex = (this.selectedIndex + 1) % this.items.length
    },

    enterHandler() {
      this.selectItem(this.selectedIndex)
    },

    selectItem(index) {
      const item = this.items[index]

      if (item) {
        this.command({ id: item })
      }
    },
  },
}
</script>

<style scoped>
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

/* Dropdown menu */
.dropdown-menu {
  @apply bg-white shadow-md rounded-md flex flex-col p-2 gap-1 w-full;
}

.dropdown-menu button {
  @apply items-center bg-transparent flex gap-1 text-left w-full text-sm p-1;
}

.dropdown-menu button:hover,
.dropdown-menu button:hover.is-selected {
  @apply bg-gray-100;
}

.dropdown-menu button.is-selected {
  @apply bg-gray-100;
}
</style>
