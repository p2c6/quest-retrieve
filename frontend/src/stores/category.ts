import { apiClient } from "@/config/http";
import type { DeleteCategory, GetCategory, StoreCategory, UpdateCategory } from "@/types/category-types";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useRouter } from "vue-router";

export const useCategoryStore = defineStore('category', () => {
    const router = useRouter();
    const categories = ref<object>({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);
    const keyword = ref(null);

    const getAllCategories = async(page = 1) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/categories?page=${page}&filter[name]=${keyword.value ?? ""}`);
            
            categories.value =  response.data;

            isLoading.value = false;

        } catch(error) {
            console.log("Error fetching categories: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const getCategory = async(id: GetCategory) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/categories/${id}`);
            
            if (response.status === 200) {
                return response.data.data;
            }

        } catch(error) {
            console.log("Error fetching categories: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const storeCategory = async(payload: StoreCategory) => {
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

    const updateCategory = async(payload: UpdateCategory) => {
        isLoading.value = false;

        try {
            const response = await apiClient.put(`/categories/${payload.id}`, payload)

            if (response.status === 200) {
                message.value = response.data.message;
                router.push({name: 'category.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on updating category: ", error)
        }

    }

    const deleteCategory = async(id: DeleteCategory) => {
        isLoading.value = false;

        try {
            const response = await apiClient.delete(`/categories/${id}`)

            if (response.status === 200) {
                message.value = response.data.message;
                
                getAllCategories()

                router.push({name: 'category.list'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on deleting category: ", error)
        }

    }

    return {
        categories,
        message,
        errors,
        isLoading,
        keyword,
        getAllCategories,
        getCategory,
        storeCategory,
        updateCategory,
        deleteCategory,
    }
});