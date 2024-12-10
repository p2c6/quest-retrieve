<script setup>
import { RouterView } from 'vue-router';
import Navbar from "@/components/Navbar.vue";
import { computed, onBeforeMount, onMounted, onUnmounted, ref, Teleport, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import Sidebar from '@/components/Sidebar.vue';
import logo from "@/assets/qr-logo.png";

const store = useAuthStore();
const isLoading = ref(true);
const screenWidth = ref(window.innerWidth);

const updateScreenWidth = () => {
    screenWidth.value = window.innerWidth;
};

const collapse = ref(false);

const collapseSideNav = () => collapse.value = !collapse.value

const isDropdownOpen = ref(false);

const collapseDropDown = () => isDropdownOpen.value = !isDropdownOpen.value

onBeforeMount(async() => {
    await store.getUser();
    isLoading.value = false;
})

onMounted(() => {
    window.addEventListener('resize', updateScreenWidth);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateScreenWidth);
})

const isMobile = computed(() => {
    return screenWidth.value < 768
})

</script>

<template>
    <div v-if="isLoading">
        Loading...
    </div>
    <div v-else>
        <div class="flex flex-col min-h-screen md:flex-row">
            <div v-if="!isMobile">
                <div :class="`w-full h-full md:w-64 bg-primary text-white p-4 ${collapse ? 'block' : 'hidden'} md:block`">
                    <Sidebar />
                </div>
            </div>

            <Teleport to="#sidebar-mobile" v-if="isMobile">
                <div :class="`w-full md:w-64 bg-primary text-white p-4 ${collapse ? 'block' : 'hidden'} md:block`">
                    <Sidebar />
                </div>
            </Teleport>
    
            <div class="flex-1 bg-gray-100">

                <div class="bg-white text-white p-4">
                    <div class="flex justify-between items-center">

                        <div class="block mt-2 md:hidden" @click="collapseSideNav">
                            <i class="text-indigo-800 pi pi-bars"></i> 
                        </div>

                        <img :src="logo" alt="QuestRetrieve Logo" width="150px">

                        <div class="relative flex justify-evenly items-center gap-1 text-xs w-48 md:w-50 mt-2 md:text-md">
                            <div class="flex items-center gap-3" @click="collapseDropDown">
                                <img src="https://i.pravatar.cc/150?img=11" alt="" class="w-5 h-5 rounded-full">
                                <p class="text-gray-400 font-semibold cursor-pointer hidden md:block"> John Doe </p>
                                <i class="text-secondary pi pi-angle-down px-1"></i>
                            </div>

                            <div :class="`absolute top-10 -right-3 md:right-2 ${isDropdownOpen ? 'block' : 'hidden'}`">
                                <div class="px-16 bg-white h-auto text-black shadow-2xl flex flex-col gap-1">
                                        <div class="p-2">Account</div>
                                        <div class="p-2">Account</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="sidebar-mobile"></div>
                
                <div class="mt-5">
                    <div class="px-5">
                        <RouterView />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>