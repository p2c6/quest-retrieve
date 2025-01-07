<script setup>
import { useAuthStore } from '@/stores/auth';
import { onBeforeMount, onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import ROLE from '@/constants/user-role';

const authStore = useAuthStore();

const systemConfigurationSubItems = ref(false);
const userManagementSubItems = ref(false);

const collapseSubItems = (parent) => {
    if (parent === "System Configuration") {
        systemConfigurationSubItems.value = !systemConfigurationSubItems.value;
    }

    if (parent === "User Management") {
        userManagementSubItems.value =  !userManagementSubItems.value;
    }
}

</script>

<template>
        <div class="text-sm md:text-md">
            <ul class="text-white">
                <li class="my-2">
                    <RouterLink to="/">
                        <div class="list-item-parent">
                            <i class="pi pi-chart-bar"></i>
                            <h4 id="list-main-item">
                                    Dashboard
                            </h4>
                        </div>
                    </RouterLink>
                </li>
                <div v-if="authStore.user && authStore.user.role_id === ROLE.IS_ADMIN">
                    <li class="my-2">
                        <div class="list-item-parent"  @click="collapseSubItems('System Configuration')">
                            <i class="pi pi-cog"></i>
                            <h4 id="list-main-item">System Configuration</h4>
                            <i class="pi pi-angle-down text-xs ml-auto"></i>
                        </div>
                        <ul :class="`list-item-child ml-8 ${systemConfigurationSubItems ? 'block' : 'hidden'}`">
                            <RouterLink :to="{name: 'category.list'}">
                                <li>
                                    Category
                                </li>
                            </RouterLink> 
                            <RouterLink :to="{name: 'subcategory.list'}">
                                <li>
                                    Subcategory
                                </li>
                            </RouterLink> 
                        </ul>
                    </li>
                    <li class="my-2">
                        <div class="list-item-parent" @click="collapseSubItems('User Management')">
                            <i class="pi pi-user"></i>
                            <h4>User Management</h4>
                            <i class="pi pi-angle-down text-xs ml-auto"></i>
                        </div>
                        <ul :class="`list-item-child ml-8 ${userManagementSubItems ? 'block' : 'hidden'}`">
                            <RouterLink :to="{name: 'role.list'}">
                                <li>
                                    Roles
                                </li>
                            </RouterLink> 
                            <RouterLink :to="{name: 'users.list'}">
                                <li>
                                    Users
                                </li>
                            </RouterLink> 
                        </ul>
                    </li>
                </div>
                <div v-if="authStore.user && authStore.user.role_id === ROLE.IS_MODERATOR">
                    <RouterLink :to="{name: 'moderator.posts.list'}">
                        <div class="list-item-parent">
                            <i class="pi pi-receipt"></i>
                            <h4 id="list-main-item">
                                    Posts
                            </h4>
                        </div>
                    </RouterLink> 
                </div>
            </ul>
        </div>
</template>

<style lang="css" scoped>
    .list-item-parent {
        @apply p-2 w-full flex items-center gap-2 hover:bg-blue-800 rounded cursor-pointer;
    }

    .list-item-child li {
        @apply my-2
    }

    .list-item-child li:hover {
        @apply text-blue-400 cursor-pointer;
    }
</style>