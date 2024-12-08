<script setup>
import { RouterLink } from 'vue-router';
import logo from "@/assets/qr-logo.png";
import home from "@/assets/home.png";
import Input from '@/components/Input.vue';
import { useAuthStore } from "@/stores/auth";
import { onBeforeUnmount, onMounted, reactive } from 'vue';


const authStore = useAuthStore();

const credentials = reactive({
    email: '',
    password: ''
});
</script>

<template>
<div class="bg-white">
    <div class="grid grid-cols-1 lg:grid-cols-2 h-screen">
        <div class="mx-6 mt-6">
                <RouterLink :to="{name: 'home'}">
                    <i class="pi pi-arrow-left text-secondary"></i>
                </RouterLink>
            
            <div class="flex flex-col items-center mt-5">
                <div>
                    <img :src="logo" alt="QuestRetrieve Logo" width="250px">
                </div>

                <div class="container mx-auto w-auto md:w-96 mt-16">
                    <div v-if="authStore.errors?.data && authStore.errors?.data.message" class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md mb-5" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <svg class="w-6 h-6 text-red-500 dark:text-white mr-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
                                </svg>

                            </div>
                            <div>
                                <p class="font-bold text-sm">Error</p>
                                <div class="text-xs flex gap-1">{{ authStore.errors?.data.message }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="status" v-if="authStore.isLoading" class="flex items-center justify-center mt-10">
                        <div class="flex flex-col items-center justify-center gap-5">
                            <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                            </svg>
                            <p class="text-primary text-xs">Please Wait...</p>
                        </div>
                    </div>
                    <form @submit.prevent="authStore.login(credentials)" v-else>
                        <div>
                            <div class="mb-8">
                                <h1 class="text-primary font-medium text-lg">Log in to your account</h1>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">E-mail address</label>
                                <input type="text" v-model="credentials.email" class="h-8 w-full border-[1.1px] border-primary mt-2 mb-2 p-2 rounded">
                                <p v-if="authStore.errors && authStore.errors.email" class="text-red-500 text-xs">{{ authStore.errors.email[0] }}</p>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Password</label>
                                <input type="password" v-model="credentials.password" class="h-8 w-full border-[1.1px] border-primary mt-2 mb-2 p-2 rounded">
                                <p v-if="authStore.errors && authStore.errors.password" class="text-red-500 text-xs">{{ authStore.errors.password[0] }}</p>
                            </div>
                            <div class="border-t-[1.1px] border-gray w-full mt-5"></div>
                            <div class="mt-4 flex flex-row items-center gap-1 text-xs">
                                <h1 class="text-primary">Don't have an account yet?</h1> 
                                <RouterLink :to="{name: 'register'}">
                                    <div class="underline text-primary font-semibold"> Register</div>
                                </RouterLink>
                            </div>
                            <div class="flex flex-row items-center justify-between mt-10">
                                <button class="bg-secondary rounded-lg px-6 py-1 text-white text-sm">
                                    Next
                                </button>
                                <RouterLink :to="{name: 'password.forgot'}">
                                    <p class="text-xs text-primary font-semibold">Forgot Password?</p>
                                </RouterLink>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
        <div class="hidden lg:block bg-primary text-white">
            <div class="h-screen flex flex-col items-center justify-between">
                <div class="w-18 text-center">
                    <h1 class="text-2xl font-bold mt-4">The Ultimate Lost and Found Platform</h1>
                    <p class="text-sm">Lost something important or found an item that isn’t yours?</p>
                </div>
                
                <img :src="home" alt="Illustration of people">
                <div class="flex flex-row gap-1 mb-10">
                    <p>Introducing, </p>
                    <p class="font-bold underline">QuestRetrieve.</p>
                </div>
                
                <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#f3f4f5" fill-opacity="1" d="M0,192L0,224L120,224L120,256L240,256L240,128L360,128L360,288L480,288L480,160L600,160L600,160L720,160L720,64L840,64L840,192L960,192L960,288L1080,288L1080,288L1200,288L1200,160L1320,160L1320,32L1440,32L1440,320L1320,320L1320,320L1200,320L1200,320L1080,320L1080,320L960,320L960,320L840,320L840,320L720,320L720,320L600,320L600,320L480,320L480,320L360,320L360,320L240,320L240,320L120,320L120,320L0,320L0,320Z"></path></svg> -->
            </div>
        </div>
    </div>
</div>
</template>
<style scope>

</style>