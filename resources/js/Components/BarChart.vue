<template>
    <div class="chart-container">
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { Bar } from 'vue-chartjs';
  import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
  
  // Registrando os componentes necessários do Chart.js
  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);
  
  // Recebe os dados do gráfico como propriedade do componente pai
  const props = defineProps({
    chartData: {
      type: Object,
      required: true,
    },
    maxConsumptionGB: {
    type: Number,
    required: true,
  }

  });
  
  // Configurações de opções para o gráfico
  const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'top',
    },
    title: {
      display: true,
      text: 'Consumo vs Acessos por MAC',
    },
  },
  scales: {
    x: {
      title: {
        display: true,
        text: 'MAC',
      },
    },
    y: {
      beginAtZero: true,
      title: {
        display: true,
        text: 'Quantidade de Acessos',
      },
      ticks: {
        precision: 0, // Mostra apenas números inteiros para os acessos
        stepSize: 1,  // Incremento em números inteiros
      },
    },
    y1: {
      type: 'linear',
      position: 'right',
      title: {
        display: true,
        text: 'Consumo Total (GB)',
      },
      ticks: {
        callback: (value) => `${value} GB`, // Exibe o valor exato em GB
      },
      beginAtZero: true, // Começa do zero para facilitar a visualização
      suggestedMax: 100, // Utiliza o valor passado pelo pai
    },
  },
};

  </script>
  
  <style scoped>
  .chart-container {
    position: relative;
    height: 100%;
    width: 100%;
    max-height: 800px;
  }
  </style>
  