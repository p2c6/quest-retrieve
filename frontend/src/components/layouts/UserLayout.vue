<script setup>
import { RouterView, useRoute } from 'vue-router';
import Navbar from "@/components/Navbar.vue";
import { computed, onBeforeMount, onMounted, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const store = useAuthStore();
const isLoading = ref(true);

const showNavbar = computed(() => {
    const excludedPaths = [
                            "/login", 
                            "/register", 
                            "/email-verification", 
                            "/verify-email", 
                            "/forgot-password",
                            "/reset-password",
                        ];

    return !excludedPaths.includes(route.path);
});

onBeforeMount(async() => {
    await store.getUser();
    isLoading.value = false;
})

</script>

<template>
    <div v-if="isLoading">
        Loading...
    </div>
    <div v-else>
        <Navbar v-if="showNavbar" />
        <RouterView />
    </div>
</template>