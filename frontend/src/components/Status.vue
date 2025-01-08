<script setup>
defineProps({
    type: String
})
</script>

<template>
    <div class="rounded w-6 h-2 place-self-end mb-3" :class="`${type == 'Lost' ? 'bg-red-500' : 'bg-green-500'}`"></div>
</template>