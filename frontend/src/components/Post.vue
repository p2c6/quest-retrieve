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
                    <div 
                        class="bg-secondary px-2 py-1 rounded text-white text-sm"
                       
                    >
                        Contact
                    </div>
                    <!-- <div class="flex flex-row gap-1 text-tertiary">
                        <i class="pi pi-pen-to-square"></i>
                        <i class="pi pi-times-circle"></i>
                        <i class="pi pi-bookmark text-green-500"></i>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</template>