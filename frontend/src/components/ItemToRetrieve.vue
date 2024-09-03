<script setup>
import Status from '@/components/Status.vue';
import { RouterLink } from 'vue-router';


</script>

<template>
    <div class="p-3">
        <div class="grid grid-cols-2 gap-38 place-content-between">
            <h1 class="font-bold text-primary place-self-start text-md md:text-lg">Iphone XR</h1>
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
                <div class="mt-5">
                    <RouterLink 
                        class="bg-secondary px-4 py-1 rounded text-white text-sm"
                        :to="{name: 'contact', params: {id: 1}}"
                    >
                        Contact
                    </RouterLink>
                </div>
            </div>
        </div>
    </div>
</template>