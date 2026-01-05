<script setup>
import { RouterLink } from 'vue-router';
import logo from "@/assets/qr-logo.png";
import help from "@/assets/help.png";
import { useAuthStore } from "@/stores/auth";
import { onBeforeUnmount, reactive } from 'vue';
import * as yup from 'yup';

let schema = yup.object({
    email: yup
        .string()
        .email('invalid e-mail format.')
        .required('e-mail is required.'),
})


const authStore = useAuthStore();

const formData = reactive({
    email: '',
});

const yupErrors = reactive({})

const forgotPassword = async(credentials) => {
    yupErrors.email = '';

    try {
        await schema.validate(credentials, {abortEarly: false })
        authStore.forgotPassword(formData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                yupErrors[err.path] = err.message;
            });
        }
    }
}

onBeforeUnmount(() => {
    authStore.message = null;
    authStore.errors = null;
})

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
                    <div v-if="authStore.message" class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md mb-5" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <svg class="w-6 h-6 text-teal-500 dark:text-white mr-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                            <p class="font-bold text-sm">{{ authStore.message }}</p>
                            </div>
                        </div>
                    </div>
                    </div>

                <div class="container mx-auto w-auto md:w-96 mt-16">
                    <form @submit.prevent="forgotPassword(formData)">
                        <div>
                            <div class="mb-6">
                                <h1 class="text-primary font-medium text-lg">Forgot Password</h1>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">E-mail address</label>
                                <input type="text" v-model="formData.email" class="h-8 w-full border-[1.1px] border-primary mt-2 mb-2 p-2 rounded">
                                <p v-if="authStore.errors && authStore.errors.email" class="text-red-500 text-xs">{{ authStore.errors.email[0] }}</p>
                                <p v-else="yupErrors.email" class="text-red-500 text-xs">{{ yupErrors.email }}</p>
                            </div>
                            <div class="border-t-[1.1px] border-gray w-full mt-5"></div>
                            <p class="mt-1 text-red-400 text-xs">Note: If you do not receive the e-mail within a few minutes, click the button below to send reset password link to your e-mail again.</p>
                            <button v-if="authStore.isLoading" class="bg-secondary rounded-lg px-6 py-1 text-white mt-10 text-sm opacity-50 cursor-not-allowed" disabled>
                                <div role="status" v-if="authStore.isLoading">
                                    <svg aria-hidden="true" class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                    </svg>
                                </div>
                            </button>
                            <button v-else class="bg-secondary rounded-lg px-6 py-1 text-white mt-5 text-sm w-full">
                                Send Reset Password Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="hidden lg:block bg-primary text-white">
            <div class="h-screen flex flex-col items-center justify-between">
                <div class="text-center mt-5">
                    <h1 class="text-lg font-bold">Help others find what they’ve lost or return what you’ve found.</h1>
                </div>
                <img :src="help" alt="Illustration of people">

                <p  class="w-auto mb-10">The time starts now.</p>

                <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#f3f4f5" fill-opacity="1" d="M0,256L0,128L120,128L120,192L240,192L240,96L360,96L360,32L480,32L480,192L600,192L600,224L720,224L720,256L840,256L840,0L960,0L960,160L1080,160L1080,192L1200,192L1200,64L1320,64L1320,192L1440,192L1440,320L1320,320L1320,320L1200,320L1200,320L1080,320L1080,320L960,320L960,320L840,320L840,320L720,320L720,320L600,320L600,320L480,320L480,320L360,320L360,320L240,320L240,320L120,320L120,320L0,320L0,320Z"></path></svg> -->
            </div>
        </div>
    </div>
</div>
</template>
<style scope>

</style>