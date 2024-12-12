<script setup>
import { RouterView, useRoute } from 'vue-router'
const route = useRoute();

</script>

<template>
  <div id="modal-container" class="relative"></div>
  <RouterView />
</template>

<style>
body {
  background-color: #F0F0F0;
}
</style>
