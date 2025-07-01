<script setup>
import Card from '@/components/Card.vue';
import { useCategoryStore } from '@/stores/category';
import { reactive } from 'vue';
import { RouterLink } from 'vue-router';
import * as yup from 'yup';

let schema = yup.object({
    name: yup.string().required('category name is required.')
})

const categoryStore = useCategoryStore();

const formData = reactive({
    name: ''
});

const yupErrors = reactive({});

const storeCategory = async(formData) => {
    yupErrors.name = '';
    categoryStore.errors = null;

    try {
        await schema.validate(formData, {abortEarly: false })
        categoryStore.storeCategory(formData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                yupErrors[err.path] = err.message;
            });
        }
    }
}
</script>

<template>
        <Card class="p-5 flex flex-row mt-2">
            <div class="overflow-x-auto w-full">
                <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="md:text-left text-primary font-medium">Create Category</p>
                        <p class="text-tertiary md:text-left text-xs md:text-sm">You are about to create category.</p>
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
                    <form @submit.prevent="storeCategory(formData)">
                        <div>
                            <label class="text-primary text-sm font-medium">Category Name</label>
                            <input type="text" v-model="formData.name" :class="`h-8 w-full border-[1.1px] border-${yupErrors.name || (categoryStore.errors && categoryStore.errors.name) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                            <p v-if="yupErrors.name" class="text-red-500 text-xs">{{ yupErrors.name }}</p>
                            <p v-else-if="categoryStore.errors && categoryStore.errors.name" class="text-red-500 text-xs">
                            {{ categoryStore.errors.name[0] }}
                            </p>
                        </div>
                        <button class="bg-secondary rounded-lg px-6 py-1 text-white mt-2 text-sm w-full md:w-20">Save</button>

                    </form>
                </div>
            </div>
        </Card>
</template>