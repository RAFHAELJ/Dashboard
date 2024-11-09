<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Obtendo o ID do usuário a partir das props do Inertia
const { props } = usePage();
const userId = props.auth?.user?.id || null;
console.log('ID do usuário:', userId);

// Variáveis para armazenar os dados da senha
const password = ref('');
const passwordConfirmation = ref('');
const errors = ref({});

// Função para enviar a nova senha ao backend usando o método PUT
const handleSubmit = () => {
    // Resetando erros
    errors.value = {};

    // Enviando a nova senha ao backend usando o método PUT
    router.put(route('password.update'), {
        password: password.value,
        password_confirmation: passwordConfirmation.value,
    }, {
        onSuccess: () => {
            alert('Senha atualizada com sucesso!');
            password.value = '';
            passwordConfirmation.value = '';
        },
        onError: (responseErrors) => {
            errors.value = responseErrors;
            console.error('Erro ao atualizar a senha:', responseErrors);
        },
    });
};
</script>

<template>
  <AuthenticatedLayout>
    <v-container class="fill-height" fluid>
      <v-card elevation="2" max-width="500" class="mx-auto pa-4">
        <v-card-title class="text-h5">Trocar Senha</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="handleSubmit">
            <!-- Campo para a nova senha -->
            <v-text-field
              v-model="password"
              label="Nova Senha"
              :error-messages="errors.password"
              type="password"
              required
              autocomplete="new-password"
            />
            
            <!-- Campo para confirmar a nova senha -->
            <v-text-field
              v-model="passwordConfirmation"
              label="Confirmação da Senha"
              :error-messages="errors.password_confirmation"
              type="password"
              required
              autocomplete="new-password"
            />
            
            <!-- Botões de ação -->
            <v-card-actions class="justify-end">
              <v-btn color="primary" @click="handleSubmit">Salvar</v-btn>
            </v-card-actions>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </AuthenticatedLayout>
</template>

<style scoped>
.fill-height {
  min-height: 100vh;
}
</style>
