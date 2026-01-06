import { apiClient } from "@/config/http";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useProfileStore = defineStore('profile', () => {
    const users = ref<object>({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);
    const keyword = ref(null);
    const column = ref<string | null>(null);

    const updateProfileDetails = async(payload: any) => {
        isLoading.value = false;

        try {

            if (!payload.password || payload.length === 0 || payload.password === '') {
                delete payload.password;
            }

            const response = await apiClient.put(`/users/${payload.id}`, payload)

            if (response.status === 200) {
                message.value = response.data.message;
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on updating user: ", error)
        }
    }

    const updateProfilePassword = async(payload: any) => {
    isLoading.value = false;

    try {

        const response = await apiClient.put(`/profile/password/${payload.id}`, payload)

        if (response.status === 200) {
            message.value = response.data.message;
        }

    } catch(error: any) {
        if (error.status == 422) {
            console.log('Validation error', error);
            errors.value = { current_password: error.response.data.message }
            return;
        }

        console.log("Error on updating user: ", error)
    }
}

    return {
        /*
            @Variables
         */
        users,
        errors,
        message,
        keyword,
        isLoading,
        column,

        /*
            @Functions
        */
        updateProfileDetails,
        updateProfilePassword,
    }
});