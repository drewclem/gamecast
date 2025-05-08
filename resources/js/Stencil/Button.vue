<template>
  <a
    v-if="isExternal"
    :href="href"
    class="block btn"
    :class="`btn-${theme} btn-${size} group ${isDisabled ? 'opacity-50 pointer-events-none' : ''}`"
    :disabled="isDisabled"
  >
    <span class="flex justify-center items-center space-x-2 pointer-events-none">
      <Icon
        v-if="icon"
        :icon="icon"
        :size="size === 'default' ? 'xsmall' : 'xxsmall'"
        class="opacity-60 group-hover:opacity-100"
      />
      <Typography>
        <slot />
      </Typography>
    </span>
  </a>
  <component
    v-else-if="!href"
    :is="as"
    :type="buttonType"
    class="btn"
    :class="`btn-${theme} btn-${size} group ${isDisabled ? 'opacity-50 pointer-events-none' : ''}`"
    :disabled="isDisabled"
  >
    <span class="flex justify-center items-center space-x-2 pointer-events-none">
      <Spinner v-if="loading" class="w-4 h-4 mr-1" />
      <Icon
        v-if="icon"
        :icon="icon"
        :size="size === 'default' ? 'xsmall' : 'xxsmall'"
        class="opacity-60 group-hover:opacity-100"
      />
      <Typography>
        <slot />
      </Typography>
    </span>
  </component>

  <Link
    v-else
    :href="href"
    class="block btn"
    :class="`btn-${theme} btn-${size}  ${isDisabled ? 'opacity-50 pointer-events-none' : ''}`"
    :disabled="isDisabled"
  >
    <span class="flex justify-center items-center space-x-2 pointer-events-none">
      <Icon
        v-if="icon"
        :icon="icon"
        :size="size === 'default' ? 'xsmall' : 'xxsmall'"
        class="opacity-60"
      />
      <Typography>
        <slot />
      </Typography>
    </span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

import Icon from '@/Stencil/Icon.vue'
import Typography from '@/Stencil/Typography.vue'
import Spinner from '@/Stencil/Spinner.vue'

defineProps({
  as: {
    type: String,
    default: 'button',
  },
  buttonType: {
    type: String,
    default: 'button',
  },
  theme: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'subdued', 'danger'].includes(value),
  },
  size: {
    type: String,
    default: 'default',
  },
  icon: {
    type: String,
    default: null,
  },
  href: {
    type: String,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  isExternal: {
    type: Boolean,
    default: false,
  },
  isDisabled: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped lang="postcss">
.btn {
  @apply text-center font-semibold rounded transform hover:scale-105 border-2 border-transparent;
}

.btn-default {
  @apply px-2 py-1;
}

.btn-square {
  @apply p-1;
}

.btn-small {
  @apply px-2 py-0.5 text-sm;
}

.btn-primary {
  @apply bg-yellow-500;
  background-image: linear-gradient(to bottom right, rgb(248, 222, 146), rgb(234 179 8));
}

.btn-primary:hover {
  @apply bg-yellow-500;
}

.btn-primary-outline {
  @apply border-yellow-500;
}

.btn-primary-outline:hover {
  @apply border-yellow-500;
}

.btn-secondary {
  @apply bg-black text-yellow-400;
  background-image: linear-gradient(to bottom right, #474747, #2b2b2b);
}

.btn-secondary:hover {
  @apply bg-gray-800;
}

.btn-tertiary {
  @apply bg-yellow-500;
}

.btn-subdued {
  @apply border-gray-300  hover:border-gray-400;
}

.btn-tertiary:hover {
  @apply bg-yellow-500;
}

.btn-danger {
  @apply bg-red-500 text-white;
  background-image: linear-gradient(to bottom right, #ff4747, #c64141);
}

.btn-danger:hover {
  @apply bg-red-600;
}

.btn-danger-outline {
  @apply border-red-500;
}

.btn-danger-outline:hover {
  @apply border-red-500;
}

.btn:disabled,
a:disabled {
  @apply opacity-50 pointer-events-none;
}

@screen lg {
  .btn-default {
    @apply px-4 py-1.5;
  }

  .btn-square {
    @apply p-2;
  }
}
</style>
