<script setup>
import PieChart from '@/components/PieChart.vue';
import LineChart from '@/components/LineChart.vue';
import Card from '@/components/Card.vue';
import { onMounted } from "vue";
import { useModeratorDashboardStore } from '@/stores/moderator/dashboard';

const dashboardStore = useModeratorDashboardStore();
const pieLabels = ['Pending', 'Approved', 'Rejected', 'Completed'];
const backgroundColors = ['#e5eb36','#36eb78', '#eb3636', '#3694eb'];
const lineLabels = [
    'January','February','March','April','May','June',
    'July','August','September','October','November','December'
] ;
const datasetLabel = 'Monthly Posts'

onMounted(async() => {
    await dashboardStore.getPostLineData();
    await dashboardStore.getPostPieData();

    await dashboardStore.getTotalPostCount();
    await dashboardStore.getApprovedPostCount();
    await dashboardStore.getRejectedPostCount();
    await dashboardStore.getCompletedPostCount();
})

</script>

<template>
        <div class="flex flex-col mt-2 w-full">
            <div class="flex flex-row gap-3">
                <Card>
                    <div class="bg-neutral-primary-soft block p-4 rounded-base shadow-xs">
                        <a href="#">
                            <h5 class="mb-2 text-xl font-semibold tracking-tight text-heading text-blue-400">Total Post</h5>
                        </a>
                        <div class="flex flex-row gap-2 items-center">
                            <svg class="w-7 h-7 mb-3 text-body mt-4 text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M6 14h2m3 0h5M3 7v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Z"/>
                            </svg>
                            <h1 v-if="dashboardStore.isLoading">...</h1>
                            <h1 v-else class="font-bold text-4xl my-2 text-blue-400">{{ dashboardStore.totalPostCountData }}</h1>
                        </div>
                        <p class="text-body text-slate-400">The overall total number of posts</p>
                    </div>
                </Card>
                <Card>
                    <div class="bg-neutral-primary-soft block p-4 rounded-base shadow-xs">
                        <a href="#">
                            <h5 class="mb-2 text-xl font-semibold tracking-tight text-heading text-green-400">Total Approved Post</h5>
                        </a>
                        <div class="flex flex-row gap-2 items-center">
                            <svg class="w-7 h-7 mb-3 text-body mt-4 text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.032 12 1.984 1.984 4.96-4.96m4.55 5.272.893-.893a1.984 1.984 0 0 0 0-2.806l-.893-.893a1.984 1.984 0 0 1-.581-1.403V7.04a1.984 1.984 0 0 0-1.984-1.984h-1.262a1.983 1.983 0 0 1-1.403-.581l-.893-.893a1.984 1.984 0 0 0-2.806 0l-.893.893a1.984 1.984 0 0 1-1.403.581H7.04A1.984 1.984 0 0 0 5.055 7.04v1.262c0 .527-.209 1.031-.581 1.403l-.893.893a1.984 1.984 0 0 0 0 2.806l.893.893c.372.372.581.876.581 1.403v1.262a1.984 1.984 0 0 0 1.984 1.984h1.262c.527 0 1.031.209 1.403.581l.893.893a1.984 1.984 0 0 0 2.806 0l.893-.893a1.985 1.985 0 0 1 1.403-.581h1.262a1.984 1.984 0 0 0 1.984-1.984V15.7c0-.527.209-1.031.581-1.403Z"/>
                            </svg>
                            <h1 v-if="dashboardStore.isLoading">...</h1>
                            <h1 v-else class="font-bold text-4xl my-2 text-green-400">{{ dashboardStore.totalApprovedCountData }}</h1>
                        </div>
                        <p class="text-body text-slate-400">The overall total number of approved posts</p>
                    </div>
                </Card>
                <Card>
                    <div class="bg-neutral-primary-soft block p-4 rounded-base shadow-xs">
                        <a href="#">
                            <h5 class="mb-2 text-xl font-semibold tracking-tight text-heading text-red-400">Total Rejected Post</h5>
                        </a>
                        <div class="flex flex-row gap-2 items-center">
                            <svg class="w-7 h-7 text-red-400 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 9-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <h1 v-if="dashboardStore.isLoading">...</h1>
                            <h1 v-else class="font-bold text-4xl my-2 text-red-400">{{ dashboardStore.totalRejectedCountData }}</h1>
                        </div>
                        <p class="text-body text-slate-400">The overall total number of rejected posts</p>
                    </div>
                </Card>
                <Card>
                    <div class="bg-neutral-primary-soft block p-4 rounded-base shadow-xs">
                        <a href="#">
                            <h5 class="mb-2 text-xl font-semibold tracking-tight text-heading text-primary">Total Completed Post</h5>
                        </a>
                        <div class="flex flex-row gap-2 items-center">
                            <svg class="w-7 h-7 text-primary dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-6 7 2 2 4-4m-5-9v4h4V3h-4Z"/>
                            </svg>
                            <h1 v-if="dashboardStore.isLoading">...</h1>
                            <h1 v-else class="font-bold text-4xl my-2 text-primary">{{ dashboardStore.totalCompletedCountData }}</h1>
                        </div>
                        <p class="text-body text-slate-400">The overall total number of completed posts</p>
                    </div>
                </Card>
            </div>
            <div class="overflow-x-auto w-full">
                <div class="mt-2 p-2 flex flex-col md:flex-row gap-5">
                    <div class="text-md md:text-sm w-full">
                        <div class="text-center md:text-left mb-2">
                            <h1 class="text-primary font-bold">Summary</h1>
                            <p class="text-tertiary">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                        </div>
                        <Card class="p-2 mb-5 pb-15">
                            <div v-if="dashboardStore.isLoading" class="flex justify-around items-center h-72">
                                Loading...
                            </div>
                            <div v-else class="flex flex-col md:flex-row md:justify-around md:items-center md:gap-10 lg:gap-20">
                                <div class="w-full md:w-72 lg:w-[300px]">
                                    <LineChart 
                                        v-if="dashboardStore.postLineData?.length" 
                                        :data="dashboardStore.postLineData"
                                        :labels="lineLabels"
                                        :dataSetLabel="datasetLabel" 
                                    />
                                </div>
                                <div class="w-full md:w-40 lg:w-72">
                                    <PieChart 
                                        v-if="dashboardStore.postPieData?.length" 
                                        :data="dashboardStore.postPieData" 
                                        :labels="pieLabels"
                                        :backgroundColors="backgroundColors" 
                                    />
                                </div>
                            </div>
                        </Card>
                    </div>

                </div>
            </div>
        </div>
</template>