<script setup>
import Card from '@/components/Card.vue';
import { onBeforeMount, onBeforeUnmount, reactive, ref } from 'vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import { useRoleStore } from '@/stores/role';

const roleStore = useRoleStore();

const isModalOpen = ref(false);
const roleId = ref(null);

const formData = reactive({
    'keyword': ''
})

let typingTimer;
const typingDelay = 1000;

const openDeleteRoleConfirmation = (id) => {
    isModalOpen.value = true;
    roleId.value = id;
}

const confirmDeleteRole = (id) => {
    roleStore.deleteRole(id)
    isModalOpen.value = false;
}

const closeModal = () => {
    isModalOpen.value = false;
}

const search = async() => {
    roleStore.keyword = formData.keyword;

    clearTimeout(typingTimer);

    typingTimer = setTimeout(async() => {
        await roleStore.getAllRoles();
    }, typingDelay)
}

onBeforeMount(async() => {
    await roleStore.getAllRoles();
})

onBeforeUnmount(() => {
    roleStore.message = null;
    roleStore.keyword = null;
})


</script>

<template>
    <Teleport to="#modal-container">
        <div :class="`fixed inset-0 z-20 flex items-center justify-center ${isModalOpen ? 'block' : 'hidden'}`">
            <div class="absolute inset-0 bg-slate-950 opacity-40" @click="closeModal"></div>
            <div id="modal-card" class="relative w-80 h-40 bg-white z-20 rounded-2xl">
                <div id="modal-header" class="w-full py-2">
                    <div class="flex justify-end mr-5">
                        <i class="pi pi-times text-gray-500 cursor-pointer" @click="closeModal"></i>
                    </div>

                </div>
                <div id="modal-body" class="flex items-center justify-center px-4">
                    <div class="flex-1">
                        <div class="flex flex-col justify-center gap-2">
                            <p class="font-semibold text-md text-primary">Delete role?</p>
                            <p class="text-xs text-gray-400">You are about to delete this role</p>
                            <div class="flex gap-1 mt-2">
                                <button class="w-36 h-8 bg-gray-200 rounded text-sm" @click="closeModal">Cancel</button>
                                <button class="w-36 h-8 bg-secondary rounded text-white text-sm" @click="confirmDeleteRole(roleId)">Yes,delete it</button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </Teleport>
    <Card class="p-5 flex flex-row mt-2  w-full">
        <div class="overflow-x-auto w-full">
            <div class="flex flex-col gap-2 justify-between items-center md:flex-row">
                <div class="text-center md:text-left">
                    <p class="text-primary font-medium">Role List</p>
                    <p class="text-tertiary text-xs md:text-sm">Listing of all roles.</p>
                </div>
                <div class="w-full md:w-16">
                    <RouterLink :to="{ name: 'users.create' }">
                    <button class="bg-secondary text-white px-2 py-1 rounded text-sm w-full">Create</button>
                    </RouterLink>
                </div>
            </div>
        </div>
    </Card>
</template>