<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import UserForm from '@/Components/forms/UserForm.vue';

const isEditing = ref(false); // Não estamos editando um usuário existente, mas sim alterando a senha
const formData = ref({
    password: '',
    password_confirmation: '',
});

// Função para enviar a nova senha ao backend
const handleSubmit = () => {
    router.post(route('password.update'), formData.value, {
        onSuccess: () => {
            formData.value.password = '';
            formData.value.password_confirmation = '';
        }
    });
};

const fields = {
    password: { 
        label: 'Nova Senha', 
        rules: [(v) => v.length >= 6 || 'Senha deve ter no mínimo 6 caracteres'], 
        required: true, 
        type: 'password',
        autocomplete: 'new-password'
    },
    password_confirmation: {
        label: 'Confirmação da Senha',
        rules: [(v) => !!v || 'A confirmação da senha é obrigatória', 
                (v) => v === formData.value.password || 'As senhas devem coincidir'],
        required: true,
        type: 'password',
        autocomplete: 'new-password'
    }
};
</script>

<template>
  <v-container class="fill-height" fluid>
    <v-card elevation="2" max-width="500" class="mx-auto pa-4">
      <v-card-title class="text-h5">Trocar Senha</v-card-title>
      <v-card-text>
        <UserForm 
          :formData="formData"
          :fields="fields"
          :isEditing="isEditing"
          @submit="handleSubmit"
          submitLabel="Salvar"
          cancelLabel="Cancelar"
        />
      </v-card-text>
    </v-card>
  </v-container>
</template>
