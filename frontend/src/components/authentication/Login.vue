<script setup>
import { RouterLink } from 'vue-router';
import logo from "@/assets/qr-logo.png";
import home from "@/assets/home.png";
import Input from '@/components/Input.vue';
import { useAuthStore } from "@/stores/auth";
import { reactive } from 'vue';


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
            
                <i class="pi pi-arrow-left text-secondary"></i>
            
            <div class="flex flex-col items-center mt-5">
                <div>
                    <img :src="logo" alt="QuestRetrieve Logo" width="250px">
                </div>

                <div class="container mx-auto w-auto md:w-96 mt-24">
                    <form @submit.prevent="authStore.login(credentials)">
                        <div>
                            <div class="mb-8">
                                <h1 class="text-primary font-medium text-lg">Log in to your account</h1>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">E-mail address</label>
                                <input type="text" v-model="credentials.email" class="h-8 w-full border-[1.1px] border-primary mt-2 mb-2 p-2 rounded">
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Password</label>
                                <input type="password" v-model="credentials.password" class="h-8 w-full border-[1.1px] border-primary mt-2 mb-2 p-2 rounded">
                            </div>
                            <div class="border-t-[1.1px] border-gray w-full mt-5"></div>
                            <div class="mt-4 flex flex-row items-center gap-1">
                                <h1 class="text-primary">Don't have an account yet?</h1> 
                                <RouterLink :to="{name: 'register'}">
                                    <div class="underline text-primary"> Register</div>
                                </RouterLink>
                            </div>
                            <button class="bg-secondary rounded-lg px-6 py-1 text-white mt-10 text-sm">Next</button>
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