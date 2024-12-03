<script setup>
import { RouterView, useRoute } from 'vue-router';
import Navbar from "@/components/Navbar.vue";
import { computed, onBeforeMount, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const store = useAuthStore();

const showNavbar = computed(() => {
    const excludedPaths = ["/login", "/register"];
    return !excludedPaths.includes(route.path);
});

onBeforeMount(async() => {
    await store.getUser();
})

</script>

<template>
    <div v-if="store.isLoading">
        Loading...
    </div>
    <div v-else>
        <Navbar v-if="showNavbar" />
        <RouterView />
    </div>
</template>