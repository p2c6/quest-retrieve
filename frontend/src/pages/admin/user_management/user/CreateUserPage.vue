<script setup>
import Card from '@/components/Card.vue';
import { useAuthStore } from '@/stores/auth';
import { useCategoryStore } from '@/stores/category';
import { useSubcategoryStore } from '@/stores/subcategory';
import { onBeforeMount, reactive } from 'vue';
import { RouterLink } from 'vue-router';

const subcategoryStore = useSubcategoryStore();
const authStore = useAuthStore();
const categoryStore = useCategoryStore();

const formData = reactive({
    category_id: '',
    name: ''
});

onBeforeMount(async() => {
    await categoryStore.getAllCategoriesDropdown();
})


</script>

<template>
        <Card class="p-5 flex flex-row mt-2">
            <div class="overflow-x-auto w-full">
                <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="md:text-left text-primary font-medium">Create User</p>
                        <p class="text-tertiary md:text-left text-xs md:text-sm">You are about to create user.</p>
                    </div>
                    <div class="w-full md:w-6">
                        <RouterLink :to="{name: 'category.list'}">
                            <div class="bg-slate-200 rounded text-center">
                                <i class="text-primary pi pi-chevron-left cursor-pointer text-xs"></i>
                            </div>
                        </RouterLink>
                    </div>
                </div>
                <div class="mt-5">
                    <form @submit.prevent="authStore.register(formData)">
                        <div>
                            <div class="flex gap-y-0.5 flex-col md:flex-row gap-5">
                                <div class="w-full md:w-1/2">
                                    <label class="text-primary text-sm font-medium">Last Name</label>
                                    <input type="text" v-model="formData.last_name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                    <p v-if="authStore.errors && authStore.errors.last_name" class="text-red-500 text-xs">{{ authStore.errors.last_name[0] }}</p>
                                </div>
                                <div class="w-full md:w-1/2">
                                    <label class="text-primary text-sm font-medium">First Name</label>
                                    <input type="text" v-model="formData.first_name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                    <p v-if="authStore.errors && authStore.errors.first_name" class="text-red-500 text-xs">{{ authStore.errors.first_name[0] }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="text-primary text-sm font-medium">Contact Number</label>
                                <input type="text" v-model="formData.contact_no" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <p v-if="authStore.errors && authStore.errors.contact_no" class="text-red-500 text-xs">{{ authStore.errors.contact_no[0] }}</p>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Date of birth</label>
                                <br>
                                <input type="date" v-model="formData.birthday" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded"  />
                                <p v-if="authStore.errors && authStore.errors.birthday" class="text-red-500 text-xs">{{ authStore.errors.birthday[0] }}</p>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Email</label>
                                <input type="text" v-model="formData.email" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <p v-if="authStore.errors && authStore.errors.email" class="text-red-500 text-xs">{{ authStore.errors.email[0] }}</p>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Password</label>
                                <input type="password" v-model="formData.password" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <p v-if="authStore.errors && authStore.errors.password" class="text-red-500 text-xs">{{ authStore.errors.password[0] }}</p>
                            </div>
                            <button class="bg-secondary rounded-lg w-full py-1 text-white mt-5 text-sm md:w-20">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </Card>
</template>