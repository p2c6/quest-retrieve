<script setup>
import Card from '@/components/Card.vue';
import { useRoute } from 'vue-router';
import { computed, onBeforeUnmount, reactive } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { usePostStore } from '@/stores/post';
import * as yup from 'yup';

let schema = yup.object({
    full_name: yup
        .string()
        .required('returner\'s name is required.'),
    item_description: yup
        .string()
        .required('description is required.'),
    where: yup
        .string()
        .required('where is required.'),
    when: yup
        .string()
        .required('when is required.'),
    email: yup
        .string()
        .email('invalid e-mail format.')
        .required('email is required.'),
    message: yup
        .string()
        .required('message is required.'),
})

const route = useRoute();
const authStore = useAuthStore();
const postStore = usePostStore();

const formData = reactive({
    id: route.params.id,
    type: route.params.type,
    full_name: authStore.fullName,
    email: '',
    item_description: '',
    where: '',
    when: '',
    message: '',
});

const yupErrors = reactive({});

const requestClaimOrReturnPost= async(formData) => {
    yupErrors.full_name = '';
    yupErrors.email = '';
    yupErrors.item_description = '';
    yupErrors.where = '';
    yupErrors.when = '';
    yupErrors.message = '';

    try {
        await schema.validate(formData, {abortEarly: false })
        postStore.requestClaimOrReturnPost(formData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                yupErrors[err.path] = err.message;
            });
        }

        console.log('yuperrors', yupErrors)
    }
}

const term = computed(() => {
    return route.params.type == "Found" ? {title: "Claim", owner: "Claimer's"} : {title: "Return", owner: "Returner's"};
});

onBeforeUnmount(() => {
    postStore.errors = null;
})


</script>

<template>
    <div class="mt-5 md:mt-5">
        <div class="container mx-auto grid grid-cols-1 place-items-start w-auto md:w-[520px]">
            <Card class="p-8">
                    <form @submit.prevent="requestClaimOrReturnPost(formData)">
                        <div class="border-b-2 mb-5">
                            <div class="flex flex-row items-center justify-center p-2 gap-x-2 text-primary">
                                <i class="pi pi-id-card text-secondary"></i>
                                <h2 class="font-bold">{{ term.title }} Item</h2>
                            </div>
                        </div>
                        <div>
                            <label class="text-primary text-sm">{{ term.owner }} name</label>
                            <input  v-model="formData.full_name" type="text" :class="`h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded`">
                            <p v-if="postStore.errors && postStore.errors.full_name" class="text-red-500 text-xs">{{ postStore.errors.full_name[0] }}</p>
                            <p v-else="yupErrors.full_name" class="text-red-500 text-xs">{{ yupErrors.full_name }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Description</label>
                            <div class="text-gray-400 text-xs">Please describe the item as much as possible in a very detailed way</div>
                            <textarea v-model="formData.item_description" class="w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded" cols="10" rows="2"></textarea>
                            <p v-if="postStore.errors && postStore.errors.item_description" class="text-red-500 text-xs">{{ postStore.errors.item_description[0] }}</p>
                            <p v-else="yupErrors.item_description" class="text-red-500 text-xs">{{ yupErrors.item_description }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Where</label>
                            <input  v-model="formData.where" type="text" :class="`h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded`">
                            <p v-if="postStore.errors && postStore.errors.where" class="text-red-500 text-xs">{{ postStore.errors.where[0] }}</p>
                            <p v-else="yupErrors.where" class="text-red-500 text-xs">{{ yupErrors.where }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">When</label>
                            <input  v-model="formData.when" type="date" :class="`h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded`">
                            <p v-if="postStore.errors && postStore.errors.when" class="text-red-500 text-xs">{{ postStore.errors.when[0] }}</p>
                            <p v-else="yupErrors.when" class="text-red-500 text-xs">{{ yupErrors.when }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">E-mail</label>
                            <input  v-model="formData.email" type="text" :class="`h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded`">
                            <p v-if="postStore.errors && postStore.errors.email" class="text-red-500 text-xs">{{ postStore.errors.email[0] }}</p>
                            <p v-else="yupErrors.email" class="text-red-500 text-xs">{{ yupErrors.email }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Message</label>
                            <textarea v-model="formData.message" class="w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded" cols="10" rows="2"></textarea>
                            <p v-if="postStore.errors && postStore.errors.message" class="text-red-500 text-xs">{{ postStore.errors.message[0] }}</p>
                            <p v-else="yupErrors.messsage" class="text-red-500 text-xs">{{ yupErrors.message }}</p>
                        </div>
                        <button :class="[
                            'w-full bg-secondary p-1 mt-1 text-white rounded',
                            postStore.isLoading ? 'disabled opacity-70 cursor-not-allowed' : ''
                        ]" :disabled="postStore.isLoading">
                            <div v-if="postStore.isLoading" class="flex items-center justify-center">
                                <div class="flex flex-row items-center justify-center gap-5">
                                    <svg aria-hidden="true" class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                    </svg>
                                    <span>Loading...</span>
                                </div>
                            </div>
                            <span v-else>Submit</span>
                        </button>
                    </form>
            </Card>
        </div>
</div>

</template>