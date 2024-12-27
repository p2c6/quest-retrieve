<script setup>
import Input from '@/components/Input.vue';
import Card from '@/components/Card.vue';
import { onBeforeMount, onBeforeUnmount, reactive } from 'vue';
import { useSubcategoryStore } from '@/stores/subcategory';
import { usePostStore } from '@/stores/post';

const subcategoryStore = useSubcategoryStore();
const postStore = usePostStore();

const formData = reactive({
    type: '',
    subcategory_id: '',
    incident_location: '',
    incident_date: '',
})

onBeforeMount(async() => {
    await subcategoryStore.getAllSubcategoriesDropdown();
})

</script>

<template>
    <div class="mt-5 md:mt-5">
        <div class="container mx-auto grid grid-cols-1 place-items-start w-auto md:w-[420px]">
            <Card class="p-8">
                    <form @submit.prevent="postStore.storePost(formData)">
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
                        </div>
                        <div>
                            <label class="text-primary text-sm">Item</label>
                            <select v-model="formData.subcategory_id"  class="h-6 w-full border-[1.1px] border-primary mt-1 mb-1 p-0 text-xs rounded md:h-10 p-2 text-base">
                                <option disabled value="">Please select item</option>
                                <option v-for="subcategory in subcategoryStore.subcategoriesDropdown" :value="subcategory.id">{{  subcategory.name }}</option>
                            </select>
                            <p v-if="postStore.errors && postStore.errors.subcategory_id" class="text-red-500 text-xs">{{ postStore.errors.subcategory_id[0] }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Incident Location</label>
                            <input v-model="formData.incident_location" type="text"  class="h-6 w-full border-[1.1px] border-primary mt-1 mb-1 p-1 text-xs rounded md:h-10 p-2 text-base">
                            <p v-if="postStore.errors && postStore.errors.incident_location" class="text-red-500 text-xs">{{ postStore.errors.incident_location[0] }}</p>
                        </div>

                        <div>
                            <label class="text-primary text-sm">Incident Date</label>
                            <input type="date" v-model="formData.incident_date"  class="h-6 w-full border-[1.1px] border-primary mt-1 mb-1 p-1 text-xs rounded md:h-10 p-2 text-base">
                            <p v-if="postStore.errors && postStore.errors.incident_date" class="text-red-500 text-xs">{{ postStore.errors.incident_date[0] }}</p>
                        </div>

                        <button class="w-full bg-secondary p-1 mt-2 text-white rounded">Submit</button>
                        
                    </form>
            </Card>
        </div>
</div>

</template>