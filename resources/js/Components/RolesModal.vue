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
      required: true,
    },
    roleTitle: {
      type: String,
      default: 'Operador',
    },
    userId: {
      type: Number,
      default: null, 
    },
  });
  
  const emit = defineEmits(['save', 'close']);
  
  // Ações e páginas disponíveis para selecionar
  const actions = ref([]);  
  const pages = ref([]);    
  
  // IDs das ações e páginas selecionadas
  const selectedActions = ref([]); 
  const selectedPages = ref([]);   
  
  const showRoleModal = ref(props.show);
  
  // Sincronizar a prop "show" com o modal
  watch(
    () => props.show,
    async (newVal) => {
      showRoleModal.value = newVal;    
      await fetchActionsAndPages(); 

      if (newVal && props.userId) {      
        // Em seguida, carrega as permissões do usuário
        fetchPermissions();
      }
    }
  );
  
  // Função para buscar as permissões atuais do usuário
  const fetchPermissions = async () => {
    try {
      const response = await fetch(`/users/${props.userId}/permissionsSelect`, {
        headers: {
          Accept: 'application/json',
        },
      });
  
      const data = await response.json();
      
      // Preencher os selects com os IDs vindos da API (AÇÕES e PÁGINAS)
      selectedActions.value = data.actions.map(action => action.id); 
      selectedPages.value = data.pages.map(page => page.id);         

    } catch (error) {
      console.error('Erro ao carregar permissões:', error);
    }
  };
  
  // Função para buscar ações e páginas
  const fetchActionsAndPages = async () => {
    try {
      // Buscar ações
      const actionsResponse = await fetch('/actions', {
        headers: {
          Accept: 'application/json',
        },
      });
      const actionsData = await actionsResponse.json();
      actions.value = actionsData.actions;  
  
      // Buscar páginas
      const pagesResponse = await fetch('/pages', {
        headers: {
          Accept: 'application/json',
        },
      });
      const pagesData = await pagesResponse.json();
      pages.value = pagesData.pages; 
    } catch (error) {
      console.error('Erro ao carregar ações ou páginas:', error);
    }
  };
  
  // Fechar o modal
  const closeRoleModal = () => {
    emit('close');
  };
  
  // Salvar as permissões e fechar o modal
  const saveRoles = () => {   
    emit('save', {
      actions: selectedActions.value,
      pages: selectedPages.value,     
    });
    closeRoleModal();
  };
  </script>
  
  <style scoped>
  .v-card {
    background-color: #f8f9fa;
  }
  </style>
  