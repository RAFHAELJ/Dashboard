<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link , usePage} from '@inertiajs/vue3';


// Obtém as permissões compartilhadas no Inertia
const page = usePage();
const permissions = page.props.permissions || {};


// Função para verificar se o usuário pode acessar uma página e uma ação
const canAccess = (pageSlug, action = null) => {
  // Verifica se as permissões são um array e contém 'all' (Administrador)
  if (Array.isArray(permissions) && permissions.includes('all')) {
    return true;
  }

  // Verifica se `permissions[pageSlug]` existe
  if (permissions[pageSlug]) {
    const pagePermissions = permissions[pageSlug];

    // Verifica se `pagePermissions` é um array
    if (Array.isArray(pagePermissions)) {
      // Se a ação não for especificada, verifica se qualquer permissão está disponível
      if (!action) return true;

      // Verifica se a ação está incluída nas permissões
      return pagePermissions.includes(action);
    }

    // Caso `pagePermissions` seja um objeto (ou Proxy com chaves numéricas)
    if (typeof pagePermissions === 'object') {
      // Extrai os valores do objeto e verifica se algum valor corresponde à ação
      const permissionsArray = Object.values(pagePermissions);
      
      if (!action) return true;
      return permissionsArray.includes(action);
    }
  }

  // Se não houver permissões para a página ou a ação específica, retorna false
  return false;
};



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
        <!-- Aqui você pode adicionar mais elementos no menu superior se necessário -->
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
            <!-- Exibe o item Home se o usuário tiver permissão -->
            <v-list-item
              v-if="canAccess('home')"
              prepend-avatar="/images/sam-banner.png"
              title="Home"
              @click="navigateTo('/')"
            ></v-list-item>
          </v-list>
        </v-card-actions>
      </v-card>

      <v-divider></v-divider>

      <v-list v-model:opened="open">
        <!-- Radios Menu (exibe se tiver permissão para acessar radios) -->
        <v-list-group v-if="canAccess('radios')" v-model="menuStates.radio" value="Radios">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-radio-tower" title="Radios"></v-list-item>
          </template>
          
          <v-list-item v-if="canAccess('radios', 'ler')" title="Relatorio de Radios" @click="navigateTo('/radios/RelatoriosRadios')"></v-list-item>
          <v-list-item v-if="canAccess('mapaRadios', 'ler')" title="Mapa de Radios" @click="navigateTo('/radios/mapaRadio')"></v-list-item>
          <v-list-item v-if="canAccess('incluirRadios', 'gravar')" title="Adicionar/Listar Radios" @click="navigateTo('/radios/')"></v-list-item>
          <v-list-item v-if="canAccess('configurarRadios', 'atualizar')" title="Configurar Radios" @click="navigateTo('/radios/configurar')"></v-list-item>
          <v-list-item v-if="canAccess('rastrearRadio', 'rastrear')" title="Rastrear Radios" @click="navigateTo('/radios/track')"></v-list-item>
        </v-list-group>

        <!-- Usuários Menu -->
        <v-list-group v-if="canAccess('users','ler')" v-model="menuStates.usuarios" value="Usuários">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-account" title="Usuários"></v-list-item>
          </template> 
          <v-list-item v-if="canAccess('usuariosDash', 'ler')" title="Lista Usuários Dashboard" @click="navigateTo('/users/')"></v-list-item>
          <v-list-item v-if="canAccess('usuariosRadio', 'ler')" title="Lista Usuários Radio" @click="navigateTo('/usuarios/')"></v-list-item>
        </v-list-group>

        <!-- Regioes Menu -->
        <v-list-group v-if="canAccess('regioes')" v-model="menuStates.usuarios" value="Regiões">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-city" title="Regiões"></v-list-item>
          </template> 
          <v-list-item v-if="canAccess('regioes', 'ler')" title="Lista Regiões" @click="navigateTo('/regioes/')"></v-list-item>
        </v-list-group>

        <!-- Campanhas Menu -->
        <v-list-group v-if="canAccess('campanhas')" v-model="menuStates.campanhas" value="Campanhas">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-bullhorn" title="Campanhas"></v-list-item>
          </template>
      
          <v-list-item v-if="canAccess('login_customizations', 'ler')" title="Lista Hotspot" @click="navigateTo('/login_customizations')"></v-list-item>
          <v-list-item v-if="canAccess('login_customizations', 'gravar')" title="Personaliza Hotspot" @click="navigateTo('/login_customizations/create')"></v-list-item>

          <v-list-item v-if="canAccess('campanhas', 'gravar')" title="Adicionar Campanha" @click="navigateTo('/campanhas/adicionar')"></v-list-item>
          <v-list-item v-if="canAccess('campanhas', 'ler')" title="Listar Campanhas" @click="navigateTo('/campanhas/')"></v-list-item>
        </v-list-group>

                <!-- Personalizar Hotspot -->
                <v-list-group v-if="canAccess('Personalizar Hotspot')" v-model="menuStates.personalizaHotSpot" value="Personalizar Hotspot">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-router-wireless-settings" title="Personalizar Hotspot"></v-list-item>
          </template>
          
          <v-list-item v-if="canAccess('login_customizations', 'ler')" title="Lista Hotspot" @click="navigateTo('/login_customizations')"></v-list-item>
          <v-list-item v-if="canAccess('login_customizations', 'gravar')" title="Personaliza Hotspot" @click="navigateTo('/login_customizations/create')"></v-list-item>

        </v-list-group>

        <!-- Radius, FAQ, and Logs -->
        <v-list-item v-if="canAccess('radius', 'ler')" title="Radius" prepend-icon="mdi-server" @click="navigateTo('/radius')"></v-list-item>
        <v-list-item v-if="canAccess('faq', 'ler')" title="FAQ" prepend-icon="mdi-help" @click="navigateTo('/faq')"></v-list-item>
        <v-list-item v-if="canAccess('logs', 'ler')" title="Logs" prepend-icon="mdi-archive" @click="navigateTo('/logs')"></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main class="d-flex align-top justify-top" style="min-height: 300px;">
      <slot :canAccess="canAccess" />
    </v-main>
  </v-app>
</template>

  <style>

</style>