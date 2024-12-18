import { apiClient } from "@/config/http";
import type { DeleteCategory, GetCategory, StoreCategory, UpdateCategory } from "@/types/category-types";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useRouter } from "vue-router";

export const useSubcategoryStore = defineStore('subcategory', () => {
    const router = useRouter();
    const subcategories = ref<object>({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);
    const keyword = ref(null);

    const getAllSubcategories = async(page = 1) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/subcategories?page=${page}&filter[keyword]=${keyword.value ?? ""}`);
            
            subcategories.value =  response.data;

            isLoading.value = false;

        } catch(error) {
            console.log("Error fetching categories: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const getSubcategory = async(id: any) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/subcategories/${id}`);
            
            if (response.status === 200) {
                return response.data.data;
            }

        } catch(error) {
            console.log("Error fetching subcategories: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const storeSubcategory = async(payload: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.post('/subcategories', payload)

            if (response.status === 201) {
                message.value = response.data.message;
                router.push({name: 'subcategory.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on storing subcategory: ", error)
        }

    }

    const updateSubcategory = async(payload: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.put(`/subcategories/${payload.id}`, payload)

            if (response.status === 200) {
                message.value = response.data.message;
                router.push({name: 'subcategory.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on updating subcategory: ", error)
        }

    }

    const deleteSubcategory = async(id: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.delete(`/subcategories/${id}`)

            if (response.status === 200) {
                message.value = response.data.message;
                
                getAllSubcategories()

                router.push({name: 'subcategory.list'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on deleting subcategory: ", error)
        }

    }

    return {
        subcategories,
        message,
        errors,
        isLoading,
        keyword,
        getAllSubcategories,
        getSubcategory,
        storeSubcategory,
        updateSubcategory,
        deleteSubcategory,
    }
});