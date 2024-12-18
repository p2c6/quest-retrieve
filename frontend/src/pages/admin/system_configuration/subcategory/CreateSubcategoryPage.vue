<script setup>
import Card from '@/components/Card.vue';
import { useCategoryStore } from '@/stores/category';
import { useSubcategoryStore } from '@/stores/subcategory';
import { onBeforeMount, reactive } from 'vue';
import { RouterLink } from 'vue-router';

const subcategoryStore = useSubcategoryStore();
const categoryStore = useCategoryStore();

const formData = reactive({
    category_id: '',
    name: ''
});

onBeforeMount(async() => {
    await categoryStore.getAllCategoriesDropdown();
})


</script>

<template>
        <Card class="p-5 flex flex-row mt-2">
            <div class="overflow-x-auto w-full">
                <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="md:text-left text-primary font-medium">Create Subcategory</p>
                        <p class="text-tertiary md:text-left text-xs md:text-sm">You are about to create subcategory.</p>
                    </div>
                    <div class="w-full md:w-6">
                        <RouterLink :to="{name: 'category.list'}">
                            <div class="bg-slate-200 rounded text-center">
                                <i class="text-primary pi pi-chevron-left cursor-pointer text-xs"></i>
                            </div>
                        </RouterLink>
                    </div>
                </div>
                <div class="mt-5">
                    <form @submit.prevent="subcategoryStore.storeSubcategory(formData)">
                        <div>
                            <label class="text-primary text-sm font-medium">Category</label>
                            <select v-model="formData.category_id"  class="h-10 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <option disabled value="">Please select category</option>
                                <option v-for="category in categoryStore.categoriesDropdown" :value="category.id">{{  category.name }}</option>
                            </select>
                            <p v-if="subcategoryStore.errors && subcategoryStore.errors.category_id" class="text-red-500 text-xs">{{ subcategoryStore.errors.category_id[0] }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm font-medium">Subcategory Name</label>
                            <input type="text" v-model="formData.name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                            <p v-if="subcategoryStore.errors && subcategoryStore.errors.name" class="text-red-500 text-xs">{{ subcategoryStore.errors.name[0] }}</p>
                        </div>
                        <button class="bg-secondary rounded-lg px-6 py-1 text-white mt-2 text-sm w-full md:w-20">Save</button>

                    </form>
                </div>
            </div>
        </Card>
</template>