<template>
    <v-card>
      <v-card-title>Pré-visualização</v-card-title>
      <v-card-text>
        <div class="preview-area" v-for="(element, index) in elements" :key="index" :style="elementStyle(element)">
          <!-- Exibe a imagem, texto, cor, etc. -->
          <img v-if="element.image" :src="element.image" />
          <p v-if="element.text">{{ element.text }}</p>
          <!-- Outros elementos a serem exibidos -->
        </div>
      </v-card-text>
    </v-card>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  
  // Recebe os elementos do componente pai
  const props = defineProps({
    elements: Array,
  });
  
  // Computa o estilo com base nos elementos
  function elementStyle(element) {
    return {
      backgroundColor: element.color || '#fff',
      mask: element.mask || 'none',
      // Outros estilos aplicados dinamicamente
    };
  }
  </script>
  
  <style scoped>
  .preview-area {
    /* Área de visualização dragável e responsiva */
    width: 100%;
    height: auto;
    position: relative;
    /* Estilos responsivos e de drag */
  }
  </style>
  