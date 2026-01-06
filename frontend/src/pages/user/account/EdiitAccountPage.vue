<script setup>
import Card from '@/components/Card.vue';
import { useUserStore } from '@/stores/user';
import { useFileUploadStore } from '@/stores/file-upload';
import { useAuthStore } from '@/stores/auth';
import { useProfileStore } from '@/stores/profile';
import { computed, onMounted, onUnmounted, reactive, ref } from 'vue';
import { RouterLink } from 'vue-router';
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

const userStore = useUserStore();
const authStore = useAuthStore();
const profileStore = useProfileStore();

const formData = reactive({
    id: '',
    email: '',
    first_name: '',
    last_name: '',
    contact_no: '',
    avatar: '',
    birthday: '',
    role_id: '',
});

const yupErrors = reactive({})

const updateUserProfile = async(formData) => {
    yupErrors.last_name = '';
    yupErrors.first_name = '';
    yupErrors.birthday = '';
    yupErrors.contact_no = '';
    userStore.errors = null;

    try {
        await schema.validate(formData, {abortEarly: false })
        profileStore.updateProfileDetails(formData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                yupErrors[err.path] = err.message;
            });
        }
    }
}

const fileInput = ref(null)
const previewUrl = ref(null)
const fileUploadStore = useFileUploadStore()

const currentUser = computed(() => authStore.user)


const triggerFileInput = () => {
  fileInput.value?.click()
}

const onFileChange = async(event) => {
  const file = event.target.files?.[0]
  if (!file) return

  // cleanup old preview
  if (previewUrl.value?.startsWith('blob:')) {
    URL.revokeObjectURL(previewUrl.value)
  }

  previewUrl.value = URL.createObjectURL(file)

  const fd = new FormData()
  fd.append('file', file)

  await fileUploadStore.uploadTemporaryFile(fd)

  formData.avatar = fileUploadStore.fileName;
}

let changePasswordSchema = yup.object({
    current_password: yup.string().required('current password is required.'),
    password: yup
        .string()
        .required('password is required.')
        .transform((value) => (value === '' ? undefined : value)) 
        .min(8, 'password is too short.')
        .max(15, 'password is too long.'),
    password_confirmation: yup
        .string()
        .when('password', {
            is: (val) => val && val.trim().length > 0,
            then: (changePasswordSchema) =>
            changePasswordSchema
                .required('password confirmation is required.')
                .oneOf([yup.ref('password')], 'Your passwords do not match.'),
            otherwise: (changePasswordSchema) => changePasswordSchema.notRequired(),
        }),
})

const passwordFormData = reactive({
    id: '',
    current_password: '',
    password: '',
    password_confirmation: '',
});


const changePasswordYupErrors = reactive({})

const updatePassword = async(passwordFormData) => {
    changePasswordYupErrors.current_password = '';
    changePasswordYupErrors.password = '';
    changePasswordYupErrors.password_confirmation = '';
    profileStore.errors = null;

    try {
        await changePasswordSchema.validate(passwordFormData, {abortEarly: false })
        await profileStore.updateProfilePassword(passwordFormData)
    } catch(validationError) {
        if (validationError.inner) {
            validationError.inner.forEach(err => {
                changePasswordYupErrors[err.path] = err.message;
            });
        }
    }
}

onMounted(async () => {
  await authStore.getUser()
  if (!currentUser.value) return

  const profile = currentUser.value.profile

  passwordFormData.id = currentUser.value.id

  formData.id = currentUser.value.id
  formData.email = currentUser.value.email
  formData.role_id = currentUser.value.role_id

  if (profile) {
    previewUrl.value = profile.avatar
      ? resolveAvatar(profile.avatar)
      : null

    formData.first_name = profile.first_name ?? ''
    formData.last_name = profile.last_name ?? ''
    formData.contact_no = profile.contact_no ?? ''
    formData.birthday = profile.birthday ?? ''
  }
})

onUnmounted(() => {
  if (previewUrl.value?.startsWith('blob:')) {
    URL.revokeObjectURL(previewUrl.value)
  }
  
  profileStore.message = null;
  profileStore.errors = null;
})

const nameInitial = computed(() => {
  const firstName = currentUser.value?.profile?.first_name
  return firstName ? firstName.charAt(0).toUpperCase() : ''
})

const resolveAvatar = (path) => {
  if (!path) return null
  return `${import.meta.env.VITE_API_URL}/storage/${path}`
}
</script>

<template>
        <div class="my-2 md:mt-5">
            <div class="container mx-auto grid grid-cols-1 place-items-start w-auto md:w-[620px]">
                <div v-if="profileStore.message" class="w-full bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md mb-5" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <svg class="w-6 h-6 text-teal-500 dark:text-white mr-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                            <p class="font-bold text-sm">Success</p>
                            <div class="text-xs flex gap-1">
                                {{ profileStore.message }}
                            </div>
                            </div>
                        </div>
                    </div>
                <Card class="p-5 flex flex-row mt-2">
                    <div class="overflow-x-auto w-full">
                        <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                            <div class="text-center md:text-left">
                                <p class="md:text-left text-primary font-medium">Account Details</p>
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
                                                <input
                                                    type="file"
                                                    ref="fileInput"
                                                    class="hidden"
                                                    accept="image/*"
                                                    @change="onFileChange"
                                                />

                                                <div
                                                    class="w-14 h-14 rounded-full text-lg font-medium bg-gray-400 flex items-center justify-center cursor-pointer overflow-hidden relative hover:opacity-70 hover:border-dashed"
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
                                                    {{ nameInitial }}
                                                </span>
                                                </div>
                                            </div>
                                    </div>
                                    <div>
                                            <div>
                                                <label class="text-primary text-sm font-medium">Last Name</label>
                                                <input type="text" v-model="formData.last_name" :class="`h-8 w-full border-[1.1px] border-${yupErrors.last_name || (userStore.errors && userStore.errors.last_name) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                                                <p v-if="yupErrors.last_name" class="text-red-500 text-xs">{{ yupErrors.last_name }}</p>
                                                <p v-else-if="userStore.errors && userStore.errors.last_name" class="text-red-500 text-xs">{{ userStore.errors.last_name[0] }}</p>
                                            </div>
                                            <div>
                                                <label class="text-primary text-sm font-medium">First Name</label>
                                                <input type="text" v-model="formData.first_name" :class="`h-8 w-full border-[1.1px] border-${yupErrors.first_name || (userStore.errors && userStore.errors.first_name) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                                                <p v-if="yupErrors.first_name" class="text-red-500 text-xs">{{ yupErrors.first_name }}</p>
                                                <p v-else-if="userStore.errors && userStore.errors.first_name" class="text-red-500 text-xs">{{ userStore.errors.first_name[0] }}</p>
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
            
            <div class="container my-2 mx-auto grid grid-cols-1 place-items-start w-auto md:w-[620px]">
                <Card class="p-5 flex flex-row mt-2">
                    <div class="overflow-x-auto w-full">
                        <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                            <div class="text-center md:text-left">
                                <p class="md:text-left text-primary font-medium">Settings</p>
                                <p class="text-tertiary md:text-left text-xs md:text-sm">Update your password.</p>
                            </div>
                        </div>
                        <div class="mt-5">
                            <form @submit.prevent="updatePassword(passwordFormData)">
                                <div>
                                    <div>
                                            <div>
                                                <label class="text-primary text-sm font-medium">Current Password</label>
                                                <input type="password" v-model="passwordFormData.current_password" :class="`h-8 w-full border-[1.1px] border-${changePasswordYupErrors.current_password || (profileStore.errors && profileStore.errors.current_password) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                                                <p v-if="changePasswordYupErrors.current_password" class="text-red-500 text-xs">{{ changePasswordYupErrors.current_password }}</p>
                                                <p v-else-if="profileStore.errors && profileStore.errors.current_password" class="text-red-500 text-xs">{{ profileStore.errors.current_password }}</p>
                                            </div>
                                            <div>
                                                <label class="text-primary text-sm font-medium">New Password</label>
                                                <input type="password" v-model="passwordFormData.password" :class="`h-8 w-full border-[1.1px] border-${changePasswordYupErrors.password || (profileStore.errors && profileStore.errors.password) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                                                <p v-if="changePasswordYupErrors.password" class="text-red-500 text-xs">{{ changePasswordYupErrors.password }}</p>
                                                <p v-else-if="profileStore.errors && profileStore.errors.password" class="text-red-500 text-xs">{{ profileStore.errors.password[0] }}</p>
                                            </div>
                                            <div>
                                                <label class="text-primary text-sm font-medium">Confirm Password</label>
                                                <input type="password" v-model="passwordFormData.password_confirmation" :class="`h-8 w-full border-[1.1px] border-${changePasswordYupErrors.password_confirmation || (profileStore.errors && profileStore.errors.password_confirmation) ? 'red-500' : 'primary' } mt-1 mb-1 p-2 rounded`">
                                                <p v-if="changePasswordYupErrors.password_confirmation" class="text-red-500 text-xs">{{ changePasswordYupErrors.password_confirmation }}</p>
                                                <p v-else-if="profileStore.errors && profileStore.errors.password_confirmation" class="text-red-500 text-xs">{{ profileStore.errors.password_confirmation[0] }}</p>
                                            </div>
                                    </div>
                                    <div>
                                </div>
                                    <button class="bg-secondary rounded-lg w-full py-1 text-white mt-5 text-sm md:w-20">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
</template>