<script setup>
import Card from '@/components/Card.vue';
import { onBeforeMount, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { useSubcategoryStore } from '@/stores/subcategory';
import { usePostStore } from '@/stores/post';
import { useRoute } from 'vue-router';

import * as yup from 'yup';

let schema = yup.object({
    type: yup
        .string()
        .required('type is required.'),
    subcategory_id: yup
        .string()
        .required('item is required.'),
    incident_location: yup
        .string()
        .required('incident location is required.'),
    incident_date: yup
        .string()
        .required('date of birth is required.'),
})

const route = useRoute();
const post = ref({});

const subcategoryStore = useSubcategoryStore();
const postStore = usePostStore();

const formData = reactive({
    id: '',
    type: '',
    subcategory_id: '',
    incident_location: '',
    incident_date: '',
})

const id = route.params?.id;

const yupErrors = reactive({});

const updatePost = async(formData) => {
    yupErrors.type = '';
    yupErrors.incident_location = '';
    yupErrors.incident_date = '';
    yupErrors.subcategory_id = '';

    try {
        await schema.validate(formData, {abortEarly: false })
        postStore.updatePost(formData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                yupErrors[err.path] = err.message;
            });
        }

        console.log('yuperrors', yupErrors)
    }
}

onBeforeMount(async() => {
    await subcategoryStore.getAllSubcategoriesDropdown();
})

onMounted(async() => {
    if (id) {
        post.value = await postStore.getUserPost(id);
        formData.id = id;
        formData.type = post.value.type; 
        formData.subcategory_id = post.value.subcategory_id; 
        formData.incident_location = post.value.incident_location; 
        formData.incident_date = post.value.incident_date.original; 
    }
})

</script>

<template>
    <div class="mt-5 md:mt-5">
        <div class="container mx-auto grid grid-cols-1 place-items-start w-auto md:w-[420px]">
            <Card class="p-8">
                    <form @submit.prevent="updatePost(formData)">
                        <div class="border-b-2 mb-5">
                            <div class="flex flex-row items-center justify-center p-2 gap-x-2 text-primary">
                                <i class="pi pi-plus-circle text-secondary"></i>
                                <h2 class="font-bold text-sm md:text-base">Creating Lost/Found Item</h2>
                            </div>
                        </div>
                        <div class="my-1">
                            <label class="text-primary text-xs md:text-base">Type</label>
                            <div class="flex mt-2">
                                <div class="flex items-center me-4">
                                    <input id="inline-radio" type="radio" value="Lost" v-model="formData.type" name="inline-radio-group" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="inline-radio" class="ms-2 text-xs font-medium text-gray-900 dark:text-gray-300 md:text-base">Lost</label>
                                </div>
                                <div class="flex items-center me-4">
                                    <input id="inline-2-radio" type="radio" value="Found" v-model="formData.type" name="inline-radio-group" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="inline-2-radio" class="ms-2 text-xs font-medium text-gray-900 dark:text-gray-300 md:text-base">Found</label>
                                </div>
                            </div>
                            <p v-if="postStore.errors && postStore.errors.type" class="text-red-500 text-xs">{{ postStore.errors.type[0] }}</p>
                            <p v-else="yupErrors.type" class="text-red-500 text-xs">{{ yupErrors.type }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Item</label>
                            <select v-model="formData.subcategory_id"  class="h-6 w-full border-[1.1px] border-primary mt-1 mb-1 p-0 text-xs rounded md:h-10 p-2 text-base">
                                <option disabled value="">Please select item</option>
                                <option v-for="subcategory in subcategoryStore.subcategoriesDropdown" :value="subcategory.id">{{  subcategory.name }}</option>
                            </select>
                            <p v-if="postStore.errors && postStore.errors.subcategory_id" class="text-red-500 text-xs">{{ postStore.errors.subcategory_id[0] }}</p>
                            <p v-else="yupErrors.subcategory_id" class="text-red-500 text-xs">{{ yupErrors.subcategory_id }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Incident Location</label>
                            <input v-model="formData.incident_location" type="text"  class="h-6 w-full border-[1.1px] border-primary mt-1 mb-1 p-1 text-xs rounded md:h-10 p-2 text-base">
                            <p v-if="postStore.errors && postStore.errors.incident_location" class="text-red-500 text-xs">{{ postStore.errors.incident_location[0] }}</p>
                            <p v-else="yupErrors.incident_location" class="text-red-500 text-xs">{{ yupErrors.incident_location }}</p>
                        </div>

                        <div>
                            <label class="text-primary text-sm">Incident Date</label>
                            <input type="date" v-model="formData.incident_date"  class="h-6 w-full border-[1.1px] border-primary mt-1 mb-1 p-1 text-xs rounded md:h-10 p-2 text-base">
                            <p v-if="postStore.errors && postStore.errors.incident_date" class="text-red-500 text-xs">{{ postStore.errors.incident_date[0] }}</p>
                            <p v-else="yupErrors.incident_date" class="text-red-500 text-xs">{{ yupErrors.incident_date }}</p>
                        </div>

                        <button class="w-full bg-secondary p-1 mt-2 text-white rounded">Submit</button>
                        
                    </form>
            </Card>
        </div>
</div>

</template>