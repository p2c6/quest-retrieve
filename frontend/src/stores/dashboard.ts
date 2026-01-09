import { defineStore } from "pinia"
import { apiClient } from "@/config/http";
import { ref } from "vue";


export const useDashboardStore = defineStore('dashboard', () => {
    const data = ref<number[]>([]);
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);

    const getUserCountPerMonth = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:res } = await apiClient.get('/admin/dashboard/users/count-per-month')
            data.value = res.data
        } catch(error) {
            data.value = [];
            console.log('Getting user count per month error: ', error)
        } finally {
            isLoading.value = false;
        }
    }
    
    const getVerifiedUserCount = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data } = await apiClient.get('/admin/dashboard/users/count-verified')
            data.value = data
        } catch(error) {
            data.value = [];
            console.log('Getting verified user count error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    return {
        /*
            @Variables
        */
        data,
        isLoading,
        message,
        errors,
        /*
            @Functions
        */
        getUserCountPerMonth,
        getVerifiedUserCount
    }
})