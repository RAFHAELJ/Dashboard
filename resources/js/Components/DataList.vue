<script setup>
import { ref, computed, watch } from 'vue';
import AddButton from './AddButton.vue';
import ActionMenu from './ActionMenu.vue';

const props = defineProps({
  headers: {
    type: Array,
    required: true,
  },
  items: {
    type: Object,
    required: true,
  },
  searchPlaceholder: {
    type: String,
    default: 'Pesquisar',
  },
  createButtonLabel: {
    type: String,
    default: 'Adicionar',
  },
  onCreate: {
    type: Function,
    required: true,
  },
  onEdit: {
    type: Function,
    required: true,
  },
  onDelete: {
    type: Function,
    required: true,
  },
  itemKey: {
    type: String,
    default: 'id',
  },
  columnTitles: {
    type: Array,
    default: () => [],
  },
  showCreateButton: {
    type: Boolean,
    default: true,
  },
  canAccess: {
    type: Function,
    required: true,
  },
  createRoute: {
    type: String,
    required: true,
  },
  showLink: {
    type: Boolean,
    default: false,
  },
  showControladoraLink: {
    type: Boolean,
    default: false,
  },
  onCustomAction: {
    type: Function,
    default: null,
  },
  search: {
    type: String,
    default: '',
  },
});

const search = ref(props.search || ''); 
const currentPage = ref(props.items.current_page || 1);
const itemsPerPage = ref(props.items.per_page || 10);
const totalRecords = computed(() => props.items.total || 1);
const emit = defineEmits(['options-changed', 'search-updated']);
const filteredItems = computed(() => {
  const searchValue = search.value.toLowerCase(); 
  const items = props.items.data || [];

  if (!searchValue) {
    return items; 
  }

  return items.filter((item) => {
    return Object.values(item).some((value) =>
      String(value).toLowerCase().includes(searchValue)
    );
  });
});

watch(search, (newSearch) => {
  if (newSearch === undefined) {
    search.value = ''; // Define um valor padrão se for undefined
  }
  emit('search-updated', newSearch || ''); 
});

const handleDeleteItem = (item) => {
  props.onDelete(item);
};

const handleEditItem = (item) => {
  props.onEdit(item);
};

const handleCreateItem = () => {
  props.onCreate();
};

const handlePrint = () => {
  const table = document.querySelector('table');
  if (table) {
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write(`
      <html>
        <head>
          <title>Imprimir Tabela</title>
          <style>
            table {
              width: 100%;
              border-collapse: collapse;
            }
            table, th, td {
              border: 1px solid black;
            }
            th, td {
              padding: 8px;
              text-align: left;
            }
          </style>
        </head>
        <body>
          ${table.outerHTML}
        </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
  }
};

const tableHeight = computed(() => {
  return `calc(100vh - 160px)`;
});

const handleExportCSV = () => {
  const items = props.items.data || [];
  if (items.length === 0) {
    console.log("Nenhum dado para exportar");
    return;
  }

  const headers = props.headers.map(header => header.text).join(',');
  const rows = items.map(item => Object.values(item).join(',')).join('\n');
  const csvContent = `${headers}\n${rows}`;

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.setAttribute('href', url);
  link.setAttribute('download', 'tabela_exportada.csv');
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const updateOptions = (options) => {  
  
  const { page, itemsPerPage: newItemsPerPage } = options;  
  if (currentPage.value !== page || itemsPerPage.value !== newItemsPerPage) {
    currentPage.value = page;
    itemsPerPage.value = newItemsPerPage;
    emit('options-changed', { page: currentPage.value, itemsPerPage: itemsPerPage.value });

  }
};

watch(() => props.items, (newItems) => {
  currentPage.value = newItems.current_page;
  itemsPerPage.value = newItems.per_page;
});

const openControladora = (controladora) => {
  if (!controladora.ip) return;

  const isIPAddress = /^(25[0-5]|2[0-4][0-9][0-9]?)\.(25[0-5]|2[0-4][0-9][0-9]?)\.(25[0-5]|2[0-4][0-9][0-9]?)\.(25[0-5]|2[0-4][0-9][0-9]?)$/.test(controladora.ip);
  const url = isIPAddress && controladora.porta ? `http://${controladora.ip}:${controladora.porta}` : `https://${controladora.ip}`;
  window.open(url, '_blank');
};
const clearFilters = () => {
  search.value = '';
  currentPage.value = 1; 
  fetchPage({ page: 1, itemsPerPage: itemsPerPage.value }); 
};
const fetchPage = ({ page, itemsPerPage }) => {
  emit('options-changed', { page, itemsPerPage }); 
};
</script>


<template>
  <v-container fluid fill-height>
    <v-row>
      <v-col cols="12">
        <v-card flat class="rounded-lg shadow" elevation="5">
          <v-card-title class="d-flex align-center justify-between flex-wrap pe-2">
            <v-text-field
              v-model="search"
              density="compact"
              :label="searchPlaceholder"
              prepend-inner-icon="mdi-magnify"
              variant="solo-filled"
              hide-details
              single-line
              class="search-field"
            ></v-text-field>
            <v-btn
            text
            color="primary"
            class="me-2"
            @click="clearFilters"
          >
            Limpar Filtros
          </v-btn>

            <v-spacer></v-spacer>

            <div class="d-flex align-center">
              <AddButton
                v-if="showCreateButton && canAccess(props.createRoute, 'gravar')"
                :label="createButtonLabel"
                @click="handleCreateItem"
                class="me-2"
              />
              <ActionMenu @print="handlePrint" @exportCsv="handleExportCSV" />
            </div>
          </v-card-title>

          <v-divider></v-divider>

          <v-data-table-server
              :headers="props.headers"
              :items="filteredItems"
              :item-key="props.itemKey"
              class="elevation-1"
              :height="tableHeight"
              :page="currentPage" 
              :items-per-page="itemsPerPage"
              :items-length="totalRecords"
              @update:options="updateOptions"
            >

            <template v-slot:item.controladora.nome="{ item }" v-if="showControladoraLink">
              <td>
                <span
                  v-if="item.controladora"
                  style="color: blue; cursor: pointer"
                  @click="openControladora(item.controladora)"
                  class="controladora-link"
                >
                  {{ item.controladora.nome }}
                </span>
                <span v-else>
                  Não disponível
                </span>
              </td>
            </template>

            <template v-slot:item.actions="{ item }">
              <v-btn
                v-if="props.onCustomAction"
                icon="mdi-information-slab-circle-outline"
                variant="plain"
                density="compact"
                @click="props.onCustomAction(item)"
              ></v-btn>
              <v-btn
                v-if="canAccess(props.createRoute, 'atualizar')"
                variant="plain"
                density="compact"
                icon="mdi-pencil-outline"
                @click="handleEditItem(item)"
              ></v-btn>
              <v-btn
                v-if="canAccess(props.createRoute, 'excluir')"
                variant="plain"
                density="compact"
                icon="mdi-trash-can-outline"
                @click="handleDeleteItem(item)"
              ></v-btn>
            </template>
          </v-data-table-server>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.v-card {
  background-color: #f8f9fa;
}
.search-field {
  max-width: 300px;
  margin-right: 10px;
}
.me-2 {
  margin-right: 10px;
}
</style>
