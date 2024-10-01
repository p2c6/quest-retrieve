<script setup>
import { ref } from "vue";

const collapse = ref(false);

const collapseFilter = () => collapse.value = !collapse.value;
</script>

<template>
    <div class="p-2 mt-2 md:mt-5">
        <div class="mx-6 flex gap-2">
<!-- 
        <div>
            <button class="px-2 py-1 border-[1.5px] border-secondary rounded text-white">
                <i class="pi pi-sliders-h text-secondary"></i>
            </button>
            <p class="text-secondary">Filter</p>
        </div>
        <div>
            <button class="px-2 py-1 border-[1.5px] border-secondary bg-secondary rounded text-white">
                <i class="pi pi-search text-white"></i>
            </button>
            <p class="text-secondary">Search</p>
        </div> -->
        <div class="w-screen">
            <div class="flex flex-col md:flex md:flex-row md:items-center gap-1">

                <input type="text" class="bg-white w-full p-2 rounded border-2 md:order-3 md:h-10 md:border-1" placeholder="I am looking for...">

                <!-- <button class="w-full border-[1.5px] border-secondary mt-2 rounded md:w-10 md:h-10 md:mt-0" @click="collapseFilter">
                    <div class="flex flex-row p-2 justify-center gap-1 md:flex-col">
                        <i class="pi pi-sliders-h text-secondary md:text-[1rem]"> </i> 
                        <p class="text-secondary text-[0.8rem] md:hidden">Filter</p>
                    </div>
                </button> -->
                

                <div :class="`${collapse ? 'block' : 'hidden'} bg-primary h-auto mt-2 p-5 rounded`" id="filterMobileContainer">
                    <div>
                        <div>   
                            <p class="text-white">Type</p>
                            <input type="text" class="bg-white w-full p-1 rounded" >
                        </div>
                        <div>
                            <p class="text-white">Location</p>
                            <input type="text" class="bg-white w-full p-1 rounded">
                        </div>
                    </div>
                </div>
                
                <button class="w-full bg-secondary mt-2 rounded md:w-10 md:h-10 md:mt-0">
                    <div class="flex flex-column p-2 justify-center gap-1 md:flex-col">
                        <i class="pi pi-search text-white md:text-[1rem]"> </i> 
                        <p class="text-white text-[0.8rem] md:hidden">Search</p>
                    </div>
                </button>
                
            </div>
            
        </div>
    </div>
    </div>
</template>