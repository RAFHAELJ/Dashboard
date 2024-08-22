<template>
    <v-col :key="card.id" cols="12" sm="6" md="4">
      <v-card class="d-card" :elevation="isDragging ? 12 : 2" v-resize="handleResize">
        <v-card-title>
          <v-icon>{{ card.icon }}</v-icon>
          <span class="ms-2">{{ card.title }}</span>
        </v-card-title>
        <v-card-subtitle class="text-h4">
          <div v-if="card.type === 'Texto'">{{ card.content }}</div>
          <div v-else-if="card.type === 'Gráfico'">
            <v-chart :options="card.chartOptions"></v-chart>
          </div>
        </v-card-subtitle>
        <v-card-actions>
          <v-btn icon @click="$emit('remove', card.id)">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  import VChart from 'vue-echarts';
  import * as echarts from 'echarts/core';
  import { CanvasRenderer } from 'echarts/renderers';
  import { LineChart, BarChart } from 'echarts/charts';
  import { TooltipComponent, GridComponent } from 'echarts/components';
  
  // Certifique-se de que o renderizador e os componentes do gráfico sejam usados
  echarts.use([CanvasRenderer, LineChart, BarChart, TooltipComponent, GridComponent]);
  
  const props = defineProps({
    card: Object,
    isDragging: Boolean,
  });
  
  function handleResize() {
    // Aqui você pode adicionar lógica para lidar com redimensionamento,
    // como re-renderizar o gráfico, por exemplo
  }
  </script>
  
  <style scoped>
  .d-card {
    transition: transform 0.3s ease;
  }
  
  .d-card:hover {
    transform: perspective(1000px) rotateY(15deg) rotateX(15deg);
  }
  </style>
  