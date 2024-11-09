<template>


  <v-app class="rounded rounded-md">
    <v-app-bar scroll-behavior="elevate" elevation="5" color="#2f2f2f">
      <template v-slot:prepend>
        <img src="../../images/LogoSf.png" alt="Logo" class="w-16 h-12">
        <!-- Aqui você pode adicionar mais elementos no menu superior se necessário -->
      </template>

      <v-app-bar-title class="text-center">WNIDashboard</v-app-bar-title>

      <template v-slot:append>
        <!-- Adicionando RegioesSelect com v-model para capturar a região selecionada -->
        <template v-if="isAdmin">
          <RegioesSelect
            v-model="selectedRegiao"
            label="Selecione a Região"
            :rules="[v => !!v || 'Região é obrigatória']"
            @update:modelValue="updateRegiao"
            :style="{ marginTop: '20px' }"
          />
        </template>
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
            <v-list-item @click="navigateTo(route('password.change'))">
              <v-list-item-title>Trocar Senha</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
    </v-app-bar>

    <v-navigation-drawer expand-on-hover rail mobile-breakpoint="3" elevation="5" :width="300">
      <v-card elevation="2">
        <v-card-actions>
          <v-list nav density="compact">
            <v-list-item v-if="canAccess('home')" prepend-icon="mdi-home" title="Home" @click="navigateTo('/')"></v-list-item>
          </v-list>
        </v-card-actions>
      </v-card>

      <v-divider></v-divider>

      <v-list v-model:opened="open">
        <!-- Radios Menu -->
        <v-list-group v-if="canAccess('radios')" v-model="menuStates.radio" value="Radios">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-radio-tower" title="Radios"></v-list-item>
          </template>
          <v-list-item v-if="canAccess('mapaRadios', 'ler')" title="Mapa de Radios" @click="navigateTo('/radios/mapaRadio')"></v-list-item>
          <v-list-item v-if="canAccess('incluirRadios', 'gravar')" title="Controle Radios" @click="navigateTo('/radios/')"></v-list-item>
          <v-list-item v-if="canAccess('rastrearRadio', 'ler')" title="Rastrear Radios" @click="navigateTo('/radios/track')"></v-list-item>
          <v-list-item v-if="canAccess('rastrearUsoRadio', 'ler')" title="Usabilidade Radios" @click="navigateTo('/radios/basetrack')"></v-list-item>
        </v-list-group>

        <!-- Usuários Menu -->
        <v-list-group v-if="canAccess('users','ler')" v-model="menuStates.usuarios" value="Usuários">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-account" title="Usuários"></v-list-item>
          </template>
          <v-list-item v-if="canAccess('usuariosDash', 'ler')" title="Lista Usuários Dashboard" @click="navigateTo('/users/')"></v-list-item>
          <v-list-item v-if="canAccess('usuariosRadio', 'ler')" title="Lista Usuários Wifi" @click="navigateTo('/usuarios/')"></v-list-item>
        </v-list-group>

        <!-- Campanhas Menu -->
        <v-list-group v-if="canAccess('campanhas')" v-model="menuStates.campanhas" value="Campanhas">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-bullhorn" title="Campanhas"></v-list-item>
          </template>
          <!--v-list-item v-if="canAccess('campanhas', 'gravar')" title="Adicionar Campanha" @click="navigateTo('/campanhas/adicionar')"></v-list-item-->
          <v-list-item v-if="canAccess('campanhas', 'ler')" title="Listar Campanhas" @click="navigateTo('/campanhas/')"></v-list-item>
        </v-list-group>

        <!-- Personalizar Hotspot -->
        <v-list-group v-if="canAccess('login_customizations')" v-model="menuStates.personalizaHotSpot" value="Personalizar Hotspot">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-cellphone-settings" title="Personalizar Hotspot"></v-list-item>
          </template>
          <v-list-item v-if="canAccess('login_customizations', 'ler')" title="Lista Hotspot" @click="navigateTo('/login_customizations')"></v-list-item>
          <v-list-item v-if="canAccess('login_customizations', 'gravar')" title="Personaliza Hotspot" @click="navigateTo('/login_customizations/create')"></v-list-item>
        </v-list-group>

        <!-- Configurações Menu -->
        <v-list-group v-if="canAccess('configuracoes')" v-model="menuStates.configuracoes" value="Configuracoes">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-cog" title="Configurações"></v-list-item>
          </template>
          <v-list-item v-if="canAccess('database', 'ler')" title="Database" @click="navigateTo('/database')"></v-list-item>
          <v-list-item v-if="canAccess('controladora', 'ler')" title="Controladora" @click="navigateTo('/controladora')"></v-list-item>
          <v-list-item v-if="canAccess('radius', 'ler')" title="Radius" @click="navigateTo('/radius')"></v-list-item>
          <v-list-item v-if="canAccess('regioes', 'ler')" title="Lista Regiões" @click="navigateTo('/regioes/')"></v-list-item>
        </v-list-group>

        <!-- Controladoras Menu -->
        <v-list-group v-if="canAccess('controladora')" v-model="menuStates.ConfiguracoesControladora" value="Controladoras">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-router-wireless-settings" title="Controladoras"></v-list-item>
          </template>
          <v-list-item v-if="canAccess('controladora', 'ler')" title="Config Controladoras" @click="navigateTo('/controladora/controladora')"></v-list-item>
        </v-list-group>

        <!-- Radius Menu -->
        <v-list-group v-if="canAccess('radius')" v-model="menuStates.radius" value="Radius">
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" prepend-icon="mdi-server" title="Radius"></v-list-item>
          </template>
          <!--v-list-item v-if="canAccess('radius', 'ler')" title="Lista Radius" @click="navigateTo('/radius/lista')"></v-list-item-->
          <v-list-item v-if="canAccess('radius', 'ler')" title="Adiciona Nas" @click="navigateTo('/radius/')"></v-list-item>
        </v-list-group>

        <!-- Outros Menus -->
        <v-list-item v-if="canAccess('faq', 'ler')" title="FAQ" prepend-icon="mdi-help" @click="navigateTo('/faq')"></v-list-item>
        <v-list-item v-if="canAccess('logs', 'ler')" title="Logs" prepend-icon="mdi-archive" @click="navigateTo('/logs')"></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main class="d-flex align-top justify-top" style="min-height: 300px; background-color: #f4f4f9">
      <slot :canAccess="canAccess" />
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import RegioesSelect from '@/Components/RegioesSelect.vue';

const page = usePage();
const permissions = page.props.permissions || {};
const isAdmin = ref(page.props.auth.user.nivel === 'Administrador');
const userRegiao = page.props.auth.user.regiao;
const selectedRegiao = ref(parseInt(localStorage.getItem('user_regiao')) || userRegiao);

const updateRegiao = (newRegiao) => {
  const storedRegiao = parseInt(localStorage.getItem('user_regiao'));
  if (storedRegiao === parseInt(newRegiao)) return;

  selectedRegiao.value = parseInt(newRegiao);
  localStorage.setItem('user_regiao', selectedRegiao.value);

  axios
    .post(route('update.region.connection'), { regiao: selectedRegiao.value })
    .catch((error) => {
      console.error('Erro ao salvar a região na sessão:', error);
    });
};

if (!localStorage.getItem('user_regiao')) {
  updateRegiao(userRegiao);
}

const canAccess = (pageSlug, action = null) => {
  if (Array.isArray(permissions) && permissions.includes('all')) return true;

  if (permissions[pageSlug]) {
    const pagePermissions = permissions[pageSlug];

    if (Array.isArray(pagePermissions)) {
      if (!action) return true;
      return pagePermissions.includes(action);
    }

    if (typeof pagePermissions === 'object') {
      const permissionsArray = Object.values(pagePermissions);
      if (!action) return true;
      return permissionsArray.includes(action);
    }
  }

  return false;
};

const showingNavigationDropdown = ref(false);
</script>

<script>
export default {
  data() {
    return {
      open: [],
      menuStates: {
        radio: false,
        usuarios: false,
        campanhas: false,
        personalizaHotSpot: false,
        configuracoes: false,
        ConfiguracoesControladora: false,
        radius: false,
      },
    };
  },
  methods: {
    navigateTo(route) {
      this.$inertia.visit(route);
    },
    logout() {
      localStorage.removeItem('user_regiao');
      this.$inertia.post(route('logout'));
    },
  },
};
</script>

<style scoped>
.fixed-cards {
  margin-top: 10px;
}
</style>
