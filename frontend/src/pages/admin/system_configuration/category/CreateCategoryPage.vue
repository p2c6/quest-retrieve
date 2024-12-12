<script setup>
import Card from '@/components/Card.vue';
import Table from '@/components/Table.vue';
import { reactive } from 'vue';

const formData = reactive({
    name: ''
});


</script>

<template>
    <div class="container grid grid-cols-1">
        <div class="p-2">
            <Card class="p-5 flex flex-row mt-2">
                <div class="overflow-x-auto w-full">
                    <div class="flex justify-around">
                        <div class="w-full">
                            <p class="md:text-left text-primary font-medium">Create Category</p>
                            <p class="text-tertiary md:text-left text-xs md:text-sm">Listing of all categories.</p>
                        </div>
                        <div>
                            <button class="bg-secondary text-white px-2 py-1 rounded text-sm">Back to categories</button>
                        </div>
                    </div>
                    <div>
                        <form @submit.prevent="categoryStore.store(formData)">
                            <div>
                                <label class="text-primary text-sm font-medium">Category Name</label>
                                <input type="text" v-model="formData.name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <p v-if="categoryStore.errors && categoryStore.errors.name" class="text-red-500 text-xs">{{ categoryStore.errors.name[0] }}</p>
                            </div>
                        </form>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>