<script setup>
import Card from '@/components/Card.vue';
import Table from '@/components/Table.vue';


</script>

<template>
    <div class="container grid grid-cols-1">
        <div class="p-2">
            <Card class="p-5 flex flex-row mt-2">
                <div class="overflow-x-auto w-full">
                    <div class="flex justify-around">
                        <div class="w-full">
                            <p class="md:text-left text-primary font-medium">Category List</p>
                            <p class="text-tertiary md:text-left text-xs md:text-sm">Listing of all categories.</p>
                        </div>
                        <div>
                            <button class="bg-secondary text-white px-2 py-1 rounded text-sm">Create</button>
                        </div>
                    </div>
                        <Table />
                </div>
            </Card>
        </div>
    </div>
</template>