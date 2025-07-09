<script setup>
import Card from '@/components/Card.vue';
import { useRoleStore } from '@/stores/role';
import { onMounted, onUnmounted, reactive, ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import * as yup from 'yup';

let schema = yup.object({
    name: yup.string().required('role name is required.'),
})

const roleStore = useRoleStore();
const route = useRoute();

const role = ref({});

const formData = reactive({
    id: '',
    name: ''
});

const id = route.params?.id;

const yupErrors = reactive({});

const updateRole = async(formData) => {
    yupErrors.name = '';
    roleStore.errors = null;

    try {
        await schema.validate(formData, {abortEarly: false })
        roleStore.updateRole(formData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                yupErrors[err.path] = err.message;
            });
        }
    }
}


onMounted(async() => {
    if (id) {
        role.value = await roleStore.getRole(id);

        formData.id = id;
        formData.name = role.value.name;
    }
})

onUnmounted(() => {
    role.value = {};
    roleStore.errors = null;
})


</script>

<template>
        <Card class="p-5 flex flex-row mt-2">
            <div class="overflow-x-auto w-full">
                <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="md:text-left text-primary font-medium">Edit Role</p>
                        <p class="text-tertiary md:text-left text-xs md:text-sm">You are about to update role.</p>
                    </div>
                    <div class="w-full md:w-6">
                        <RouterLink :to="{name: 'role.list'}">
                            <div class="bg-slate-200 rounded text-center">
                                <i class="text-primary pi pi-chevron-left cursor-pointer text-xs"></i>
                            </div>
                        </RouterLink>
                    </div>
                </div>
                <div class="mt-5">
                    <form @submit.prevent="updateRole(formData)">
                        <div>
                            <label class="text-primary text-sm font-medium">Role Name</label>
                            <input type="text" v-model="formData.name" :class="`h-8 w-full border-[1.1px] border-${yupErrors.name || (roleStore.errors && roleStore.errors.name) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                            <p v-if="yupErrors.name" class="text-red-500 text-xs">{{ yupErrors.name }}</p>
                            <p v-else-if="roleStore.errors && roleStore.errors.name" class="text-red-500 text-xs">{{ roleStore.errors.name[0] }}</p>
                        </div>
                        <button class="bg-secondary rounded-lg px-6 py-1 text-white text-sm w-full mt-2 md:w-24">Update</button>

                    </form>
                </div>
            </div>
        </Card>
</template>