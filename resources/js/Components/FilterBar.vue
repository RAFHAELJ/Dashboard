<template>
  <v-sheet color="lighten-4" class="mb-6 py-4 px-2" rounded elevation="2">
    <v-row align="center" justify="space-between" dense>
      <!-- Campo de Data de Início -->
      <v-col cols="12" sm="6" md="2">
        <v-text-field
          v-model="internalFilters.startD"
          label="Data de Início"
          prepend-inner-icon="mdi-calendar"
          type="date"
          outlined
          dense
          hide-details
          class="py-1"
        ></v-text-field>
      </v-col>

      <!-- Campo de Data de Fim -->
      <v-col cols="12" sm="6" md="2">
        <v-text-field
          v-model="internalFilters.endD"
          label="Data de Fim"
          prepend-inner-icon="mdi-calendar"
          type="date"
          outlined
          dense
          hide-details
          class="py-1"
        ></v-text-field>
      </v-col>

      <!-- Renderização dinâmica de campos adicionais -->
      <template v-for="(field, key) in extraFields" :key="key">
        <v-col :cols="field.cols || '12'" :sm="field.sm || '6'" :md="field.md || '2'">
          <v-text-field
            v-model="internalFilters[key]"
            :label="field.label"
            :prepend-inner-icon="field.icon || ''"
            :type="field.type || 'text'"
            outlined
            dense
            hide-details
            class="py-1"
          ></v-text-field>
        </v-col>
      </template>

      
      <v-col cols="12" sm="12" md="4" class="d-flex justify-end align-center">       
        <v-progress-circular
          v-show="true"  
          indeterminate
          color="primary"
          class="me-4"
          :size="24" 
          v-if="loading"
          >Careregando</v-progress-circular>
        
        <v-btn @click="onSearch" color="primary" small rounded class="py-1 me-2" :disabled="loading">
          <v-icon left>mdi-magnify</v-icon>
          Buscar
        </v-btn>
        
        <slot></slot>
        
        <v-btn :href="exportUrl" color="secondary" small rounded class="py-1 mx-1" style="min-width: 100px" v-if="csvButton" :disabled="loading">
          <v-icon left>mdi-file-download</v-icon>
          Exportar
        </v-btn>
      </v-col>
    </v-row>
  </v-sheet>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
  filters: {
    type: Object,
    required: true,
  },
  csvButton: {
    type: Boolean,
    default: false,
  },
  extraFields: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['update:filters', 'search']);

// Cópia local dos filtros
const internalFilters = ref({ ...props.filters });
const loading = ref(false); // Estado de carregamento

// Observa mudanças no filtro externo para refletir no interno
watch(
  () => props.filters,
  (newFilters) => {
    if (JSON.stringify(newFilters) !== JSON.stringify(internalFilters.value)) {
      internalFilters.value = { ...newFilters };
    }
  },
  { deep: true }
);

// Emite as atualizações para o componente pai
watch(
  internalFilters,
  (newFilters) => {
    if (JSON.stringify(newFilters) !== JSON.stringify(props.filters)) {
      emit('update:filters', newFilters);
    }
  },
  { deep: true }
);

// Função de busca com loading
const onSearch = async () => {
  loading.value = true;
  emit('search', internalFilters.value); // Emite os filtros atuais ao buscar

  // Simula uma operação assíncrona com espera para visualização do loading
  await new Promise(resolve => setTimeout(resolve, 1000)); // Aguarda 1 segundo
  
  loading.value = false; // Desativa o estado de carregamento após a busca
};

// URL de exportação
const exportUrl = computed(() => route('radios.export', props.filters));
</script>
