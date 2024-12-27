<script setup>
import Navbar from "@/components/Navbar.vue";
import Filter from "@/components/Filter.vue";
import CardList from "@/components/CardList.vue";
import Card from "@/components/Card.vue";
import NoItems from "@/components/NoItems.vue";
import Post from "@/components/Post.vue";

</script>

<template>
    <main>
        <div class="mx-8 mt-2">
            <RouterView />
        </div>
    </main>
</template>