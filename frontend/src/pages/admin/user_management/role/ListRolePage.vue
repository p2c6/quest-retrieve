<script setup>
import { onBeforeMount, onBeforeUnmount, reactive } from 'vue';
import { useRoleStore } from '@/stores/role';
import DynamicTable from '@/components/DynamicTable.vue';

const roleStore = useRoleStore();

const resource = 'role'
const createRoute = { name: `${resource}.create` };
const editRoute = `${resource}.edit`

const columns = [
    {
        key: "name",
        label: "Role Name",
        sortable: true
    },
    {
        key: "actions",
        label: "Action",
    },
];

let typingTimer;
const typingDelay = 1000;

const formData = reactive({
    'keyword': ''
})

const search = async (keyword = '') => {
    roleStore.keyword = keyword;

    clearTimeout(typingTimer);
    typingTimer = setTimeout(async () => {
        await roleStore.getAllRoles();
    }, typingDelay);
};

const accessRelatedColumn = (parent) => {
    const segment =  parent.split(".")

    const lastItem = segment[segment.length - 1];

    return lastItem;
    
}

const sort = async (columnName) => {
    await roleStore.sort(accessRelatedColumn(columnName));
    await roleStore.getAllRoles(roleStore.roles.current_page || 1);
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
    <DynamicTable
        :resource="resource"
        :columns="columns" 
        :isLoading="roleStore.isLoading"
        :on-delete="roleStore.deleteRole"
        :on-search="search"
        :on-sort="sort"
        :data="roleStore.roles"
        :paginated-data="roleStore.getAllRoles"
        :message="roleStore.message"
        :errors="roleStore.errors"
        :create-route="createRoute"
        :edit-route="editRoute"
    />
</template>