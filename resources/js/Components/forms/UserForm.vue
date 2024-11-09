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
          <template v-if="field.mask && field.type !== 'select'">
            <v-text-field
              v-model="form[key]"
              :label="field.label"
              :type="field.type || 'text'"
              :rules="(field.rules || []).concat([(v) => formErrors[key] ? formErrors[key][0] : true])"
              :error="!!formErrors[key]"
              :error-messages="formErrors[key] ? formErrors[key] : []"
              :required="field.required"
              :autocomplete="field.autocomplete"
              v-mask="field.mask"
            />
          </template>
          <template v-else-if="field.type !== 'select'">
            <v-text-field
              v-model="form[key]"
              :label="field.label"
              :type="field.type || 'text'"
              :rules="(field.rules || []).concat([(v) => formErrors[key] ? formErrors[key][0] : true])"
              :error="!!formErrors[key]"
              :error-messages="formErrors[key] ? formErrors[key] : []"
              :required="field.required"
              :autocomplete="field.autocomplete"
            />
          </template>

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

        <ControladoraSelect
          v-if="showCreateControladora"
          v-model="form.controladora"
          label="Selecione uma Controladora"
          :rules="[v => !!v || 'A seleção de uma controladora é obrigatória']"
        />

        <regioes-select
          v-if="showCreateRegiao"
          v-model="form.regiao"
          label="Selecione uma região"
          :rules="[v => !!v || 'A seleção de uma região é obrigatória']"
        />
      </v-form>

      <v-row class="d-flex align-center mb-4">
        <v-col cols="auto">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                v-if="isAdmin && isEditing && isUsersPage"
                icon
                v-bind="attrs"
                v-on="on"
                @click="openRoleModal"
                title="Alterar permissões do usuário"
              >
                <v-icon>mdi-account-details</v-icon>
              </v-btn>
            </template>
            <span>Alterar Permissões</span>
          </v-tooltip>
        </v-col>
        
        <v-col cols="auto">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                v-if="isAdmin && isEditing && isUsersPage"
                icon
                v-bind="attrs"
                v-on="on"
                @click="resetaSenhaUser"
                title="Redefinir senha do usuário para 'wnidobrasil'"
              >
                <v-icon>mdi-lock-off</v-icon>
              </v-btn>
            </template>
            <span>Redefinir Senha</span>
          </v-tooltip>
        </v-col>
      </v-row>

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

      <v-snackbar
        v-model="snackbar.show"
        :timeout="snackbar.timeout"
        top
        color="success"
      >
        {{ snackbar.text }}
      </v-snackbar>
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
import { useForm, router, usePage } from '@inertiajs/vue3';
import RegioesSelect from '../RegioesSelect.vue';
import RolesModal from '../RolesModal.vue';
import axios from 'axios';
import ControladoraSelect from '../ControladoraSelect.vue';

const page = usePage();
const isAdmin = ref(page.props.auth.user.nivel === 'Administrador');
const isUsersPage = window.location.pathname === '/users/' || window.location.pathname === '/users';

const props = defineProps({
  formData: { type: Object, default: () => ({}) },
  fields: { type: Object, required: true },
  showCreateRegiao: { type: Boolean, default: false },
  showCreateControladora: { type: Boolean, default: false },
  isEditing: Boolean,
  title: { type: String, default: 'Item' },
  createRoute: { type: String, required: true },
  updateRoute: { type: String, required: true },
  returnRoute: { type: String, required: true },
});

const emit = defineEmits(['cancel']);
const form = reactive({ ...props.formData });
const valid = ref(true);
const formErrors = ref({});
const snackbar = reactive({ show: false, text: '', timeout: 3000 });

const showRoleModal = ref(false);
const selectedActions = ref([]);
const selectedPages = ref([]);
const actions = ref([]); 
const pages = ref([]);   

const checkRole = (newRole) => {
  showRoleModal.value = newRole === 'Operador';
};

const openRoleModal = () => {
  if (form.id) {
    axios.get(`/users/${form.id}/permissions`).then(response => {
      selectedActions.value = response.data.actions; 
      selectedPages.value = response.data.pages; 
      showRoleModal.value = true;
    });
  }
};

const resetaSenhaUser = () => {
  if (form.id) {
    axios.get(`/users/${form.id}/resetSenha`).then(() => {
      snackbar.text = 'Senha redefinida com sucesso para "wnidobrasil"';
      snackbar.show = true;
    });
  }
};

const handleSaveRoles = (data) => {   
  selectedActions.value = data.actions;
  selectedPages.value = data.pages;
  closeRoleModal();
};

const closeRoleModal = () => {
  showRoleModal.value = false;
};

watch(
  () => props.formData,
  (newData) => {
    Object.assign(form, newData);
  }
);

const submitForm = () => {
  const routeName = props.isEditing ? props.updateRoute : props.createRoute;
  const method = props.isEditing ? 'put' : 'post';
  const routeParams = props.isEditing ? { id: props.formData.id } : {};

  form.selectedActions = selectedActions.value;
  form.selectedPages = selectedPages.value;
  formErrors.value = {};

  useForm(form).submit(method, route(routeName, routeParams), {
    onSuccess: () => {
      emit('cancel');
      router.visit(route(props.returnRoute));
    },
    onError: (errors) => {
      formErrors.value = errors;
    }
  });
};
</script>

<style scoped>
.v-card {
  background-color: #f8f9fa;
}
</style>
