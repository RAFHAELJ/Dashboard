<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    
      
    
      
  </AuthenticatedLayout>
</template>

<script>
export default {
  data() {
    return {
      open: [],  // Inicia com todos os menus recolhidos
      menuStates: {
        radio: false,
        usuarios: false,
        campanhas: false,
      },
      isSidebarHidden: false,
      isMobileSidebarOpen: false,
    };
  },
  methods: {

  },
  mounted() {
  }
    
};
</script>

