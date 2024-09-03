<script setup>
import Status from '@/components/Status.vue';


</script>

<template>
    <div class="p-3">
        <div class="grid grid-cols-2 gap-38 place-content-between">
            <h1 class="font-bold text-primary place-self-start text-md md:text-lg">Iphone SE</h1>
            <Status />
            <div class="mt-4">
                <div class="flex flex-row items-center gap-1 text-tertiary text-sm md:text-md">
                    <i class="pi pi-map-marker"></i>
                    <p>Quezon City</p>
                </div>
                <div class="flex flex-row items-center gap-1 text-tertiary text-sm md:text-md">
                    <i class="pi pi-calendar"></i>
                    <p>August 31, 2024</p>
                </div>
                <div>
                    <button class="bg-secondary mt-5 px-4 py-1 rounded text-white text-sm">Contact</button>
                </div>
            </div>
        </div>
    </div>
</template>