<template>
  <VuePieChart :chart-data="chartData" />
</template>

<script setup>
import { reactive } from 'vue';
import { PieChart as VuePieChart } from 'vue-chart-3';
import { Chart, PieController, ArcElement, Tooltip, Legend } from 'chart.js';

// Register necessary chart components
Chart.register(PieController, ArcElement, Tooltip, Legend);

const prop = defineProps({
    data: Array
});

const chartData = reactive({
  labels: ['Verified', 'Not Yet Verified'],
  datasets: [
    {
      data: prop.data,
      backgroundColor: ['#36A2EB', '#FFCE56'],
    },
  ],
});
</script>
