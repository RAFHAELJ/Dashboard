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

      <!-- Botões de Ação -->
      <v-col cols="12" sm="12" md="4" class="d-flex justify-end">
        <v-btn @click="onSearch" color="primary" small rounded class="py-1 me-2">
          <v-icon left>mdi-magnify</v-icon>
          Buscar
        </v-btn>
        <v-btn :href="exportUrl" color="secondary" small rounded class="py-1">
          <v-icon left>mdi-file-download</v-icon>
          Exportar CSV
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
  extraFields: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['update:filters', 'search']);

// Cópia local dos filtros
const internalFilters = ref({ ...props.filters });

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

// Função de busca
const onSearch = () => {
  emit('search', internalFilters.value); // Emite os filtros atuais ao buscar
};

const exportUrl = computed(() => {
  return route('radios.export', props.filters);
});
</script>
