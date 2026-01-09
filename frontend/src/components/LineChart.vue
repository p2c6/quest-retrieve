<template>
      <VueLineChart  :chart-data="chartData" :options="chartOptions" />
  </template>
  
  <script setup>
  import { computed, reactive } from 'vue';
  import { LineChart as VueLineChart } from 'vue-chart-3';
  import { Chart, LineController, LineElement, PointElement, CategoryScale, LinearScale, Title, Tooltip, Legend } from 'chart.js';
  
  
  const props = defineProps({
      data: Array,
      labels: Array,
      dataSetLabel: String,
  });

  // Register necessary components
  Chart.register(LineController, LineElement, PointElement, CategoryScale, LinearScale, Title, Tooltip, Legend);
  
  const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
      {
        label: props.dataSetLabel,
        data: props.data ?? [],
        borderColor: '#42A5F5',
        backgroundColor: 'rgba(66, 165, 245, 0.2)',
        fill: true,
      },
    ],
  }));
  
  const chartOptions = reactive({
    responsive: true,
    maintainAspectRatio: true, // Ensures the chart maintains its aspect ratio
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          precision: 0,      
          stepSize: 1,      
          callback: (value) => Math.round(value),
        },
      },
    },
    aspectRatio: 1, // Width is twice the height (adjust as needed)
    plugins: {
      legend: {
        display: true,
      },
      tooltip: {
        callbacks: {
          label: (tooltipItem) => `Value: ${tooltipItem.raw}`,
        },
      },
    },
  });
  </script>
  
  <style scoped>
  /* Optional: Custom styles if needed */
  </style>
  