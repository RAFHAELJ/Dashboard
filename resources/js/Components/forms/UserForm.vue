<template>
  <v-card>
    <v-card-title>
      {{ isEditing ? 'Editar' : 'Adicionar' }} {{ title }}
      <v-spacer></v-spacer>
      <v-btn icon @click="$emit('cancel')">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-card-title>

    <v-divider></v-divider>

    <v-card-text>
      <v-form ref="dynamicForm" v-model="valid">
        <template v-for="(field, key) in fields" :key="key">
          <!-- Campos de texto -->
          <v-text-field
            v-if="field.type !== 'select'"
            v-model="form[key]"
            :label="field.label"
            :type="field.type || 'text'"
            :rules="(field.rules || []).concat([(v) => formErrors[key] ? formErrors[key][0] : true])"  
            :error="!!formErrors[key]"
            :error-messages="formErrors[key] ? formErrors[key] : []"
            :required="field.required"
            :autocomplete="field.autocomplete"
          />

          <!-- Campo de Select para Cargos -->
          <v-select
              v-if="field.type === 'select'"
              v-model="form[key]"
              :items="field.items"
              :label="field.label"
              :rules="(field.rules || []).concat([(v) => formErrors[key] ? formErrors[key][0] : true])"
              :error="!!formErrors[key]"
              :error-messages="formErrors[key] ? formErrors[key] : []"
              :required="field.required"
              @update:modelValue="checkRole"
            />
        </template>
      </v-form>

      <!-- Selecione uma Região -->
      <regioes-select
        v-if="showCreateRegiao"
        v-model="form.regiao"
        label="Selecione uma região"
        :rules="[v => !!v || 'A seleção de uma região é obrigatória']"
      />

      <!-- Ícone para editar permissões (visível apenas no modo de edição) -->
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-if="form.nivel === 'Operador'" icon v-bind="attrs" @click="openRoleModal">
            <v-icon>mdi-lock</v-icon>
          </v-btn>
        </template>
        <span>Alterar Permissões</span>
      </v-tooltip>

      <!-- Componente RolesModal -->
      <RolesModal
        :show="showRoleModal"
        :roleTitle="'Operador'"
        :userId="form.id" 
        :actions="actions"
        :pages="pages"
        :selectedActions="selectedActions"
        :selectedPages="selectedPages"
        @save="handleSaveRoles"
        @close="closeRoleModal"
      />
    </v-card-text>

    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="primary" @click="submitForm">Salvar</v-btn>
      <v-btn text @click="$emit('cancel')">Cancelar</v-btn>
    </v-card-actions>
  </v-card>


</template>


<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import RegioesSelect from '../RegioesSelect.vue';
import RolesModal from '../RolesModal.vue';
import axios from 'axios';

const props = defineProps({
  formData: {
    type: Object,
    default: () => ({}),
  },
  fields: {
    type: Object,
    required: true,
  },
  showCreateRegiao: {
    type: Boolean,
    default: false,
  },
  isEditing: Boolean,
  title: {
    type: String,
    default: 'Item',
  },
  createRoute: {
    type: String,
    required: true,
  },
  updateRoute: {
    type: String,
    required: true,
  },
  returnRoute: {
    type: String,
    required: true,
  },
});

// Definir eventos
const emit = defineEmits(['cancel']);

// Formulário reativo
const form = reactive({ ...props.formData });
console.log(form);
const valid = ref(true);

// Estado do Snackbar
const snackbar = reactive({
  show: false,
  text: '',
  timeout: 3000, // 3 segundos para o snackbar sumir
});

// Estado para o modal de roles
const showRoleModal = ref(false);
const selectedActions = ref([]);
const selectedPages = ref([]);
const actions = ref([]); 
const pages = ref([]);   

// Função checkRole para verificar o nível/cargo do usuário
const checkRole = (newRole) => {
  if (newRole === 'Operador') {
    showRoleModal.value = true;
  } else {
    showRoleModal.value = false;
  }
};

// Função para abrir o modal e carregar as permissões existentes
const openRoleModal = () => {
  if (form.id) {
    axios.get(`/users/${form.id}/permissions`).then(response => {
      selectedActions.value = response.data.actions; 
      selectedPages.value = response.data.pages; 
      showRoleModal.value = true;
    });
  }
};

// Salvar as roles e fechar o modal
const handleSaveRoles = (data) => { 
  selectedActions.value = data.actions;
  selectedPages.value = data.pages;
  closeRoleModal();
};

const closeRoleModal = () => {
  showRoleModal.value = false;
};

// Sincronizar mudanças do formData
watch(
  () => props.formData,
  (newData) => {
    Object.assign(form, newData);
  }
);

// Enviar o formulário e as permissões associadas
const formErrors = ref({});  // Armazenar os erros por campo

// Enviar o formulário e tratar as permissões associadas
const submitForm = () => {
  
  const routeName = props.isEditing ? props.updateRoute : props.createRoute;
  const method = props.isEditing ? 'put' : 'post';
  const routeParams = props.isEditing ? { id: props.formData.id } : {};
  console.log(routeName);
  formErrors.value = {};  // Limpar os erros ao submeter o formulário

  useForm(form).submit(method, route(routeName, routeParams), {
    onSuccess: () => {
     
      emit('cancel');
      router.visit(route(props.returnRoute));
    },
    onError: (errors) => {
      formErrors.value = errors;  // Armazenar os erros vindos do backend
     
    }
  });
};

</script>


<style scoped>
.v-card {
  background-color: #f8f9fa;
}
</style>
