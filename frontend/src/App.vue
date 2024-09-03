<script setup>
import { RouterLink, RouterView } from 'vue-router'
import Navbar from './components/Navbar.vue';
</script>

<template>
  <Navbar />
  <RouterView />
</template>

<style>
body {
  background-color: #F0F0F0;
}
</style>
