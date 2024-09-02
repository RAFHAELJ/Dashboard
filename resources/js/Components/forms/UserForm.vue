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
        <v-text-field
          v-for="(field, key) in fields"
          :key="key"
          v-model="form[key]"
          :label="field.label"
          :type="field.type || 'text'"
          :rules="field.rules"
          :required="field.required"
        ></v-text-field>
      </v-form>
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
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  formData: {
    type: Object,
    default: () => ({}),
  },
  fields: {
    type: Object,
    required: true,
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
});

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
      // Reseta os campos dinâmicos do formulário após o sucesso
      Object.keys(form).forEach((key) => {
        form[key] = '';
      });
      emit('cancel');
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
