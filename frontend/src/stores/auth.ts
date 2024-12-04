import { defineStore } from "pinia"
import { apiClient } from "@/config/http";
import type { LoginCredentials } from "@/types";
import { ref } from "vue";
import { useRouter } from "vue-router";


export const useAuthStore = defineStore('auth', () => {
    const router = useRouter();
    const user = ref(null);
    const isLoading = ref<boolean | null>(null);
    const errors = ref(null);

    const getUser = async():Promise<void> => {
        isLoading.value = true;
        try {
            const { data } = await apiClient.get('/user')
            console.log('data', data)
            user.value = data
        } catch(error) {
            console.log('Getting user error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    const login = async(credentials: LoginCredentials): Promise<any> => {
        isLoading.value = true;
        errors.value = null;
        try { 
            const response = await apiClient.post('/authentication/login', credentials);

            if (response.status === 200) {
                await getUser();
                router.push({name: 'home'});
            }

        } catch(error: any) {
            if (error.status === 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }
            
            console.log("Login error: " , error);
        } finally {
            isLoading.value = false;
        }
    }

    const logout = async(): Promise<void> => {
        isLoading.value = true;
        try { 
            const response = await apiClient.post('/authentication/logout');

            if (response.status === 200) {
                user.value = null;
                router.push({name: 'home'});
            }

        } catch(error: any) {
            if (error.status === 422) {
                console.log('validation error');
                return;
            }
            
            console.log("Login error: " , error);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        /*
            @Variables
        */
        user,
        isLoading,
        errors,
        /*
            @Functions
        */
        login,
        logout,
        getUser,
    }
})