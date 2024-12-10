<script setup>
import { ref } from 'vue';

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
        <div>
            <ul class="text-white">
                <li class="my-2">
                    <div class="list-item-parent">
                        <i class="pi pi-chart-bar"></i>
                        <h4 id="list-main-item">Dashboard</h4>
                    </div>
                </li>
                <li class="my-2">
                    <div class="list-item-parent"  @click="collapseSubItems('System Configuration')">
                        <i class="pi pi-cog"></i>
                        <h4 id="list-main-item">System Configuration</h4>
                        <i class="pi pi-angle-down text-xs ml-auto"></i>
                    </div>
                    <ul :class="`ml-8 ${systemConfigurationSubItems ? 'block' : 'hidden'}`">
                        <li>Category</li>
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
                    <ul :class="`ml-8 ${userManagementSubItems ? 'block' : 'hidden'}`">
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
</style>