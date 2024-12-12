<script setup>
import Card from '@/components/Card.vue';
import Table from '@/components/Table.vue';
import { useCategoryStore } from '@/stores/category';
import { reactive } from 'vue';
import { RouterLink } from 'vue-router';

const categoryStore = useCategoryStore();

const formData = reactive({
    name: ''
});


</script>

<template>
        <Card class="p-5 flex flex-row mt-2">
            <div class="overflow-x-auto w-full">
                <div class="flex justify-around">
                    <div class="w-full">
                        <p class="md:text-left text-primary font-medium">Create Category</p>
                        <p class="text-tertiary md:text-left text-xs md:text-sm">You are about to create category.</p>
                    </div>
                    <div>
                        <RouterLink :to="{name: 'category.list'}">
                            <button class="bg-secondary text-white px-2 py-1 rounded text-sm">Back</button>
                        </RouterLink>
                    </div>
                </div>
                <div class="mt-5">
                    <form @submit.prevent="categoryStore.storeCategory(formData)">
                        <div>
                            <label class="text-primary text-sm font-medium">Category Name</label>
                            <input type="text" v-model="formData.name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                            <p v-if="categoryStore.errors && categoryStore.errors.name" class="text-red-500 text-xs">{{ categoryStore.errors.name[0] }}</p>
                        </div>
                        <button class="bg-secondary rounded-lg px-6 py-1 text-white mt-5 text-sm">Save</button>

                    </form>
                </div>
            </div>
        </Card>
</template>