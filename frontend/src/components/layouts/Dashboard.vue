<script setup>
import Card from '@/components/Card.vue';
import home from "@/assets/home.png";
import explore from "@/assets/explore.png";
import PieChart from '@/components/PieChart.vue';
import LineChart from '@/components/LineChart.vue';


</script>

<template>
    <main>
        <div class="container mx-auto grid grid-cols-1">
            <div class="mt-5 p-2">
                <Card class="p-2 flex flex-row">
                    <img :src="home" alt="" class="order-2 w-40 md:w-72 order-1">
                    <div class="order-2 mt-5">
                        <p class="text-center md:text-left text-primary font-medium">Welcome back John Doe!</p>
                        <p class="text-tertiary text-center md:text-left text-xs md:text-sm">A streamlined dashboard providing real-time insights, key performance metrics, and intuitive visualizations to help you monitor and manage your data effortlessly. Customize widgets, track progress, and make informed decisions with a user-friendly interface designed for efficiency and clarity. Stay updated and in control at a glance.</p>
                    </div>
                </Card>
            </div>
            <div class="mt-2 p-2 flex flex-col md:flex-row gap-5">
                <Card class="p-2 mb-5 text-white flex flex-col justify-center items-center gap-5 w-full md:w-96" bg="bg-primary">
                    <img :src="explore" alt="" class="w-40 md:w-60  ">
                    <div class="text-center text-xs md:text-xs">
                        <h1 class="text-white font-bold">Check Records</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut posuere hendrerit risus, eget ullamcorper tortor.</p>
                        <button class=" mt-2 mb-2 px-4 py-2 bg-secondary rounded-full">Explore</button>
                    </div>
                </Card>
                
                <div class="text-md md:text-sm w-full">
                    <div class="text-center md:text-left mb-2">
                        <h1 class="text-primary font-bold">Summary</h1>
                        <p class="text-tertiary">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                    </div>
                    <Card class="p-2 mb-5 pb-15 flex flex-col md:flex-row md:justify-around md:items-center md:gap-10 lg:gap-20">
                        <div class="w-full md:w-72 lg:w-[300px]">
                            <LineChart />
                        </div>
                        <div class="w-full md:w-40 lg:w-72">
                            <PieChart />
                        </div>
                    </Card>
                    <!-- <Card>
                        <div class="w-auto">
                            <PieChart />
                        </div>
                    </Card> -->
                </div>

            </div>
        </div>
    </main>
</template>