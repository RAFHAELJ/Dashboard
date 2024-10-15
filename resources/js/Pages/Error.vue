<!-- resources/js/Pages/ErrorPage.vue -->
<template>
    <AuthenticatedLayout>
      <v-container fluid class="d-flex align-center justify-center fill-height">
        <v-card elevation="3" class="error-card pa-5" max-width="600">
          <v-row align="center" justify="center">
            <v-col cols="12" class="text-center">
              <v-icon size="64" color="error" class="mb-4">mdi-alert-circle</v-icon>
              <h2 class="mb-2">Oops! Algo deu errado.</h2>
              <p>{{ message }}</p>
            </v-col>
            <v-col cols="12" class="text-center mt-4">
              <v-btn color="primary" @click="goBack">Voltar</v-btn>
              <v-btn color="secondary" @click="goHome" class="ml-2">Ir para o Início</v-btn>
            </v-col>
          </v-row>
        </v-card>
      </v-container>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { usePage, router } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  
  const { props } = usePage();
  const message = props.error || 'Houve um problema ao carregar esta página. Por favor, tente novamente mais tarde.';
  
  const goBack = () => {
    window.history.back();
  };
  
  const goHome = () => {
    router.visit('/');
  };
  </script>
  
  <style scoped>
  .error-card {
    text-align: center;
    background-color: #fff;
  }
  
  .error-card h2 {
    color: #d32f2f;
  }
  
  .error-card p {
    color: #666;
    font-size: 16px;
  }
  
  @media (max-width: 600px) {
    .error-card {
      padding: 20px;
    }
  }
  </style>
  