<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

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
    logout() {
      this.$inertia.post(route('logout'));
    },
    
    
  }
  
};
</script>

<template>
    <v-app class="rounded rounded-md">
      <v-app-bar
        scroll-behavior="elevate" 
        elevation=5
        color="grey-lighten-3"
        density="compact"
      >
        <template v-slot:prepend>
         
        </template>

        <v-app-bar-title class="text-center">Sistema de Radio</v-app-bar-title>

        <template v-slot:append>
  <v-menu offset-y>
    <template v-slot:activator="{ props }">
      <v-btn v-bind="props">
        <v-avatar size="32" color="primary">
          <template v-if="$page.props.auth.user.image">
            <v-img :src="$page.props.auth.user.image" alt="User Image"></v-img>
          </template>
          <template v-else>
            {{ $page.props.auth.user.name.charAt(0) }}
          </template>
        </v-avatar>
      </v-btn>
    </template>
    <v-list>
      <v-list-item @click="navigateTo(route('profile.edit'))">
        <v-list-item-title>Profile</v-list-item-title>
      </v-list-item>
      <v-list-item @click="logout">
        <v-list-item-title>Log Out</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

      </v-app-bar>
  
      <v-navigation-drawer expand-on-hover rail mobile-breakpoint="3">
            
            <v-card elevation="2">
            <v-card-actions>
              <v-list>
              <v-list-item prepend-avatar="/images/sam-banner.png" title="Home" @click="navigateTo('/')"></v-list-item>
            </v-list>
            </v-card-actions>
          </v-card>

            <v-divider></v-divider>

            <v-list v-model:opened="open">
              <!-- Radios Menu -->
              <v-list-group v-model="menuStates.radio" value="Radios">
                <template v-slot:activator="{ props }">
                  <v-list-item v-bind="props" prepend-icon="mdi-radio-tower" title="Radios"></v-list-item>
                </template>
                
                <v-list-item title="Relatorio de Radios" @click="navigateTo('/radios/RelatoriosRadios')"></v-list-item>
                <v-list-item title="Mapa de Radios" @click="navigateTo('/radios/mapaRadio')"></v-list-item>
                <v-list-item title="Configurar Radios" @click="navigateTo('/radios/configurar')"></v-list-item>
                <v-list-item title="Rastrear Radios" @click="navigateTo('/radios/track')"></v-list-item>
                <v-list-item title="Adicionar Radios" @click="navigateTo('/radios/')"></v-list-item>                
                <v-list-item title="Listar Radios" @click="navigateTo('/radios/')"></v-list-item>
              </v-list-group>

              <!-- Usuários Menu -->
              <v-list-group v-model="menuStates.usuarios" value="Usuários">
                <template v-slot:activator="{ props }">
                  <v-list-item v-bind="props" prepend-icon="mdi-account" title="Usuários"></v-list-item>
                </template> 
                <v-list-item title="Lista Usuários Dashboard" @click="navigateTo('/users/')"></v-list-item>
                <v-list-item title="Lista Usuários Radio" @click="navigateTo('/usuarios/')"></v-list-item>
              </v-list-group>

              <!-- Regiao Menu -->
              <v-list-group v-model="menuStates.usuarios" value="Regiões">
                <template v-slot:activator="{ props }">
                  <v-list-item v-bind="props" prepend-icon="mdi-city" title="Regiões"></v-list-item>
                </template> 
                <v-list-item title="Lista Rregioes" @click="navigateTo('/regioes/')"></v-list-item>
                
              </v-list-group>

              <!-- Campanhas Menu -->
              <v-list-group v-model="menuStates.campanhas" value="Campanhas">
                <template v-slot:activator="{ props }">
                  <v-list-item v-bind="props" prepend-icon="mdi-bullhorn" title="Campanhas"></v-list-item>
                </template>
                <v-list-item title="Adicionar Campanha" @click="navigateTo('/campanhas/adicionar')"></v-list-item>                
                <v-list-item title="Listar Campanhas" @click="navigateTo('/campanhas/')"></v-list-item>
              </v-list-group>
              <!-- Wi-fi Menu -->
              <v-list-group v-model="menuStates.wifi" value="Wifi">
                <template v-slot:activator="{ props }">
                  <v-list-item v-bind="props" prepend-icon="mdi-wifi-settings" title="Wi-Fi"></v-list-item>
                </template>
                <v-list-item title="Adicionar Ap" @click="navigateTo('/radios/')"></v-list-item>                
                <v-list-item title="Listar Aps" @click="navigateTo('/radios/')"></v-list-item>
              </v-list-group>

              <!-- Radius, FAQ, and Logs -->
              <v-list-item title="Personaliza Hotspot" prepend-icon="mdi-router-wireless-settings" @click="navigateTo('/login_customizations')"></v-list-item>
              <v-list-item title="Radius" prepend-icon="mdi-server" @click="navigateTo('/radius')"></v-list-item>
              <v-list-item title="FAQ" prepend-icon="mdi-help" @click="navigateTo('/faq')"></v-list-item>
              <v-list-item title="Logs" prepend-icon="mdi-archive" @click="navigateTo('/logs')"></v-list-item>
            </v-list>
          </v-navigation-drawer>
  
      <v-main class="d-flex align-top justify-top" style="min-height: 300px;" >
        <slot />
      </v-main>
    </v-app>
  </template>
  <style>

</style>