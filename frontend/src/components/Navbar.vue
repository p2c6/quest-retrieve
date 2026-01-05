<script setup>
import logo from "@/assets/qr-logo.png";
import { useAuthStore } from "@/stores/auth";
import { ref } from "vue";
import { RouterLink } from "vue-router";

const collapse = ref(true);
const store = useAuthStore();

const collapseNav = () => collapse.value = !collapse.value;



</script>

<template>
    <div>
        <div class="bg-white">
            <div class="mx-6 flex items-center gap-4 p-2">
                <button class="mt-2 border-[1.7px] rounded border-indigo-800 px-2 py-1 md:hidden" @click="collapseNav" id="btnCollapseNav">
                    <i class="text-indigo-800 pi pi-bars"></i>
                </button>
                <RouterLink :to="{name: 'home'}">
                    <img :src="logo" alt="QuestRetrieve Logo" width="150px">
                </RouterLink>
            </div>
        </div>
        <div :class="`bg-indigo-800`">
                <div :class="`${collapse ? 'block' : 'hidden'} mx-6 text-white p-2 gap-1 md:flex flex-row justify-between`" v-if="store.user?.email_verified_at && store.user?.profile?.first_name">
                    <div class="flex flex-col md:flex-row gap-2">
                        <div>
                            Home
                        </div>
                        <div>
                            <RouterLink :to="{name: 'posts.list'}">
                                Posts
                            </RouterLink>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row justify-end gap-2">
                        
                        <RouterLink :to="{name: 'account.edit'}">
                            <div class="hover:cursor-pointer">
                                {{ store.user?.profile?.first_name }}  {{ store.user?.profile?.last_name }}
                            </div>
                        </RouterLink>
                        <div class="hover:cursor-pointer" @click="store.logout">
                            Logout
                        </div>
                    </div>
                </div>
                <div :class="`mx-6 text-white flex flex-col p-2 gap-2 ${collapse ? 'block' : 'hidden'} md:flex md:flex-row md:justify-end`" v-else>
                    <div>
                        <RouterLink :to="{name: 'login'}">
                            Login
                        </RouterLink>
                    </div>
                    <div>
                        <RouterLink :to="{name: 'register'}">
                            Register
                        </RouterLink>
                    </div>
                </div>
            
        </div>
    </div>
</template>