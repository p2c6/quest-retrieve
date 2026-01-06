import { defineStore } from "pinia"
import { apiClient, webClient } from "@/config/http";
import type { User, UserForgotPassword, UserLogin, UserRegistration, UserResetPassword } from "@/types";
import { computed, ref } from "vue";
import { useRouter } from "vue-router";


export const useFileUploadStore = defineStore('file-upload', () => {
    const router = useRouter();
    const user = ref<User | null>(null);
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);
    const fileName = ref<Object | null>(null);

    const getUser = async():Promise<void> => {
        isLoading.value = true;

        try {
            const { data } = await apiClient.get('/user')
            console.log('data', data)
            user.value = data
        } catch(error) {
            user.value = null;
            console.log('Getting user error: ', error)
        } finally {
            isLoading.value = false;
        }
    }

    const uploadTemporaryFile = async(payload: any): Promise<any> => {
        isLoading.value = true;
        errors.value = null;

        try { 
            const response = await apiClient.post('/temporary-file/upload', payload, {
                headers: {
                'Content-Type': 'multipart/form-data',
                },
            });

            if (response.status === 200) {
                fileName.value = response.data.file_path;
            }

        } catch(error: any) {
            if (error.status === 401) {
                console.log('Authentication error', error);
                const authError = {
                    data: error.response?.data
                };
                errors.value = authError;
            }

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

    const register = async(payload: UserRegistration): Promise<any> => {
        isLoading.value = true;
        errors.value = null;

        try { 
            const response = await apiClient.post('/authentication/register', payload);

            if (response.status === 201) {
                router.push({name: 'email.verification'})
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
        message,
        errors,
        fileName,
        /*
            @Functions
        */
        uploadTemporaryFile,
        register,
        logout,
        getUser,
    }
})