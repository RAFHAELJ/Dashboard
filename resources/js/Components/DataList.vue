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
   canAccess: { // Recebendo o canAccess diretamente via prop
    type: Function,
    required: true
  },
  createRoute: {
    type: String,
    required: true,
  },
   showLink: {
    type: Boolean,
    default: false
  },
  showControladoraLink: { 
    type: Boolean,
    default: false
  }
});

const search = ref('');

const filteredItems = computed(() => {
  const searchValue = search.value.toLowerCase();
  const items = props.items.data || []; 
  return items.filter((item) => {
    return Object.values(item).some(value =>
      String(value).toLowerCase().includes(searchValue)
    );
  });
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
  // Seleciona o elemento da tabela
  const table = document.querySelector('table'); // Ajuste o seletor para sua tabela

  if (table) {
    // Cria uma nova janela temporária
    const printWindow = window.open('', '', 'width=800,height=600');
    
    // Adiciona o conteúdo da tabela na nova janela
    printWindow.document.write(`
      <html>
        <head>
          <title>Imprimir Tabela</title>
          <style>
            /* Estilos de impressão */
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

    // Executa o comando de impressão
    printWindow.document.close(); // Necessário para que o conteúdo seja renderizado
    printWindow.focus(); // Garante que a nova janela está ativa
    printWindow.print();
    printWindow.close();
  }
};

const tableHeight = computed(() => {
  return `calc(100vh - 160px)`;  // Você já tem isso, só precisa garantir que está definido corretamente
});

const handleExportCSV = () => {
  const items = props.items.data || []; // Dados da tabela

  if (items.length === 0) {
    console.log("Nenhum dado para exportar");
    return;
  }

  // Cria o cabeçalho do CSV a partir dos headers
  const headers = props.headers.map(header => header.text).join(',');

  // Cria o corpo do CSV a partir dos dados da tabela
  const rows = items.map(item => {
    return Object.values(item).join(',');
  }).join('\n');

  // Junta o cabeçalho e o corpo
  const csvContent = `${headers}\n${rows}`;

  // Cria um blob com o conteúdo CSV
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);

  // Cria um link temporário para iniciar o download do CSV
  const link = document.createElement('a');
  link.setAttribute('href', url);
  link.setAttribute('download', 'tabela_exportada.csv');
  document.body.appendChild(link);

  // Dispara o clique no link para baixar o arquivo
  link.click();

  // Remove o link temporário
  document.body.removeChild(link);
};


watch(() => props.items, (newItems) => {
  console.log('Itens paginados atualizados:', newItems);
});

const openControladora = (controladora) => {
  if (!controladora.ip) return;

  const isIPAddress = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(controladora.ip);

  const url = isIPAddress && controladora.porta
    ? `http://${controladora.ip}:${controladora.porta}`
    : `http://${controladora.ip}`;

  window.open(url, '_blank');
};



</script>

<template>
  <v-container fluid fill-height>
    <v-row>
      <v-col cols="12">
        <v-card flat class="rounded-lg shadow">
          <v-card-title class="d-flex align-center justify-between flex-wrap pe-2">
            <!-- Campo de busca à esquerda -->
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

            <!-- Espaço para manter os botões à direita -->
            <v-spacer></v-spacer>

            <!-- Botões de ação à direita -->
            <div class="d-flex align-center">
              <!-- Botão de adicionar -->
              <AddButton
                v-if="showCreateButton && canAccess(props.createRoute, 'gravar')"
                :label="createButtonLabel"
                @click="handleCreateItem"
                class="me-2"
              />

              <!-- Menu de ações com opções de imprimir e exportar CSV -->
              <ActionMenu
                @print="handlePrint"
                @exportCsv="handleExportCSV"
              />
            </div>
          </v-card-title>

          <v-divider></v-divider>

          <!-- Tabela de dados -->
          <v-data-table
            :headers="props.headers"
            :items="filteredItems"
            :item-key="props.itemKey"
            class="elevation-1"
            :height="tableHeight"
          >
            <!-- Títulos das colunas -->
            <template v-slot:columnTitles>
              <v-col v-for="(title, index) in props.columnTitles" :key="index">
                {{ title }}
              </v-col>
            </template>

            <template v-slot:item.controladora.nome="{ item }" v-if="showControladoraLink">
              <td>
                <span
                  v-if=" item.controladora"
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

            <!-- Ações de edição e exclusão -->
            <template v-slot:item.actions="{ item }">
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

            <!-- Paginação -->
            <template v-slot:footer>
              <v-pagination
                v-model="props.items.current_page"
                :length="props.items.last_page"
                :total-visible="7"
              ></v-pagination>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.v-card {
  background-color: #f8f9fa;
}

/* Campo de busca */
.search-field {
  max-width: 300px;
  margin-right: 10px;
}

/* Espaçamento entre botões */
.me-2 {
  margin-right: 10px;
}

@media (max-width: 768px) {
  .v-card-title {
    flex-direction: column;
    align-items: flex-start;
  }

  .search-field {
    width: 100%;
    margin-top: 10px;
  }

  .me-2 {
    margin-bottom: 10px;
  }
}
</style>

<style scoped>
.v-card {
  background-color: #f8f9fa;
}

.search-field {
  max-width: 300px;
  margin-right: 10px;
}

@media (max-width: 768px) {
  .v-card-title {
    flex-direction: column;
    align-items: flex-start;
  }

  .search-field {
    width: 100%;
    margin-top: 10px;
  }
}
</style>
