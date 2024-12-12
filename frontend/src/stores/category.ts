import { apiClient } from "@/config/http";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useCategoryStore = defineStore('category', () => {
    const categories = ref({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);

    const getAllCategories = async(page = 1) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/categories?page=${page}`);

            console.log('res', response.data)

            categories.value =  response.data;
        } catch(error) {
            console.log("Error fetching categories: ", error)
        }
    }

    return {
        categories,
        getAllCategories,
    }
});