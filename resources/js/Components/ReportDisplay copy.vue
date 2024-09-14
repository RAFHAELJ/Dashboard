<template>
    <v-container>
      <v-card>
        <v-card-title>Relatório de Registros</v-card-title>
        <v-card-text>
          <ul>
            <li v-for="(report, index) in reports" :key="index">
              {{ report }}
            </li>
          </ul>
        </v-card-text>
      </v-card>
    </v-container>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    reports: {
      type: Array,
      default: () => [],
    },
  });
  </script>
  
  <style scoped>
  ul {
    list-style-type: none;
    padding: 0;
  }
  li {
    padding: 5px 0;
  }
  </style>
  