
<template>
    <v-card :elevation="3" class="d-flex align-center fixed-card" outlined>
      <v-card-title class="d-flex align-center justify-center">
        <v-icon :color="color" class="mr-2">{{ icon }}</v-icon>
        <span class="title">{{ title }}</span>
      </v-card-title>
      <v-card-subtitle class="text-center text-h5">
        {{ value }}
      </v-card-subtitle>
    </v-card>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    title: String,
    icon: String,
    value: [String, Number],
    color: {
      type: String,
      default: '#42A5F5'
    }
  });
  </script>
  
  <style scoped>
  .fixed-card {
    padding: 10px;
    text-align: center;
    background-color: #f5f5f5;
  }
  
  .title {
    font-weight: bold;
    font-size: 16px;
  }
  </style>
  