<script setup>
import Card from '@/components/Card.vue';
import Table from '@/components/Table.vue';
import { useCategoryStore } from '@/stores/category';
import { onBeforeMount, onMounted, onUnmounted, reactive, ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import * as yup from 'yup';

const categoryStore = useCategoryStore();
const route = useRoute();

const category = ref({});
const schema = yup.object({
    name: yup.string().required('Name is required.')
});

const formData = reactive({
    id: '',
    name: ''
});

const id = route.params?.id;


const yupErrors = reactive({});

const updateCategory = async(formData) => {
    yupErrors.name = '';
    categoryStore.errors = null;

    try {
        await schema.validate(formData, {abortEarly: false })
        categoryStore.updateCategory(formData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                yupErrors[err.path] = err.message;
            });
        }
    }
}

onMounted(async() => {
    if (id) {
        category.value = await categoryStore.getCategory(id);

        formData.id = id;
        formData.name = category.value.name;
    }
})

onUnmounted(() => {
    category.value = {};
})


</script>

<template>
        <Card class="p-5 flex flex-row mt-2">
            <div class="overflow-x-auto w-full">
                <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="md:text-left text-primary font-medium">Edit Category</p>
                        <p class="text-tertiary md:text-left text-xs md:text-sm">You are about to update category.</p>
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
                    <form @submit.prevent="updateCategory(formData)">
                        <div>
                            <label class="text-primary text-sm font-medium">Category Name</label>
                            <input type="text" v-model="formData.name" :class="`h-8 w-full border-[1.1px] border-${yupErrors.name || (categoryStore.errors && categoryStore.errors.name) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                            <p v-if="yupErrors.name" class="text-red-500 text-xs">{{ yupErrors.name }}</p>
                            <p v-else-if="categoryStore.errors && categoryStore.errors.name" class="text-red-500 text-xs">
                            {{ categoryStore.errors.name[0] }}
                            </p>
                        </div>
                        <button class="bg-secondary rounded-lg px-6 py-1 text-white text-sm w-full mt-2 md:w-24">Update</button>

                    </form>
                </div>
            </div>
        </Card>
</template>