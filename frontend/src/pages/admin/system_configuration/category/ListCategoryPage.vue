<script setup>
import Card from '@/components/Card.vue';
import { onBeforeMount, onMounted, ref } from 'vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import { useCategoryStore } from '@/stores/category';
import { RouterView } from 'vue-router';

const categoryStore = useCategoryStore();

onBeforeMount(async() => {
    await categoryStore.getAllCategories();
})


</script>

<template>
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
            <div class="w-full mt-5 relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Category name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="category in categoryStore.categories.data" :key="category.id" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ category.name }}
                            </th>
                            <td class="px-6 py-4">
                                <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex justify-center md:justify-end mt-2">
                    <TailwindPagination
                    :data="categoryStore.categories"
                    @pagination-change-page="categoryStore.getAllCategories"
                />
                </div>
            </div>
        </div>
    </Card>
</template>