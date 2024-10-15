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
            :rules="field.rules"
            :required="field.required"
            :autocomplete="field.autocomplete"
          />

          <!-- Campo de Select para Cargos -->
          <v-select
            v-if="field.type === 'select'"
            v-model="form[key]"
            :items="field.items"
            :label="field.label"
            :rules="field.rules"
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

      <!-- Componente RolesModal -->
      <RolesModal
        :show="showRoleModal"
        :roleTitle="'Operador'"
        :userId="form.id || null" 
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
import { ref, reactive, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import RegioesSelect from '../RegioesSelect.vue';
import RolesModal from '../RolesModal.vue';

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
const valid = ref(true);

// Estado para o modal de roles
const showRoleModal = ref(false);
const selectedActions = ref([]);
const selectedPages = ref([]);

// Função para verificar o cargo
const checkRole = (newRole) => {
  if (newRole === 'Operador') {
    showRoleModal.value = true;
  } else {
    showRoleModal.value = false;
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
const submitForm = () => {
  const routeName = props.isEditing ? props.updateRoute : props.createRoute;
  const method = props.isEditing ? 'put' : 'post';
  const routeParams = props.isEditing ? { id: props.formData.id } : {};

  const formData = {
    ...form,
    selectedActions: selectedActions.value,  // Enviar ações selecionadas
    selectedPages: selectedPages.value        // Enviar páginas selecionadas
  };

  useForm(formData).submit(method, route(routeName, routeParams), {
    onSuccess: () => {
      emit('cancel'); // Fechar o modal
      router.visit(route(props.returnRoute)); // Redirecionar
    },
    onError: (e) => {
      console.error('Erro ao submeter o formulário:', e);
    }
  });
};
</script>

<style scoped>
.v-card {
  background-color: #f8f9fa;
}
</style>
