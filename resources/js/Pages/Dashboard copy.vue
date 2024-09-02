<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    
      

      <v-card>
        <v-layout>
          <v-navigation-drawer expand-on-hover rail>
            <v-list>
              <v-list-item prepend-avatar="/images/sam-banner.png" title="Home" @click="navigateTo('/')"></v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-list v-model:opened="open">
              <!-- Radios Menu -->
              <v-list-group v-model="menuStates.radio" value="Radios">
                <template v-slot:activator="{ props }">
                  <v-list-item v-bind="props" prepend-icon="mdi-radio-tower" title="Radios"></v-list-item>
                </template>
                <v-list-item title="Personaliza Hotspot" @click="navigateTo('/radios/personaliza-hotspot')"></v-list-item>
                <v-list-item title="Mapa de Radios" @click="navigateTo('/radios/mapa')"></v-list-item>
                <v-list-item title="Configurar Radios" @click="navigateTo('/radios/configurar')"></v-list-item>
                <v-list-item title="Rastrear Radios" @click="navigateTo('/radios/rastrear')"></v-list-item>
              </v-list-group>

              <!-- Usuários Menu -->
              <v-list-group v-model="menuStates.usuarios" value="Usuários">
                <template v-slot:activator="{ props }">
                  <v-list-item v-bind="props" prepend-icon="mdi-account" title="Usuários"></v-list-item>
                </template>
                <v-list-item title="Novo Usuário Radio" @click="navigateTo('/usuarios/novo-radio')"></v-list-item>
                <v-list-item title="Novo Usuário Dashboard" @click="navigateTo('/usuarios/novo-dashboard')"></v-list-item>
                <v-list-item title="Lista Usuários Dashboard" @click="navigateTo('/usuarios/lista-dashboard')"></v-list-item>
                <v-list-item title="Lista Usuários Radio" @click="navigateTo('/usuarios/lista-radio')"></v-list-item>
              </v-list-group>

              <!-- Campanhas Menu -->
              <v-list-group v-model="menuStates.campanhas" value="Campanhas">
                <template v-slot:activator="{ props }">
                  <v-list-item v-bind="props" prepend-icon="mdi-bullhorn" title="Campanhas"></v-list-item>
                </template>
                <v-list-item title="Adicionar Campanha" @click="navigateTo('/campanhas/adicionar')"></v-list-item>
                <v-list-item title="Alterar Campanha" @click="navigateTo('/campanhas/alterar')"></v-list-item>
                <v-list-item title="Listar Campanhas" @click="navigateTo('/campanhas/listar')"></v-list-item>
              </v-list-group>

              <!-- Radius, FAQ, and Logs -->
              <v-list-item title="Radius" @click="navigateTo('/radius')"></v-list-item>
              <v-list-item title="FAQ" @click="navigateTo('/faq')"></v-list-item>
              <v-list-item title="Logs" @click="navigateTo('/logs')"></v-list-item>
            </v-list>
          </v-navigation-drawer>

          <v-main>
            <!-- Inertia will render pages here -->
            <slot />
          </v-main>
        </v-layout>
      </v-card>
    
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
    navigateTo(route) {
      this.$inertia.visit(route);
    },
    toggleSidebar() {
      this.isSidebarHidden = !this.isSidebarHidden;
      if (window.innerWidth < 768) {
        this.isMobileSidebarOpen = !this.isMobileSidebarOpen;
      }
    },
    handleResize() {
      if (window.innerWidth >= 768) {
        this.isSidebarHidden = false;
        this.isMobileSidebarOpen = false;
      } else {
        this.isSidebarHidden = true;
      }
    }
  },
  mounted() {
    if (window.innerWidth < 768) {
      this.isSidebarHidden = true;
    }
    window.addEventListener('resize', this.handleResize);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.handleResize);
  }
};
</script>

<style scoped>
/* Estilos específicos da sidebar em mobile */
.sidebar.sidebar-hidden {
  display: none;
}

.sidebar.sidebar-mobile-open {
  transform: translateX(0);
  transition: transform 0.3s ease;
}

.content-expanded {
  margin-left: 0;
}
</style>
