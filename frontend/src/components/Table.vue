<script setup>
import { onMounted, ref } from 'vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import { apiClient } from '@/config/http';

const categoriesData = ref({});

const getCategories = async (page = 1) => {
    const response = await apiClient.get(`/categories?page=${page}`)

    categoriesData.value = response.data
}

onMounted(() => {
    getCategories();
})

</script>

<template>
    

<div class="w-full mt-2 relative overflow-x-auto shadow-md sm:rounded-lg">
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
            <tr v-for="categories in categoriesData.data" :key="categories.id" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ categories.name }}
                </th>
                <td class="px-6 py-4">
                    <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="flex justify-center md:justify-end mt-2">
        <TailwindPagination
        :data="categoriesData"
        @pagination-change-page="getCategories"
    />
    </div>
</div>
</template>