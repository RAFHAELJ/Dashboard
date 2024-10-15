<template>
  <v-dialog v-model="showRoleModal" max-width="600px">
    <v-card>
      <v-card-title>Permissões para {{ roleTitle }}</v-card-title>
      <v-card-text>
        <!-- Select para Ações com Chips -->
        <v-select
          v-model="selectedActions"
          :items="actions"
          label="Selecione as Ações"
          multiple
          chips
          clearable
        ></v-select>

        <!-- Select para Páginas com Chips -->
        <v-select
          v-model="selectedPages"
          :items="pages"
          label="Selecione as Páginas"
          multiple
          chips
          clearable
        ></v-select>
      </v-card-text>
      <v-card-actions>
        <v-btn color="primary" @click="saveRoles">Salvar</v-btn>
        <v-btn text @click="closeRoleModal">Fechar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  roleTitle: {
    type: String,
    default: 'Operador'
  },
  actions: {
    type: Array,
    default: () => ['ler', 'gravar', 'alterar', 'tudo']
  },
  pages: {
    type: Array,
    default: () => ['Página 1', 'Página 2', 'Página 3']
  }
});

const emit = defineEmits(['save', 'close']);

const selectedActions = ref([]);
const selectedPages = ref([]);
const showRoleModal = ref(props.show);

// Sincronizar a prop "show" com o modal
watch(() => props.show, (newVal) => {
  showRoleModal.value = newVal;
});

// Fechar o modal
const closeRoleModal = () => {
  emit('close');
};

// Salvar as permissões e fechar o modal
const saveRoles = () => {
  emit('save', {
    actions: selectedActions.value,
    pages: selectedPages.value
  });
  closeRoleModal();
};
</script>

<style scoped>
.v-card {
  background-color: #f8f9fa;
}
</style>
