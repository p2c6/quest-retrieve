<script setup>
import { RouterView, useRoute } from 'vue-router'
import Navbar from './components/Navbar.vue';
const route = useRoute();

</script>

<template>
  <!-- <Navbar v-if="route.path !== '/login' && route.path !== '/register'" /> -->
  <RouterView />
</template>

<style>
body {
  background-color: #F0F0F0;
}
</style>
