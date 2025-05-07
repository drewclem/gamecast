<template>
    <component :is="as" class="card" :class="paddingClasses">
        <slot />
    </component>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    padding: {
        type: String,
        default: "default",
        validator: (value) =>
            [
                "none",
                "xxsmall",
                "xsmall",
                "default",
                "medium",
                "large",
                "xlarge",
                "xxlarge",
                "xxxlarge",
            ].includes(value),
    },
    as: {
        type: String,
        default: "div",
    },
});

const paddingClasses = computed(() => {
    switch (props.padding) {
        case "none":
            return "p-0";
        case "xxsmall":
            return "p-1 lg:p-2";
        case "xsmall":
            return "p-2 lg:p-4";
        case "default":
            return "p-3 lg:p-6";
        case "medium":
            return "p-4 lg: p-8";
        case "large":
            return "p-6 lg:p-10";
        case "xlarge":
            return "p-8 lg:p-12";
        case "xxlarge":
            return "p-10 lg:p-14";
        case "xxxlarge":
            return "p-12 lg:p-16";
    }
});
</script>

<style lang="postcss" scoped>
.card {
    @apply bg-white rounded-md overflow-hidden relative border border-gray-200;

    /* Layered shadows for depth */
    box-shadow:
        /* Inner highlights */ inset 0 1px 1px
            rgba(255, 255, 255, 1),
        /* Main elevation shadow */ 0 1px 1px rgba(17, 39, 31, 0.01),
        0 2px 2px rgba(17, 39, 31, 0.01), 0 4px 4px rgba(17, 39, 31, 0.01),
        0 8px 8px rgba(17, 39, 31, 0.01),
        /* Subtle ambient shadow */ 0 16px 16px rgba(17, 39, 31, 0.01);

    /* Subtle gradient overlay for depth */
    background: linear-gradient(
        180deg,
        rgb(255, 255, 255) 0%,
        rgb(252, 252, 252) 100%
    );

    /* Top edge highlight */
    &::before {
        content: "";
        @apply absolute left-0 right-0 h-[1px] top-0;
        background: linear-gradient(
            90deg,
            transparent 0%,
            rgba(255, 255, 255, 0.8) 50%,
            transparent 100%
        );
    }
}
</style>
