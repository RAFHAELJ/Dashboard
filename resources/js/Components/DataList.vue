<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  headers: {
    type: Array,
    required: true,
  },
  items: {
    type: Array,
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
});

const search = ref('');

const filteredItems = computed(() => {
  const searchValue = search.value.toLowerCase();
  return props.items.filter((item) => {
    return Object.values(item).some(value =>
      String(value).toLowerCase().includes(searchValue)
    );
  });
});

const tableHeight = computed(() => {
  return `calc(100vh - 160px)`;
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
console.log(props.columnTitles);
</script>

<template>
  <v-container fluid fill-height>
    <v-row>
      <v-col cols="12">
        <v-card flat class="rounded-lg shadow">
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
              @click="handleCreateItem"
              density="comfortable"
              color="primary"
              class="add-button"
            >
              <v-icon start>mdi-plus</v-icon>
              {{ createButtonLabel }}
            </v-btn>
          </v-card-title>

          <v-divider></v-divider>

          <v-data-table
            :headers="props.headers"
            :items="filteredItems"
            :item-key="props.itemKey"
            class="elevation-1"
            :height="tableHeight"
          >
            <template v-slot:columnTitles>
              <v-col v-for="(title, index) in props.columnTitles" :key="index">
                {{ title }}
              </v-col>
            </template>

            <template v-slot:item.actions="{ item }">
              <v-btn
                variant="plain"
                density="compact"
                icon="mdi-pencil-outline"
                @click="handleEditItem(item)"
              > </v-btn>
              <v-btn
                variant="plain"
                density="compact"
                icon="mdi-trash-can-outline"
                @click="handleDeleteItem(item)"
              > </v-btn>
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

/* Botão de adicionar */
.add-button {
  border-radius: 50px;
  text-transform: uppercase;
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

  .add-button {
    width: 100%;
    margin-top: 10px;
  }
}
</style>
