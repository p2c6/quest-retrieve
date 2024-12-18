<script setup>
import Card from '@/components/Card.vue';
import { onBeforeMount, onBeforeUnmount, reactive, ref } from 'vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import { useCategoryStore } from '@/stores/category';

const categoryStore = useCategoryStore();

const isModalOpen = ref(false);
const categoryId = ref(null);

const formData = reactive({
    'keyword': ''
})

let typingTimer;
const typingDelay = 1000;

const openDeleteCategoryConfirmation = (id) => {
    isModalOpen.value = true;
    categoryId.value = id;
}

const confirmDeleteCategory = (id) => {
    categoryStore.deleteCategory(id)
    isModalOpen.value = false;
}

const closeModal = () => {
    isModalOpen.value = false;
}

const search = async() => {
    categoryStore.keyword = formData.keyword;
    clearTimeout(typingTimer);
    typingTimer = setTimeout(async() => {
        await categoryStore.getAllCategories();
    }, typingDelay)
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
    
</template>