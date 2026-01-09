<template>
      <LineChart :chart-data="chartData" :options="chartOptions" />
  </template>
  
  <script setup>
  import { reactive } from 'vue';
  import { LineChart } from 'vue-chart-3';
  import { Chart, LineController, LineElement, PointElement, CategoryScale, LinearScale, Title, Tooltip, Legend } from 'chart.js';
  
  // Register necessary components
  Chart.register(LineController, LineElement, PointElement, CategoryScale, LinearScale, Title, Tooltip, Legend);
  
  const chartData = reactive({
    labels: [
      'January', 
      'February', 
      'March', 
      'April', 
      'May', 
      'June', 
      'July', 
      'August', 
      'September', 
      'October', 
      'November', 
      'December'
    ],
    datasets: [
      {
        label: 'Registered Users',
        data: [10, 50, 25, 70, 40],
        borderColor: '#42A5F5',
        backgroundColor: 'rgba(66, 165, 245, 0.2)',
        fill: true,
      },
    ],
  });
  
  const chartOptions = reactive({
    responsive: true,
    maintainAspectRatio: true, // Ensures the chart maintains its aspect ratio
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
  