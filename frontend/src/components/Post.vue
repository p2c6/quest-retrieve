<script setup>
import Status from '@/components/Status.vue';

defineProps({
    post: Object
});

</script>

<template>
    <div class="p-3">
        <div class="grid grid-cols-2 gap-38 place-content-between">
            <h1 class="font-bold text-primary place-self-start text-md md:text-lg">{{ post.subcategory.name }}</h1>
            <Status :type="post.type" />
            <div class="mt-4 w-60">
                <div class="flex flex-row items-center gap-1 text-tertiary text-sm md:text-md">
                    <i class="pi pi-map-marker"></i>
                    <p>{{ post.incident_location }}</p>
                </div>
                <div class="flex flex-row items-center gap-1 text-tertiary text-sm md:text-md">
                    <i class="pi pi-calendar"></i>
                    <p>{{ post.incident_date }}</p>
                </div>
                <div class="mt-5">
                    <div class="bg-secondary px-2 py-1 rounded text-white text-sm w-16">
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