<script setup>
const props = defineProps({
  onPrint: {
    type: Function,
    required: true,
  },
  onExportCsv: {
    type: Function,
    required: true,
  },
});
</script>

<template>
  <v-menu>
    <!-- Usando o slot activator corretamente -->
    <template v-slot:activator="{ props }">
      <v-btn icon="mdi-dots-vertical" v-bind="props"></v-btn> <!-- Usando props do ativador -->
    </template>

    <!-- Conteúdo do menu -->
    <v-list>
      <v-list-item @click="onPrint">
        <v-icon>
          <v-icon>mdi-printer</v-icon>
        </v-icon>
        <v-list-item-title>Imprimir</v-list-item-title>
      </v-list-item>

      <v-list-item @click="onExportCsv">
        <v-icon>
          <v-icon>mdi-file-csv</v-icon>
        </v-icon>
        <v-list-item-title>Exportar CSV</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>
