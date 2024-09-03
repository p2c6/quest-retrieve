<script setup>
import Status from '@/components/Status.vue';


</script>

<template>
    <div class="p-3">
        <div class="grid grid-cols-2 gap-38 place-content-between">
            <h1 class="font-bold text-primary place-self-start text-md">Iphone SE</h1>
            <Status />
            <div class="mt-4">
                <div class="flex flex-row items-center gap-1">
                    <i class="text-indigo-800 pi pi-map-marker"></i>
                    <p class="text-xs">Quezon City</p>
                </div>
            </div>
        </div>
    </div>
</template>