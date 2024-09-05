<script setup>

defineProps({
    bg: {
        type: String,
        default: 'bg-white'
    }
});
</script>

<template>
    <div :class="`w-full ${bg} h-auto rounded-md`">
        <slot></slot>
    </div>
</template>