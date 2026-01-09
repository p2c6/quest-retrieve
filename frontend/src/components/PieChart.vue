<template>
  <VuePieChart :chart-data="chartData" />
</template>

<script setup>
import { reactive } from 'vue';
import { PieChart as VuePieChart } from 'vue-chart-3';
import { Chart, PieController, ArcElement, Tooltip, Legend } from 'chart.js';

// Register necessary chart components
Chart.register(PieController, ArcElement, Tooltip, Legend);

const props = defineProps({
    data: Array,
    labels: Array,
    backgroundColors: Array,
});

const chartData = reactive({
  labels: props.labels,
  datasets: [
    {
      data: props.data,
      backgroundColor: props.backgroundColors,
    },
  ],
});
</script>
