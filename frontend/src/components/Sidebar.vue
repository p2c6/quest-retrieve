<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

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
                    <div class="list-item-parent">
                        <i class="pi pi-chart-bar"></i>
                        <h4 id="list-main-item">
                            <RouterLink :to="{name: 'dashboard'}">
                                Dashboard
                            </RouterLink>
                        </h4>
                    </div>
                </li>
                <li class="my-2">
                    <div class="list-item-parent"  @click="collapseSubItems('System Configuration')">
                        <i class="pi pi-cog"></i>
                        <h4 id="list-main-item">System Configuration</h4>
                        <i class="pi pi-angle-down text-xs ml-auto"></i>
                    </div>
                    <ul :class="`list-item-child ml-8 ${systemConfigurationSubItems ? 'block' : 'hidden'}`">
                        <li>
                            <RouterLink :to="{name: 'category.list'}">
                                Category
                            </RouterLink> 
                        </li>
                        <li>Roles</li>
                        <li>Subcategory</li>
                    </ul>
                </li>
                <li class="my-2">
                    <div class="list-item-parent" @click="collapseSubItems('User Management')">
                        <i class="pi pi-user"></i>
                        <h4>User Management</h4>
                        <i class="pi pi-angle-down text-xs ml-auto"></i>
                    </div>
                    <ul :class="`list-item-child ml-8 ${userManagementSubItems ? 'block' : 'hidden'}`">
                        <li>Roles</li>
                        <li>Users</li>
                    </ul>
                </li>
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