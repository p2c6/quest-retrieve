<script setup>
import Card from '@/components/Card.vue';
import { useRoleStore } from '@/stores/role';
import { useUserStore } from '@/stores/user';
import { useFileUploadStore } from '@/stores/file-upload';
import { useAuthStore } from '@/stores/auth';
import { useProfileStore } from '@/stores/profile';
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
const authStore = useAuthStore();
const profileStore = useProfileStore();

const user = ref({});


const formData = reactive({
    file: null,
    first_name: '',
    last_name: '',
    contact_no: '',
    avatar: '',
    birthday: '',
});

const yupErrors = reactive({})

const updateUserProfile = async(formData) => {
    profileStore.updateProfileDetails(formData)
    // yupErrors.last_name = '';
    // yupErrors.first_name = '';
    // yupErrors.birthday = '';
    // yupErrors.contact_no = '';
    // userStore.errors = null;

    // try {
    //     await schema.validate(formData, {abortEarly: false })
    //     userStore.updateUser(formData)
    // } catch(validationError) {
    //     if (validationError.inner) {
    //         validationError.inner.forEach(err => {
    //             yupErrors[err.path] = err.message;
    //         });
    //     }
    // }
}

const fileInput = ref(null)
const previewUrl = ref(null)
const fileUploadStore = useFileUploadStore();


const triggerFileInput = () => {
  fileInput.value.click()
}

const onFileChange = (event) => {
  const file = event.target.files[0]

  if (!file) return

  previewUrl.value = URL.createObjectURL(file)
  formData.file = file

  const fd = new FormData()
  fd.append('file', file)

  fileUploadStore.uploadTemporaryFile(fd)
}

onMounted(async() => {
    authStore.getUser();
    formData.last_name = authStore.user.profile.last_name;
    formData.first_name = authStore.user.profile.first_name;
    formData.contact_no = authStore.user.profile.contact_no;
    formData.birthday = authStore.user.profile.birthday;
});

onUnmounted(() => {
    user.value = {};
    userStore.errors = null;
})


</script>

<template>
        <div class="mt-5 md:mt-5">
            <div class="container mx-auto grid grid-cols-1 place-items-start w-auto md:w-[620px]">
                <Card class="p-5 flex flex-row mt-2">
                    <div class="overflow-x-auto w-full">
                        <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                            <div class="text-center md:text-left">
                                <p class="md:text-left text-primary font-medium">Account</p>
                                <p class="text-tertiary md:text-left text-xs md:text-sm">Update your account details.</p>
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
                            <form @submit.prevent="updateUserProfile(formData)">
                                <div>
                                    <div class="flex justify-center">
                                        <div class="flex flex-col items-center space-y-4">
                                                <!-- Hidden file input -->
                                                <input
                                                    type="file"
                                                    ref="fileInput"
                                                    class="hidden"
                                                    accept="image/*"
                                                    @change="onFileChange"
                                                />

                                                <!-- Circle preview / upload button -->
                                                <div
                                                class="w-14 h-14 rounded-full text-lg font-medium bg-gray-400 flex items-center justify-center cursor-pointer overflow-hidden relative hover:opacity-70"
                                                @click="triggerFileInput"
                                                >
                                                <img
                                                    v-if="previewUrl"
                                                    :src="previewUrl"
                                                    class="w-full h-full object-cover"
                                                    alt="Preview"
                                                />
                                                <span
                                                    v-else
                                                    class="text-gray-700 absolute"
                                                >
                                                    L
                                                </span>
                                                </div>
                                            </div>
                                    </div>
                                    <div>
                                            <div>
                                                <label class="text-primary text-sm font-medium">Last Name</label>
                                                <input type="text" v-model="formData.last_name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                            </div>
                                            <div>
                                                <label class="text-primary text-sm font-medium">First Name</label>
                                                <input type="text" v-model="formData.first_name" class="h-8 w-full border-[1.1px] border-primary mt-1 mb-1 p-2 rounded">
                                            </div>
                                    </div>
                                    <div>
                                        <label class="text-primary text-sm font-medium">Date of birth</label>
                                        <br>
                                        <input type="date" v-model="formData.birthday" :class="`h-8 w-full border-[1.1px] border-${yupErrors.birthday || (userStore.errors && userStore.errors.birthday) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`"  />
                                        <p v-if="yupErrors.birthday" class="text-red-500 text-xs">{{ yupErrors.birthday }}</p>
                                        <p v-else-if="userStore.errors && userStore.errors.birthday" class="text-red-500 text-xs">{{ userStore.errors.birthday[0] }}</p>
                                    </div>
                                    <div>
                                        <label class="text-primary text-sm font-medium">Contact Number</label>
                                        <input type="text" v-model="formData.contact_no" :class="`h-8 w-full border-[1.1px] border-${yupErrors.contact_no || (userStore.errors && userStore.errors.contact_no) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                                        <p v-if="yupErrors.contact_no" class="text-red-500 text-xs">{{ yupErrors.contact_no }}</p>
                                        <p v-else-if="userStore.errors && userStore.errors.contact_no" class="text-red-500 text-xs">{{ userStore.errors.contact_no[0] }}</p>
                                    </div>
                                    <div>
                                </div>
                                    <button class="bg-secondary rounded-lg w-full py-1 text-white mt-5 text-sm md:w-20">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
</template>