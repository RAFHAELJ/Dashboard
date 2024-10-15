<template>
  <AuthenticatedLayout>
    <v-container>
      <!-- Usando o componente FilterBar -->
      <FilterBar
        :filters="filters"
        @update:filters="(newFilters) => filters = newFilters"
        @search="handleSearch"
      >
        <!-- Inputs adicionais, ex: MAC, Região -->
        <v-col cols="12" sm="6" md="3">
          <v-text-field
            v-model="filters.mac"
            label="MAC"
            prepend-inner-icon="mdi-network"
            outlined
            dense
            hide-details
          ></v-text-field>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-text-field
            v-model="filters.region"
            label="Região"
            prepend-inner-icon="mdi-map-marker"
            outlined
            dense
            hide-details
          ></v-text-field>
        </v-col>
      </FilterBar>

      <!-- Exibindo o total de registros -->
      <v-row>
        <v-col cols="12" class="d-flex justify-center">
          <p>Total de registros: {{ totalRecords }}</p>
        </v-col>
      </v-row>

      <!-- Usando o componente ReportDisplay -->
      <ReportDisplay
        :reports="users"
        :page="page"
        :lastPage="lastPage"
        @page-change="handlePageChange"
      />
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted } from 'vue';
import FilterBar from '@/Components/FilterBar.vue';
import ReportDisplay from '@/Components/ReportDisplay.vue';
import { router } from '@inertiajs/vue3';

// Função para obter a data de hoje e 15 dias antes
const getTodayDate = () => {
  const today = new Date();
  return today.toISOString().split('T')[0];
};

const getDateBefore = (days) => {
  const date = new Date();
  date.setDate(date.getDate() - days);
  return date.toISOString().split('T')[0];
};

// Filtros e estados
const filters = ref({
  startD: getDateBefore(15), // 15 dias atrás
  endD: getTodayDate(),      // Data de hoje
  username: '',
  mac: '',
  region: ''
});

const users = ref([]);
const page = ref(1);
const lastPage = ref(1);
const totalRecords = ref(0);

// Função para buscar relatórios
const fetchReports = () => {
  router.get('/radios/track', {
    startD: filters.value.startD,
    endD: filters.value.endD,
    username: filters.value.username,
    mac: filters.value.mac,
    region: filters.value.region,
    page: page.value,
  }, {
    preserveState: true,
    replace: true,
    onSuccess: (pageData) => {
      users.value = pageData.props.users.data;
      lastPage.value = pageData.props.users.last_page;
      totalRecords.value = pageData.props.users.total;
    },
  });
};

// Função para iniciar a busca sempre na página 1
const handleSearch = () => {
  page.value = 1;
  fetchReports();
};

// Função para lidar com a mudança de página
const handlePageChange = (newPage) => {
  page.value = newPage;
  fetchReports();
};

// Busca inicial de relatórios quando o componente é montado
onMounted(() => {
  fetchReports();
});
</script>
