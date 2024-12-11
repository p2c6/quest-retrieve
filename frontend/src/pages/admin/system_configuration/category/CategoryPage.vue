<script setup>
import Card from '@/components/Card.vue';
import Table from '@/components/Table.vue';


</script>

<template>
    <div class="container grid grid-cols-1">
        <div class="p-2">
            <div>
            </div>
            <Card class="p-5 flex flex-row mt-2">
                <div class="overflow-x-auto w-full">
                    <p class="text-center md:text-left text-primary font-medium">Category List</p>
                    <p class="text-tertiary text-center md:text-left text-xs md:text-sm">Listing of all categories.</p>
                    <Table />
                </div>
            </Card>
        </div>
    </div>
</template>