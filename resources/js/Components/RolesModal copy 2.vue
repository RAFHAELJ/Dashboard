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
            item-title="name" 
            item-value="id"  
          ></v-select>
  
          <!-- Select para Páginas com Chips -->
          <v-select
            v-model="selectedPages"
            :items="pages"
            label="Selecione as Páginas"
            multiple
            chips
            clearable
            item-title="name" 
            item-value="id"   
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
    userId: {
      type: Number,
      default: null // Pode ser null se for um novo usuário
    }
  });
  
  const emit = defineEmits(['save', 'close']);
  
  // Ações e páginas
  const actions = ref([]);
  const pages = ref([]);
  const selectedActions = ref([]);  // IDs das ações selecionadas
  const selectedPages = ref([]);    // IDs das páginas selecionadas
  const showRoleModal = ref(props.show);
  
  // Sincronizar a prop "show" com o modal
  watch(() => props.show, (newVal) => {
    showRoleModal.value = newVal;
  
    // Se for um usuário existente, buscar permissões, ações e páginas
    if (newVal && props.userId) {
      fetchPermissions();
      fetchActionsAndPages();
    }
  });
  
  // Função para buscar as permissões atuais do usuário
  const fetchPermissions = () => {
    fetch(`/users/${props.userId}/permissionsSelect`, {
      headers: {
        'Accept': 'application/json',
      }
    })
      .then(response => response.json())
      .then(data => {
        selectedActions.value = data.actions.map(action => action.id); // IDs das ações
        selectedPages.value = data.pages.map(page => page.id); // IDs das páginas
        console.log(selectedActions.value, selectedPages.value);
      })
      .catch(error => console.error('Erro ao carregar permissões:', error));
  };
  
  // Função para buscar ações e páginas
  const fetchActionsAndPages = () => {
    // Buscar actions
    fetch('/actions', {
      headers: {
        'Accept': 'application/json',
      }
    })
      .then(response => response.json())
      .then(data => {
        actions.value = data.actions;
      })
      .catch(error => console.error('Erro ao carregar ações:', error));
  
    // Buscar páginas
    fetch('/pages', {
      headers: {
        'Accept': 'application/json',
      }
    })
      .then(response => response.json())
      .then(data => {
        pages.value = data.pages;
      })
      .catch(error => console.error('Erro ao carregar páginas:', error));
  };
  
  // Fechar o modal
  const closeRoleModal = () => {
    emit('close');
  };
  
  // Salvar as permissões e fechar o modal
  const saveRoles = () => {
    console.log(selectedActions.value, selectedPages.value);
    emit('save', {
      actions: selectedActions.value, // IDs das ações selecionadas
      pages: selectedPages.value       // IDs das páginas selecionadas
    });
    closeRoleModal();
  };
  </script>
  
  <style scoped>
  .v-card {
    background-color: #f8f9fa;
  }
  </style>
  