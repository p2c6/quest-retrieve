import { defineStore } from "pinia"
import { apiClient, webClient } from "@/config/http";
import type { User, UserForgotPassword, UserLogin, UserRegistration, UserResetPassword } from "@/types";
import { ref } from "vue";
import { useRouter } from "vue-router";


export const useAuthStore = defineStore('auth', () => {
    const router = useRouter();
    const user = ref<User | null>(null);
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);

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

    const login = async(payload: UserLogin): Promise<any> => {
        isLoading.value = true;
        errors.value = null;

        try { 
            const response = await apiClient.post('/authentication/login', payload);

            if (response.status === 200) {
                await getUser();
                
                router.push({name: 'home'});
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

    const verifyEmail = async(url: string): Promise<any> => {
        isLoading.value = true;

        try {
            const response = await webClient.get(url);

            if (response.status === 200) {
                await getUser();
            }
        } catch(error) {
            console.log(error)
        } finally {
            isLoading.value = false;
        }
        
    }

    const resendEmailVerificationLink = async():Promise<any> => {
        isLoading.value = true;

        try {
            const response = await webClient.post('/api/verification/email/verification-notification');

            if (response.status === 200) {
                console.log('response', response.data);
            }
        } catch (error) {
            console.log("Resend email verification error: " , error)
        } finally {
            isLoading.value = false;
        }
    }

    const forgotPassword = async(payload: UserForgotPassword):Promise<any> => {
        isLoading.value = true;

        try {
            const response = await webClient.post('/api/forgot-password', payload);

            if (response.status === 200) {
                console.log('response', response.data);
            }
        } catch (error: any) {
            if (error.status === 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Forgot password error: " , error)
        } finally {
            isLoading.value = false;
        }
    }

    const resetPassword = async(payload: UserResetPassword):Promise<any> => {
        isLoading.value = true;

        try {
            const response = await webClient.post('/api/reset-password', payload);

            if (response.status === 200) {
                message.value = response.data.message;
            }
        } catch (error: any) {
            if (error.status === 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors ?? error.response.data;
                return;
            }

            console.log("Reset password error: " , error)
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
        /*
            @Functions
        */
        login,
        register,
        logout,
        getUser,
        verifyEmail,
        resendEmailVerificationLink,
        forgotPassword,
        resetPassword,
    }
})