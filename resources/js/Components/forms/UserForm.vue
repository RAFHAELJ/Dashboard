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
          <v-text-field
            v-if="field.type !== 'select'"
            v-model="form[key]"
            :label="field.label"
            :type="field.type || 'text'"
            :rules="field.rules"
            :required="field.required"
            :autocomplete="field.autocomplete" 
          ></v-text-field>

          <!-- Campo de Select para Cargos -->
          <v-select
              v-if="field.type === 'select'"
              v-model="form[key]"
              :items="field.items"
              :label="field.label"
              :rules="field.rules"
              :required="field.required"
            ></v-select>
        </template>
      </v-form>

      <!-- Regiao -->
      <regioes-select
        v-if="showCreateRegiao"
        v-model="form.regiao"
        label="Selecione uma região"
        :rules="[v => !!v || 'A seleção de uma região é obrigatória']"
      ></regioes-select>
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
const emit = defineEmits(['cancel']);
const form = reactive({ ...props.formData });
const valid = ref(true);

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

  useForm(form).submit(method, route(routeName, routeParams), {
    onSuccess: () => {
      Object.keys(form).forEach((key) => {
        form[key] = ''; // Limpar os campos após a submissão
      });
      emit('cancel'); // Fechar o modal ou resetar o estado
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
