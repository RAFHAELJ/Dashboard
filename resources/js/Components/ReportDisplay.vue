<template>
  <v-list>
    <v-list-item
      v-for="(report, index) in reports"
      :key="index"
      @click="onViewDetails(report)"
      class="report-item"
    >
      <div class="d-flex justify-space-between align-center">
        <div>
          <strong>Usuário:</strong> {{ report.UserName }}<br>
          <small>Início: {{ report.acctstarttime }} - Fim: {{ report.acctstoptime }}</small>
        </div>
        <v-icon right color="primary">mdi-eye</v-icon>
      </div>
    </v-list-item>
  </v-list>

  <v-row>
    <v-col cols="12" class="d-flex justify-center">
      <v-pagination :model-value="page" :length="lastPage" @update:modelValue="onPageChange" />
    </v-col>
  </v-row>

  <!-- Modal para exibir detalhes -->
  <v-dialog v-model="dialog" max-width="600px">
    <v-card>
      <v-card-title>
        Detalhes do Usuário: {{ selectedReport.UserName }}
      </v-card-title>
      <v-card-text>
        <div><strong>Início da Sessão:</strong> {{ selectedReport.acctstarttime }}</div>
        <div><strong>Fim da Sessão:</strong> {{ selectedReport.acctstoptime }}</div>
        <div><strong>Duração:</strong> {{ selectedReport.acctsessiontime }}</div>
        <div><strong>Dados Enviados:</strong> {{ selectedReport.acctinputoctets }}</div>
        <div><strong>Dados Recebidos:</strong> {{ selectedReport.acctoutputoctets }}</div>
        <div><strong>Endereço de Origem:</strong> {{ selectedReport.callingstationid }}</div>
        <div><strong>Endereço de Destino:</strong> {{ selectedReport.calledstationid }}</div>
      </v-card-text>
      <v-card-actions>
        <v-btn color="primary" text @click="dialog = false">Fechar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  reports: {
    type: Array,
    required: true,
  },
  page: {
    type: Number,
    required: true,
  },
  lastPage: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(['page-change', 'view-details']);

const dialog = ref(false);
const selectedReport = ref({});

// Função para exibir detalhes no modal
const onViewDetails = (report) => {
  selectedReport.value = report;
  dialog.value = true;
};

// Função para emitir o evento de mudança de página
const onPageChange = (newPage) => {
  emit('page-change', newPage);
};
</script>

<style scoped>
.report-item {
  cursor: pointer;
  padding: 16px;
  border-bottom: 1px solid #e0e0e0;
  transition: background-color 0.2s ease;
}

.report-item:hover {
  background-color: #f0f0f0;
}
</style>
