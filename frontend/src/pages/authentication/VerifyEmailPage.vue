<script setup>
import { useAuthStore } from '@/stores/auth';
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const store = useAuthStore();

onMounted(() => {
    const verificationUrl = route.query.verification_url ?? null;
    
    if (store.user && verificationUrl !== null) {
        store.verifyEmail(verificationUrl.replace("http://localhost:8000/", ""));
    } 
})
</script>

<template>

  </template>