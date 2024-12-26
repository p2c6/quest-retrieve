import { apiClient } from "@/config/http";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useRouter } from "vue-router";


export const useUserStore = defineStore('user', () => {
    const router = useRouter();
    const users = ref<object>({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);
    const keyword = ref(null);
    const rolesDropdown = ref(null);

    const getAllUsers = async(page = 1) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/users?page=${page}&filter[keyword]=${keyword.value ?? ""}`);
            
            users.value =  response.data;

            isLoading.value = false;

        } catch(error) {
            console.log("Error fetching users: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const storeUser = async(payload: any) => {
        isLoading.value = false;
        
        try { 
            const response = await apiClient.post('/users', payload);

            if (response.status === 201) {
                message.value = response.data.message;
                router.push({name: 'users.list'});
            }

        } catch(error: any) {
            if (error.status === 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }
            
            console.log("Error on storing user: " , error);
        } finally {
            isLoading.value = false;
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

        /*
            @Functions
        */
        getAllUsers,
        storeUser,
    }
});