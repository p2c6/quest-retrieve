import { defineStore } from "pinia"
import { apiClient } from "@/config/http";
import { ref } from "vue";


export const useDashboardStore = defineStore('dashboard', () => {
    const userCountPerMonthData = ref<number[]>([]);
    const verifiedCountData = ref<number[]>([]);
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);

    const getUserCountPerMonth = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/admin/dashboard/users/count-per-month')
            userCountPerMonthData.value = response.data
        } catch(error) {
            userCountPerMonthData.value = [];
            console.log('Getting user count per month error: ', error)
        } finally {
            isLoading.value = false;
        }
    }
    
    const getVerifiedUserCount = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/admin/dashboard/users/count-verified')
            verifiedCountData.value = response.data
        } catch(error) {
            verifiedCountData.value = [];
            console.log('Getting verified user count error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    return {
        /*
            @Variables
        */
        userCountPerMonthData,
        verifiedCountData,
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