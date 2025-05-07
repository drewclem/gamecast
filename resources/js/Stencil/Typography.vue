<template>
  <component
    :is="as"
    :class="{
      'text-3xl lg:text-4xl leading-tight font-bold': variant === 'billboard',
      'text-xl lg:text-2xl leading-tight font-medium': variant === 'h1',
      'text-lg lg:text-xl leading-6 font-medium': variant === 'h2',
      'text-base lg:text-lg leading-6 font-medium': variant === 'h3',
      'text-sm lg:text-base font-medium': variant === 'h4',
      'text-xs lg:text-sm font-medium': variant === 'h5',
      'text-xs font-medium': variant === 'h6',
      'text-xs': variant === 'body-small',
      'text-sm': variant === 'body',
      'text-base ': variant === 'body-large',
      'text-xs font-medium': variant === 'label-small',
      'text-sm font-medium': variant === 'label',
      'text-base font-medium': variant === 'label-large',
    }"
  >
    <slot />
  </component>
</template>

<script setup>
defineProps({
  as: {
    type: String,
    default: 'p',
    validator: (value) => {
      return ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'p', 'label'].includes(value)
    },
  },
  variant: {
    type: String,
    default: 'body',
    validator: (value) => {
      return [
        'billboard',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'body-small',
        'body',
        'body-large',
        'label-small',
        'label',
        'label-large',
        'button',
      ].includes(value)
    },
  },
})
</script>

<style lang="postcss" scoped>
h1,
h2,
h3,
h4,
h5,
h6 {
  @apply tracking-tight font-bold;
}
</style>
