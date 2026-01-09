import { defineStore } from "pinia"
import { apiClient } from "@/config/http";
import { ref } from "vue";


export const useModeratorDashboardStore = defineStore('moderator-dashboard', () => {
    const postLineData = ref<number>(0);
    const totalPostCountData = ref<number>(0);
    const totalApprovedCountData = ref<number>(0);
    const totalRejectedCountData = ref<number>(0);
    const totalCompletedCountData = ref<number>(0);
    const totalPendingCountData = ref<number>(0);
    const postPieData = ref<number>(0);
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);

    const getPostLineData = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/moderator/dashboard/posts/count-per-month')
            postLineData.value = response.data
        } catch(error) {
            postLineData.value = 0;
            console.log('Getting posts count per month error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    const getTotalPostCount = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/moderator/dashboard/posts/count-total-post')
            totalPostCountData.value = response.data
        } catch(error) {
            totalPostCountData.value = 0;
            console.log('Getting total posts count error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    const getPendingPostCount = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/moderator/dashboard/posts/count-pending-post')
            totalPendingCountData.value = response.data
        } catch(error) {
            totalPendingCountData.value = 0;
            console.log('Getting total pending posts count error: ', error)
        } finally {
            isLoading.value = false;
        }
    
    }

    const getApprovedPostCount = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/moderator/dashboard/posts/count-approved-post')
            totalApprovedCountData.value = response.data
        } catch(error) {
            totalApprovedCountData.value = 0;
            console.log('Getting total approved posts count error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    const getRejectedPostCount = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/moderator/dashboard/posts/count-rejected-post')
            totalRejectedCountData.value = response.data
        } catch(error) {
            totalRejectedCountData.value = 0;
            console.log('Getting total rejected posts count error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    const getCompletedPostCount = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/moderator/dashboard/posts/count-completed-post')
            totalCompletedCountData.value = response.data
        } catch(error) {
            totalCompletedCountData.value = 0;
            console.log('Getting total completed posts count error: ', error)
        } finally {
            isLoading.value = false;
        }
    }
    
    const getPostPieData =  async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data:response } = await apiClient.get('/moderator/dashboard/posts/count-per-status')
            postPieData.value = response.data
        } catch(error) {
            postPieData.value = 0;
            console.log('Getting posts pie data error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    return {
        /*
            @Variables
        */
        postLineData,
        totalPostCountData,
        totalApprovedCountData,
        totalRejectedCountData,
        totalCompletedCountData,
        totalPendingCountData,
        postPieData,
        isLoading,
        message,
        errors,
        /*
            @Functions
        */
        getPostLineData,
        getPostPieData,
        getTotalPostCount,
        getPendingPostCount,
        getApprovedPostCount,
        getRejectedPostCount,
        getCompletedPostCount,
    }
})