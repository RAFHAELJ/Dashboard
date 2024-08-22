<template>
    <v-card>
      <v-card-title>
        {{ isEditing ? 'Editar Usuário' : 'Adicionar Usuário' }}
        <v-spacer></v-spacer>
        <v-btn icon @click="$emit('cancel')">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-form ref="userForm" v-model="valid">
          <v-text-field v-model="form.name" label="Nome" :rules="nameRules" required></v-text-field>
          <v-text-field v-model="form.email" label="Email" :rules="emailRules" required></v-text-field>
          <v-text-field
            v-if="!isEditing"
            v-model="form.password"
            label="Senha"
            type="password"
            :rules="passwordRules"
            required
          ></v-text-field>
          <v-text-field
            v-if="!isEditing"
            v-model="form.password_confirmation"
            label="Confirmar Senha"
            type="password"
            :rules="confirmPasswordRules"
            required
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
  
  // Props recebidos do componente pai
  const props = defineProps({
    user: Object,
    isEditing: Boolean
  });
  
  // Estado do formulário e regras de validação
  const form = reactive({
    name: props.user.name || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: ''
  });
  
  const nameRules = [(v) => !!v || 'Nome é obrigatório'];
  const emailRules = [
    (v) => !!v || 'Email é obrigatório',
    (v) => /.+@.+\..+/.test(v) || 'E-mail deve ser válido'
  ];
  const passwordRules = [(v) => v.length >= 6 || 'Senha deve ter no mínimo 6 caracteres'];
  const confirmPasswordRules = [
    (v) => v === form.password || 'As senhas não coincidem'
  ];
  
  // Reativo para validar o formulário
  const valid = ref(true);
  
  // Observa as mudanças no usuário e atualiza o formulário
  watch(
    () => props.user,
    (newUser) => {
      form.name = newUser.name || '';
      form.email = newUser.email || '';
      form.password = '';
      form.password_confirmation = '';
    }
  );
  
  // Função para submeter o formulário via Inertia.js
  const submitForm = () => {
    const routeName = props.isEditing ? 'usuarios1.update' : 'usuarios1.store';
    const method = props.isEditing ? 'put' : 'post';
    const routeParams = props.isEditing ? { id: props.user.id } : {};
 
    useForm(form).submit(method, route(routeName, routeParams), {
      onSuccess: () => {
        form.password = '';
        form.password_confirmation = '';
        // Fecha o modal ao salvar
        emit('cancel');
      },
      onError: (e) => {
        console.log('form');
        console.log(e);
        // Você pode adicionar lógica de tratamento de erro aqui
      }
    });
  };
  </script>
  