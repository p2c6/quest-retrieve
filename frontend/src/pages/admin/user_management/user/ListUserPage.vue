<script setup>
import Card from '@/components/Card.vue';
import { onBeforeMount, onBeforeUnmount, reactive, ref } from 'vue';
import { useUserStore } from '@/stores/user';
import DynamicTable from '@/components/DynamicTable.vue';

const userStore = useUserStore();

const resource = 'users'
const createRoute = { name: `${resource}.create` };
const editRoute = `${resource}.edit`

const columns = [
    {
        key: "profile.full_name",
        label: "Full Name",
        sortable: true
    },
    {
        key: "profile.birthday",
        label: "Birthday",
        sortable: true
    },
    {
        key: "email",
        label: "E-mail",
        sortable: true
    },
    {
        key: "profile.contact_no",
        label: "E-mail",
        sortable: true
    },
    {
        key: "actions",
        label: "Action",
    },
];

let typingTimer;
const typingDelay = 1000;

const search = async (keyword = '') => {
    userStore.keyword = keyword;

    clearTimeout(typingTimer);
    typingTimer = setTimeout(async () => {
        await userStore.getAllUsers();
    }, typingDelay);
};

const accessRelatedColumn = (parent) => {
    const segment =  parent.split(".")

    const lastItem = segment[segment.length - 1];

    return lastItem;
    
}

const sort = async (columnName) => {
    await userStore.sort(accessRelatedColumn(columnName));
    await userStore.getAllUsers(userStore.users.current_page || 1);
}

onBeforeMount(async() => {
    await userStore.getAllUsers();
})

onBeforeUnmount(() => {
    userStore.message = null;
    userStore.keyword = null;
    userStore.errors = null;
})


</script>

<template>
    <DynamicTable
        :resource="resource"
        :columns="columns" 
        :isLoading="userStore.isLoading"
        :on-delete="userStore.deleteUser"
        :on-search="search"
        :on-sort="sort"
        :data="userStore.users"
        :paginated-data="userStore.getAllUsers"
        :message="userStore.message"
        :errors="userStore.errors"
        :create-route="createRoute"
        :edit-route="editRoute"
    />
</template>