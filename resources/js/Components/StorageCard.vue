<template>
    <v-card :elevation="2" class="d-card storage-card">
      <v-card-title>
        <v-icon left>mdi-database</v-icon> Espaço da Storage
      </v-card-title>
      <v-card-subtitle class="text-h4">{{ usedPercentage.toFixed(1) }}% Usado</v-card-subtitle>
      <v-card-text>
        <v-chart :option="chartOptions" autoresize style="height: 150px; width: 100%;"></v-chart>
      </v-card-text>
      <v-card-actions>
        <p>Total: {{ formatSize(total) }}</p>
        <p>Usado: {{ formatSize(used) }}</p>
      </v-card-actions>
    </v-card>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import VChart from 'vue-echarts';
  
  const props = defineProps({
    total: Number,
    used: Number,
    usedPercentage: Number,
  });
  
  const chartOptions = computed(() => ({
    series: [
      {
        type: 'pie',
        radius: ['60%', '80%'],
        avoidLabelOverlap: false,
        label: { show: false },
        data: [
          { value: props.usedPercentage, name: 'Usado' },
          { value: 100 - props.usedPercentage, name: 'Livre' },
        ],
        color: ['#FF6384', '#36A2EB']
      }
    ]
  }));
  
  function formatSize(bytes) {
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) return '0 Byte';
    const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
  }
  </script>
  
  <style scoped>
  .storage-card {
    transition: transform 0.3s ease;
  }
  
  .storage-card:hover {
    transform: perspective(1000px) rotateY(10deg) rotateX(10deg);
  }
  </style>
  