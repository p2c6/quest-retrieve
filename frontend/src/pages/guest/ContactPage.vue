<script setup>
import Input from '@/components/Input.vue';
import Card from '@/components/Card.vue';
import { useRoute } from 'vue-router';
import { computed, onBeforeUnmount, reactive } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { usePostStore } from '@/stores/post';

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
                    <form @submit.prevent="postStore.requestClaimOrReturnPost(formData)">
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
                        </div>
                        <div>
                            <label class="text-primary text-sm">Description</label>
                            <div class="text-gray-400 text-xs">Please describe the item as much as possible in a very detailed way</div>
                            <textarea v-model="formData.item_description" class="w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded" cols="10" rows="2"></textarea>
                            <p v-if="postStore.errors && postStore.errors.item_description" class="text-red-500 text-xs">{{ postStore.errors.item_description[0] }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Where</label>
                            <input  v-model="formData.where" type="text" :class="`h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded`">
                            <p v-if="postStore.errors && postStore.errors.where" class="text-red-500 text-xs">{{ postStore.errors.where[0] }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">When</label>
                            <input  v-model="formData.when" type="date" :class="`h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded`">
                            <p v-if="postStore.errors && postStore.errors.when" class="text-red-500 text-xs">{{ postStore.errors.when[0] }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">E-mail</label>
                            <input  v-model="formData.email" type="text" :class="`h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded`">
                            <p v-if="postStore.errors && postStore.errors.email" class="text-red-500 text-xs">{{ postStore.errors.email[0] }}</p>
                        </div>
                        <div>
                            <label class="text-primary text-sm">Message</label>
                            <textarea v-model="formData.message" class="w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded" cols="10" rows="2"></textarea>
                            <p v-if="postStore.errors && postStore.errors.message" class="text-red-500 text-xs">{{ postStore.errors.message[0] }}</p>
                        </div>
                        <button class="w-full bg-secondary p-1 mt-1 text-white rounded">Submit</button>
                    </form>
            </Card>
        </div>
</div>

</template>