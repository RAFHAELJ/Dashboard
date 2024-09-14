<template>
    <v-app-bar flat color="primary" dark>
      <v-toolbar-title>Filtros do Relatório</v-toolbar-title>
  
      <!-- Campo de filtro por nome -->
      <v-text-field
        v-model="filters.name"
        label="Buscar por nome"
        prepend-icon="mdi-account-search"
        class="mx-3"
      ></v-text-field>
  
      <!-- Campo de data de com calendário -->
      <v-menu
        v-model="menuFrom"
        :close-on-content-click="false"
        transition="scale-transition"
        offset-y
        min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="filters.dateFrom"
            label="Data de"
            prepend-icon="mdi-calendar"
            readonly
            v-bind="attrs"
            @click="menuFrom = true"
          ></v-text-field>
        </template>
        <v-date-picker v-model="filters.dateFrom" @input="menuFrom = false"></v-date-picker>
      </v-menu>
  
      <!-- Campo de data até com calendário -->
      <v-menu
        v-model="menuTo"
        :close-on-content-click="false"
        transition="scale-transition"
        offset-y
        min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="filters.dateTo"
            label="Data até"
            prepend-icon="mdi-calendar"
            readonly
            v-bind="attrs"
            @click="menuTo = true"
          ></v-text-field>
        </template>
        <v-date-picker v-model="filters.dateTo" @input="menuTo = false"></v-date-picker>
      </v-menu>
  
      <!-- Seleção de região -->
      <v-select
        v-model="filters.region"
        :items="regions"
        label="Selecione uma Região"
        prepend-icon="mdi-map-marker"
        class="mx-3"
      ></v-select>
  
      <!-- Botões de Imprimir e Exportar -->
      <v-menu offset-y>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item @click="printReport">
            <v-icon left>mdi-printer</v-icon>
            <v-list-item-title>Imprimir</v-list-item-title>
          </v-list-item>
          <v-list-item @click="exportReport">
            <v-icon left>mdi-file-export</v-icon>
            <v-list-item-title>Exportar</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  // Filtros iniciais
  const filters = ref({
    name: '',
    dateFrom: '',
    dateTo: '',
    region: null,
  });
  
  // Opções de regiões
  const regions = ['Norte', 'Sul', 'Leste', 'Oeste'];
  
  // Controle dos menus de calendário
  const menuFrom = ref(false);
  const menuTo = ref(false);
  
  // Funções para exportar e imprimir
  const printReport = () => {
    window.print();
  };
  
  const exportReport = () => {
    alert('Exportação de relatório!');
  };
  </script>
  
  <style scoped>
  .v-app-bar {
    position: sticky;
    top: 0;
    z-index: 100;
  }
  </style>
  