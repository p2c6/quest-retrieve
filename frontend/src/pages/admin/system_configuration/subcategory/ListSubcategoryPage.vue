<script setup>
import { onBeforeMount, onBeforeUnmount} from 'vue';
import { useSubcategoryStore } from '@/stores/subcategory';
import DynamicTable from '@/components/DynamicTable.vue';

const subcategoryStore = useSubcategoryStore();

const createRoute = { name: 'category.create' };
const editRoute = 'category.edit'

const columns = [
    {
        key: "category.name",
        label: "Category Name",
        sortable: true
    },
    {
        key: "name",
        label: "Subcategory Name",
        sortable: true
    },
    {
        key: "actions",
        label: "Action",
    },
];

const search = async (keyword = '') => {
    subcategoryStore.keyword = keyword;

    clearTimeout(typingTimer);
    typingTimer = setTimeout(async () => {
        await subcategoryStore.getAllSubcategories();
    }, typingDelay);
};

const sort = async (columnName) => {
    await subcategoryStore.sort(columnName);
    await subcategoryStore.getAllSubcategories(subcategoryStore.subcategories.current_page || 1);
}

onBeforeMount(async() => {
    await subcategoryStore.getAllSubcategories();
})

onBeforeUnmount(() => {
    subcategoryStore.message = null;
    subcategoryStore.keyword = null;
})

</script>

<template>
    <DynamicTable
        :columns="columns" 
        :isLoading="subcategoryStore.isLoading"
        :on-delete="subcategoryStore.deleteSubcategory"
        :on-search="search"
        :on-sort="sort"
        :data="subcategoryStore.subcategories"
        :paginated-data="subcategoryStore.getAllSubcategories"
        :message="subcategoryStore.message"
        :errors="subcategoryStore.errors"
        :create-route="createRoute"
        :edit-route="editRoute"
    />
</template>