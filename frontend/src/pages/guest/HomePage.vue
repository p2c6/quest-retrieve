<script setup>
import Navbar from "@/components/Navbar.vue";
import Filter from "@/components/Filter.vue";
import CardList from "@/components/CardList.vue";
import Card from "@/components/Card.vue";
import NoItems from "@/components/NoItems.vue";
import Post from "@/components/Post.vue";

const datas = [
{
    id:1,
    title: 'Test 1'
},
{
    id:2,
    title: 'Test 2'
},
{
    id:3,
    title: 'Test 3'
},
{
    id:4,
    title: 'Test 4'
},
{
    id:5,
    title: 'Test 5'
},
{
    id:6,
    title: 'Test 6'
},
]
</script>

<template>
    <main>
        <div>
            <Filter />
            <CardList v-if="datas.length > 0">
                <Card v-for="data in datas" :key="data.id">
                    <Post />
                </Card>
            </CardList>
            <NoItems v-else />
        </div>
    </main>
</template>