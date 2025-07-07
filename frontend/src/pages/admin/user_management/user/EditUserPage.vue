<script setup>
import Card from '@/components/Card.vue';
import { useRoleStore } from '@/stores/role';
import { useUserStore } from '@/stores/user';
import { onBeforeMount, onMounted, onUnmounted, reactive, ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import * as yup from 'yup';

let schema = yup.object({
    last_name: yup.string().required('last name is required.'),
    first_name: yup.string().required('first name is required.'),
    birthday: yup.string().required('birthday is required.'),
    email: yup
        .string()
        .email('invalid email format.')
        .required('e-mail is required.'),
    password: yup
        .string()
        .transform((value) => (value === '' ? undefined : value)) 
        .min(8, 'password is too short.')
        .max(15, 'password is too long.'),
    password_confirmation: yup
        .string()
        .when('password', {
            is: (val) => val && val.trim().length > 0,
            then: (schema) =>
            schema
                .required('password confirmation is required.')
                .oneOf([yup.ref('password')], 'Your passwords do not match.'),
            otherwise: (schema) => schema.notRequired(),
        }),
    contact_no: yup.string().required('contact number is required.'),
    role_id: yup.string().required('role is required.')
})

const route = useRoute();

const userStore = useUserStore();
const roleStore = useRoleStore();

const user = ref({});

const id = route.params?.id;

const formData = reactive({
    id: '',
    email: '',
    password: '',
    password_confirmation: '',
    last_name: '',
    first_name: '',
    birthday: '',
    contact_no: '',
    role_id: '',
});

const yupErrors = reactive({})

const updateUser = async(formData) => {
    yupErrors.last_name = '';
    yupErrors.first_name = '';
    yupErrors.birthday = '';
    yupErrors.email = '';
    yupErrors.password = '';
    yupErrors.password_confirmation = '';
    yupErrors.contact_no = '';
    yupErrors.role_id = '';
    userStore.errors = null;

    try {
        await schema.validate(formData, {abortEarly: false })
        userStore.updateUser(formData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                yupErrors[err.path] = err.message;
            });
        }
    }
}

onBeforeMount(async() => {
    await roleStore.getAllRolesDropdown();
})

onMounted(async() => {
    if (id) {
        user.value = await userStore.getUser(id);

        formData.id = id;
        formData.email = user.value.email;
        formData.last_name = user.value.last_name;
        formData.first_name = user.value.first_name;
        formData.birthday = user.value.birthday;
        formData.contact_no = user.value.contact_no;
        formData.role_id = user.value.role_id;
    }
})

onUnmounted(() => {
    user.value = {};
    userStore.errors = null;
})


</script>

<template>
        <Card class="p-5 flex flex-row mt-2">
            <div class="overflow-x-auto w-full">
                <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                    <div class="text-center md:text-left">
                        <p class="md:text-left text-primary font-medium">Edit User</p>
                        <p class="text-tertiary md:text-left text-xs md:text-sm">You are about to update user.</p>
                    </div>
                    <div class="w-full md:w-6">
                        <RouterLink :to="{name: 'users.list'}">
                            <div class="bg-slate-200 rounded text-center">
                                <i class="text-primary pi pi-chevron-left cursor-pointer text-xs"></i>
                            </div>
                        </RouterLink>
                    </div>
                </div>
                <div class="mt-5">
                    <form @submit.prevent="updateUser(formData)">
                        <div>
                            <div class="flex gap-y-0.5 flex-col md:flex-row gap-5">
                                <div class="w-full md:w-1/2">
                                    <label class="text-primary text-sm font-medium">Last Name</label>
                                    <input type="text" v-model="formData.last_name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                    <p v-if="yupErrors.last_name" class="text-red-500 text-xs">{{ yupErrors.last_name }}</p>
                                    <p v-else-if="userStore.errors && userStore.errors.last_name" class="text-red-500 text-xs">{{ userStore.errors.last_name[0] }}</p>
                                </div>
                                <div class="w-full md:w-1/2">
                                    <label class="text-primary text-sm font-medium">First Name</label>
                                    <input type="text" v-model="formData.first_name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                    <p v-if="yupErrors.first_name" class="text-red-500 text-xs">{{ yupErrors.first_name }}</p>
                                    <p v-else-if="userStore.errors && userStore.errors.first_name" class="text-red-500 text-xs">{{ userStore.errors.first_name[0] }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Date of birth</label>
                                <br>
                                <input type="date" v-model="formData.birthday" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded"  />
                                <p v-if="yupErrors.birthday" class="text-red-500 text-xs">{{ yupErrors.birthday }}</p>
                                <p v-else-if="userStore.errors && userStore.errors.birthday" class="text-red-500 text-xs">{{ userStore.errors.birthday[0] }}</p>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Contact Number</label>
                                <input type="text" v-model="formData.contact_no" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <p v-if="yupErrors.contact_no" class="text-red-500 text-xs">{{ yupErrors.contact_no }}</p>
                                <p v-else-if="userStore.errors && userStore.errors.contact_no" class="text-red-500 text-xs">{{ userStore.errors.contact_no[0] }}</p>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Email</label>
                                <input type="text" v-model="formData.email" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <p v-if="yupErrors.email" class="text-red-500 text-xs">{{ yupErrors.email }}</p>
                                <p v-else-if="userStore.errors && userStore.errors.email" class="text-red-500 text-xs">{{ userStore.errors.email[0] }}</p>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Password</label>
                                <input type="password" v-model="formData.password" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <p v-if="yupErrors.password" class="text-red-500 text-xs">{{ yupErrors.password }}</p>
                                <p v-else-if="userStore.errors && userStore.errors.password" class="text-red-500 text-xs">{{ userStore.errors.password[0] }}</p>
                            </div>
                            <div>
                                <label class="text-primary text-sm font-medium">Confirm Password</label>
                                <input type="password" v-model="formData.password_confirmation" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <p v-if="yupErrors.password_confirmation" class="text-red-500 text-xs">{{ yupErrors.password_confirmation }}</p>
                                <p v-else-if="userStore.errors && userStore.errors.password" class="text-red-500 text-xs">{{ userStore.errors.password[0] }}</p>
                            </div>
                            <div>
                            <label class="text-primary text-sm font-medium">Role</label>
                            <select v-model="formData.role_id"  class="h-10 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                <option disabled value="">Please select role</option>
                                <option v-for="role in roleStore.rolesDropdown" :value="role.id">{{  role.name }}</option>
                            </select>
                            <p v-if="yupErrors.role_id" class="text-red-500 text-xs">{{ yupErrors.role_id }}</p>
                            <p v-else-if="userStore.errors && userStore.errors.role_id" class="text-red-500 text-xs">{{ userStore.errors.role_id[0] }}</p>
                        </div>
                            <button class="bg-secondary rounded-lg w-full py-1 text-white mt-5 text-sm md:w-20">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </Card>
</template>