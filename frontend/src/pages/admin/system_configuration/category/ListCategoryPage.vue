<script setup>
import Card from '@/components/Card.vue';
import { onBeforeMount, onBeforeUnmount, reactive, ref } from 'vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import { useCategoryStore } from '@/stores/category';

const categoryStore = useCategoryStore();

const isModalOpen = ref(false);
const categoryId = ref(null);

const formData = reactive({
    'keyword': ''
})

let typingTimer;
const typingDelay = 1000;

const openDeleteCategoryConfirmation = (id) => {
    isModalOpen.value = true;
    categoryId.value = id;
}

const confirmDeleteCategory = (id) => {
    categoryStore.deleteCategory(id)
    isModalOpen.value = false;
}

const closeModal = () => {
    isModalOpen.value = false;
}

const search = async() => {
    categoryStore.keyword = formData.keyword;
    clearTimeout(typingTimer);
    typingTimer = setTimeout(async() => {
        await categoryStore.getAllCategories();
    }, typingDelay)
}

onBeforeMount(async() => {
    await categoryStore.getAllCategories();
})

onBeforeUnmount(() => {
    categoryStore.message = null;
    categoryStore.keyword = null;
})


</script>

<template>
    <Teleport to="#modal-container">
        <div :class="`fixed inset-0 z-20 flex items-center justify-center ${isModalOpen ? 'block' : 'hidden'}`">
            <div class="absolute inset-0 bg-slate-950 opacity-40" @click="closeModal"></div>
            <div id="modal-card" class="relative w-80 h-40 bg-white z-20 rounded-2xl">
                <div id="modal-header" class="w-full py-2">
                    <div class="flex justify-end mr-5">
                        <i class="pi pi-times text-gray-500 cursor-pointer" @click="closeModal"></i>
                    </div>

                </div>
                <div id="modal-body" class="flex items-center justify-center px-4">
                    <div class="flex-1">
                        <div class="flex flex-col justify-center gap-2">
                            <p class="font-semibold text-md text-primary">Delete category?</p>
                            <p class="text-xs text-gray-400">You are about to delete this category</p>
                            <div class="flex gap-1 mt-2">
                                <button class="w-36 h-8 bg-gray-200 rounded text-sm" @click="closeModal">Cancel</button>
                                <button class="w-36 h-8 bg-secondary rounded text-white text-sm" @click="confirmDeleteCategory(categoryId)">Yes,delete it</button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </Teleport>
    <Card class="p-5 flex flex-row mt-2  w-full">
        <div class="overflow-x-auto w-full">
            <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                <div class="text-center md:text-left">
                    <p class="text-primary font-medium">Category List</p>
                    <p class="text-tertiary text-xs md:text-sm">Listing of all categories.</p>
                </div>
                <div class="w-full md:w-16">
                    <RouterLink :to="{ name: 'category.create' }">
                    <button class="bg-secondary text-white px-2 py-1 rounded text-sm w-full">Create</button>
                    </RouterLink>
                </div>
            </div>
            <div class="flex justify-center relative mt-2 md:justify-end">
                <label class="relative block w-full md:w-48">
                    <span class="sr-only">Search</span>
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <i class="text-gray-400 pi pi-search cursor-pointer"> </i>

                    </span>
                    <input @input="search" v-model="formData.keyword" class="placeholder:italic placeholder:text-slate-400 block bg-white w-full border border-slate-300 rounded-md py-1 pl-9 pr-3 shadow-sm sm:text-sm md:py-2" placeholder="Search for anything..." type="text" name="search"/>
                </label>
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
                    <div role="status" v-if="categoryStore.isLoading" class="flex items-center justify-center mt-10">
                        <div class="flex flex-col items-center justify-center gap-5">
                            <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                            </svg>
                        </div>
                    </div>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" v-else>
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
                            <tr v-if="categoryStore.categories.data.length > 0" v-for="category in categoryStore.categories.data" :key="category.id" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ category.name }}
                                </th>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <RouterLink :to="{name: 'category.edit', params:{ id: category.id } }" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                            <i class="text-primary pi pi-pen-to-square cursor-pointer"> Edit</i>
                                        </RouterLink>
                                        <div class="text-red-500 cursor-pointer" @click="openDeleteCategoryConfirmation(category.id)">
                                            <i class="text-red-500 pi pi-trash text-gray-500 cursor-pointer"> </i> Delete
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="2" class="text-center">No data found.</td>
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