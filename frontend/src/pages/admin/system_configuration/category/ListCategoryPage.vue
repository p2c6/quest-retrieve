<script setup>
import { onBeforeMount, onBeforeUnmount } from 'vue';
import { useCategoryStore } from '@/stores/category';
import DynamicTable from '@/components/DynamicTable.vue';

const categoryStore = useCategoryStore();

const createRoute = { name: 'category.create' };
const editRoute = 'category.edit'
const resource = 'Category'

const columns = [
    {
        key: "name",
        label: "Category Name",
        sortable: true
    },
    {
        key: "actions",
        label: "Action",
    },
];

const search = async (keyword = '') => {
    categoryStore.keyword = keyword;

    clearTimeout(typingTimer);
    typingTimer = setTimeout(async () => {
        await categoryStore.getAllCategories();
    }, typingDelay);
};

const sort = async (columnName) => {
    await categoryStore.sort(columnName);
    await categoryStore.getAllCategories(categoryStore.categories.current_page || 1);
}

onBeforeMount(async() => {
    await categoryStore.getAllCategories();
})

onBeforeUnmount(() => {
    categoryStore.message = null;
    categoryStore.keyword = null;
})

</script>

<template>
    <DynamicTable
        :resource="resource"
        :columns="columns" 
        :isLoading="categoryStore.isLoading"
        :on-delete="categoryStore.deleteCategory"
        :on-search="search"
        :on-sort="sort"
        :data="categoryStore.categories"
        :paginated-data="categoryStore.getAllCategories"
        :message="categoryStore.message"
        :errors="categoryStore.errors"
        :create-route="createRoute"
        :edit-route="editRoute"
    />
</template>