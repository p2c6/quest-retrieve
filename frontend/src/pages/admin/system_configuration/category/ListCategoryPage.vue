<script setup>
import Card from '@/components/Card.vue';
import { onBeforeMount, onBeforeUnmount, onMounted, ref } from 'vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import { useCategoryStore } from '@/stores/category';
import { RouterView } from 'vue-router';

const categoryStore = useCategoryStore();

onBeforeMount(async() => {
    await categoryStore.getAllCategories();
})

onBeforeUnmount(() => {
    categoryStore.message = null;
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
                    <RouterLink :to="{name: 'category.create'}">
                        <button class="bg-secondary text-white px-2 py-1 rounded text-sm">Create</button>
                    </RouterLink>
                </div>
            </div>
            <div class="w-full mt-5 relative overflow-x-auto shadow-md sm:rounded-lg">
                <div v-if="categoryStore.message" class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md mb-5" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <svg class="w-6 h-6 text-teal-500 dark:text-white mr-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                            <p class="font-bold text-sm">Success</p>
                            <div class="text-xs flex gap-1">
                                {{ categoryStore.message }}
                            </div>
                            </div>
                        </div>
                    </div>
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