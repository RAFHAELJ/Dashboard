<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LoginPreview from '@/Components/LoginPreview.vue';  // Componente criado anteriormente
import { VContainer, VRow, VCol, VCard, VCardActions, VCardTitle, VCardText, VBtn, VIcon } from 'vuetify/components';

const { props } = usePage();
const loginConfigurations = ref(props.customizations || []);

const search = ref('');

const handleEditItem = (config) => {
    router.visit(route('login_customizations.edit', config.id)); 
};

// Função para deletar uma configuração de login
const deleteLoginConfiguration = async (id) => {
  if (confirm('Tem certeza que deseja deletar esta configuração?')) {
    try {
      await router.delete(route('login_customizations.destroy', id));

      if (Array.isArray(loginConfigurations.value)) {
        loginConfigurations.value = loginConfigurations.value.filter(config => config.id !== id);
      }

      window.location.reload();
    } catch (error) {
      console.error('Erro ao deletar configuração de login:', error);
    }
  }
};

// Função para processar os dados retornados
const processLoginConfigurations = (configs) => {
  return configs.map(config => {
    // Verifica se os campos são strings JSON e faz o parsing
    if (typeof config.login_method === 'string') {
      config.login_method = JSON.parse(config.login_method);
    }

    if (typeof config.elements === 'string') {
      config.elements = JSON.parse(config.elements);
    }

    return config;
  });
};

// Computed para filtrar as configurações com base na pesquisa
const filteredLoginConfigurations = computed(() => {
  const configArray = loginConfigurations.value || [];

  return configArray.filter(config => {
    const layoutName = typeof config.layout_name === 'string' ? config.layout_name.toLowerCase() : '';
    return layoutName.includes(search.value.toLowerCase());
  });
});

// Inicializando os dados
onMounted(() => {
  if (loginConfigurations.value.length > 0) {
    loginConfigurations.value = processLoginConfigurations(loginConfigurations.value);
  }
});

</script>

<template>
  <Head title="Configurações de Login" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container class="page-background" fluid fill-height>
        <!-- Filtros e Botão de Adicionar na Mesma Linha -->
        <v-row class="align-center">
          <v-col cols="12" md="6" class="py-1">
            <v-text-field
              v-model="search"
              label="Buscar Configuração"
              prepend-icon="mdi-magnify"
              density="compact"
              class="search-field"
            />
          </v-col>
         
        </v-row>

        <!-- Listagem de Login Configurations em Formato de Card -->
        <v-row>
          <v-col cols="12" sm="6" md="4" lg="3" v-for="config in filteredLoginConfigurations" :key="config.id">
            <v-card class="card-login-config" hover v-if="canAccess('login_configurations','ler')">
              <v-card-title>{{ config.layout_name }}</v-card-title>
              <v-card-text>
                <!-- Inserindo o LoginPreview dentro do card para exibir a configuração -->
                <LoginPreview :customScreenData="config" />
              </v-card-text>

              <v-card-actions>
                <v-btn 
                v-if="canAccess('login_configurations','atualizar')"
                icon @click="handleEditItem(config)">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn 
                v-if="canAccess('login_configurations','excluir')"
                icon @click="deleteLoginConfiguration(config.id)">
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>

        <!-- Formulário para Edição/Criar Configurações de Login -->
        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
          <!-- Aqui você pode incluir o formulário de edição, se necessário -->
        </v-dialog>
      </v-container>
    </template>
  </AuthenticatedLayout>
</template>

<style scoped>
.page-background {
  background-color: #f4f4f9;
  padding: 20px;
}

.add-button {
  border-radius: 8px;
  text-transform: uppercase;
}

.search-field {
  border-radius: 8px;
  background-color: #ffffff;
}

.card-login-config {
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-login-config .v-card-title {
  font-weight: bold;
}

</style>