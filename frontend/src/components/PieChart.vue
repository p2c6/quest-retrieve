<template>
  <PieChart :chart-data="chartData" />
</template>

<script setup>
import { reactive } from 'vue';
import { PieChart } from 'vue-chart-3';
import { Chart, PieController, ArcElement, Tooltip, Legend } from 'chart.js';

// Register necessary chart components
Chart.register(PieController, ArcElement, Tooltip, Legend);

const chartData = reactive({
  labels: ['Red', 'Blue', 'Yellow'],
  datasets: [
    {
      data: [10, 20, 30],
      backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
    },
  ],
});
</script>
