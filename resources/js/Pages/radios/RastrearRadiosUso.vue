<template>
  <AuthenticatedLayout>
    <v-container>
      <!-- Filtros de Data e Inputs Adicionais -->
      <FilterBar
        :filters="filters"
        @update:filters="(newFilters) => (filters = newFilters)"
        @search="handleSearch"
      >
        <v-btn @click="exportToCSV" color="primary" small rounded class="py-1 mx-1" style="min-width: 100px">
          <v-icon left>mdi-file-download</v-icon>
          Exportar
        </v-btn>
        
        <v-btn @click="showGraphicModal = true" color="success" small rounded class="py-1 mx-1" style="min-width: 100px">
          <v-icon left>mdi-chart-bar</v-icon>
          Gráfico
        </v-btn>
      </FilterBar>

      <!-- Indicador de Carregamento -->
      <v-row v-if="loading" class="d-flex justify-center mb-3">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
      </v-row>

      <!-- Total de registros -->
      <v-row>
        <v-col cols="12" class="d-flex justify-center">
          <p>Total de registros: {{ totalRecords }}</p>
        </v-col>
      </v-row>

      <!-- Tabela de Relatório -->
      <v-data-table
        :headers="headers"
        :items="formattedData"
        class="elevation-1"
        :items-per-page="10"
      >
        <template v-slot:[`item.total_consumption`]="{ item }">
          {{ item.total_consumption }} MB
        </template>
      </v-data-table>

      <!-- Modal para o Gráfico -->
      <v-dialog v-model="showGraphicModal" max-width="2200px">
        <v-card>
          <v-card-title class="text-h6">Consumo Total por MAC (MB)</v-card-title>
          <v-card-text>
            <div class="chart-container">
              <BarChart :chart-data="chartData" :maxConsumptionGB="maxConsumptionGB" />
            </div>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" text @click="showGraphicModal = false">Fechar</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted } from 'vue';
import FilterBar from '@/Components/FilterBar.vue';
import { router } from '@inertiajs/vue3';
import BarChart from '@/Components/BarChart.vue';

const getTodayDate = () => new Date().toISOString().split('T')[0];
const getDateBefore = (days) => {
  const date = new Date();
  date.setDate(date.getDate() - days);
  return date.toISOString().split('T')[0];
};

const filters = ref({
  startD: getDateBefore(15),
  endD: getTodayDate(),
  mac: '',
  region: ''
});
const macData = ref([]);
const page = ref(1);
const loading = ref(false);
const showGraphicModal = ref(false);
const totalRecords = computed(() => macData.value.length);

const headers = [
  { text: 'MAC', value: 'mac', title: 'MAC', key: 'mac' },
  { text: 'Acessos', value: 'access_count', title: 'Acessos', key: 'access_count' },
  { text: 'Total Upload', value: 'total_upload', title: 'Total Upload', key: 'total_upload' },
  { text: 'Total Download', value: 'total_download', title: 'Total Download', key: 'total_download' },
  { text: 'Consumo Total (MB)', value: 'total_consumption', title: 'Consumo Total (MB)', key: 'total_consumption' },
];

const convertToMB = (size) => {
  const [value, unit] = size.split(" ");
  return parseFloat(value) * (unit === "GB" ? 1024 : 1);
};

const formattedData = computed(() => 
  macData.value.map((data) => ({
    mac: data.mac,
    access_count: data.access_count,
    total_upload: data.total_upload,
    total_download: data.total_download,
    total_consumption: (convertToMB(data.total_upload) + convertToMB(data.total_download)).toFixed(2),
  }))
);

const chartData = computed(() => {
  return {
    labels: macData.value.map((item) => item.mac),
    datasets: [
      {
        label: 'Consumo Total (GB)',
        backgroundColor: 'blue',
        data: macData.value.map(
          (item) => (convertToMB(item.total_upload) + convertToMB(item.total_download)) / 1024
        ),
      },
      {
        label: 'Acessos',
        backgroundColor: 'green',
        data: macData.value.map((item) => item.access_count || 0),
      }
    ]
  };
});

const maxConsumptionGB = computed(() => {
  return (
    Math.max(
      ...macData.value.map(item => (convertToMB(item.total_upload) + convertToMB(item.total_download)) / 1024)
    ) * 1.1
  );
});

const fetchMacData = async () => {
  loading.value = true;
  try {
    await router.get('/radios/basetrack', {
      startD: filters.value.startD,
      endD: filters.value.endD,
      mac: filters.value.mac,
      region: filters.value.region,
      page: page.value,
    }, {
      preserveState: true,
      replace: true,
      onSuccess: (pageData) => {
        macData.value = pageData.props.macData || [];
      }
    });
  } finally {
    loading.value = false;
  }
};

const exportToCSV = () => {
  const csvContent = [
    ["MAC", "Acessos", "Total Upload", "Total Download", "Consumo Total (MB)"],
    ...formattedData.value.map(data => [
      data.mac,
      data.access_count,
      data.total_upload,
      data.total_download,
      `${data.total_consumption} MB`
    ])
  ]
  .map(row => row.join(","))
  .join("\n");

  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.setAttribute("download", "Relatorio de Uso.csv");
  link.click();
};

const handleSearch = () => {
  page.value = 1;
  fetchMacData();
};

onMounted(() => {
  fetchMacData();
});
</script>

<style scoped>
.chart-container {
  position: relative;
  height: 500px;
  width: 100%;
}
</style>
