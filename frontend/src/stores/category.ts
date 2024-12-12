import { apiClient } from "@/config/http";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useRouter } from "vue-router";

export const useCategoryStore = defineStore('category', () => {
    const router = useRouter();
    const categories = ref({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);

    const getAllCategories = async(page = 1) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/categories?page=${page}`);
            
            categories.value =  response.data;

            isLoading.value = false;

        } catch(error) {
            console.log("Error fetching categories: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const storeCategory = async(payload: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.post('/categories', payload)

            if (response.status === 201) {
                message.value = response.data.message;
                router.push({name: 'category.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on storing category: ", error)
        }

    }

    return {
        categories,
        message,
        errors,
        getAllCategories,
        storeCategory,
    }
});