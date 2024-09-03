<script setup>
import emptyIllustration from "@/assets/empty_item.png";
</script>

<template>
    <main class="grid grid-1 justify-items-center">
        <div class="mt-5 flex flex-col items-center">
            <img :src="emptyIllustration" alt="No Items Image" class="md:w-120 md:h-120" />
            <p class="text-tertiary">Ooops. There's no item found. Please try again...</p>
        </div>
    </main>
</template>