<template>
    <div id="app">
      <nav class="navbar navbar-custom">
        <button class="btn btn-outline-secondary d-md-none" @click="toggleSidebar">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="company-name">Nome da Empresa</h1>
      </nav>
      <div :class="['sidebar', { 'sidebar-hidden': isSidebarHidden, 'sidebar-mobile-open': isMobileSidebarOpen }]">
        <div class="logo text-center my-4">
          <img src="../../../images/sam-banner.png" alt="Logo" class="rounded-circle" width="80" height="80">
        </div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <router-link class="nav-link" to="/" @click.native="closeAllMenus">Home</router-link>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="toggleMenu('radio')">
              Radios
              <i :class="['fas', menuStates.radio ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </a>
            <ul v-show="menuStates.radio" class="submenu">
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/radios/personaliza-hotspot" @click.native="closeAllMenus">Personaliza Hotspot</router-link>
              </li>
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/radios/mapa" @click.native="closeAllMenus">Mapa de Radios</router-link>
              </li>
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/radios/configurar" @click.native="closeAllMenus">Configurar Radios</router-link>
              </li>
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/radios/rastrear" @click.native="closeAllMenus">Rastrear Radios</router-link>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="toggleMenu('usuarios')">
              Usuários
              <i :class="['fas', menuStates.usuarios ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </a>
            <ul v-show="menuStates.usuarios" class="submenu">
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/usuarios/novo-radio" @click.native="closeAllMenus">Novo Usuário Radio</router-link>
              </li>
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/usuarios/novo-dashboard" @click.native="closeAllMenus">Novo Usuário Dashboard</router-link>
              </li>
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/usuarios/lista-dashboard" @click.native="closeAllMenus">Lista Usuários Dashboard</router-link>
              </li>
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/usuarios/lista-radio" @click.native="closeAllMenus">Lista Usuários Radio</router-link>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link d-flex justify-content-between align-items-center" @click.prevent="toggleMenu('campanhas')">
              Campanhas
              <i :class="['fas', menuStates.campanhas ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </a>
            <ul v-show="menuStates.campanhas" class="submenu">
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/campanhas/adicionar" @click.native="closeAllMenus">Adicionar Campanha</router-link>
              </li>
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/campanhas/alterar" @click.native="closeAllMenus">Alterar Campanha</router-link>
              </li>
              <li class="nav-item submenu-item">
                <router-link class="nav-link" to="/campanhas/listar" @click.native="closeAllMenus">Listar Campanhas</router-link>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/radius" @click.native="closeAllMenus">Radius</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/faq" @click.native="closeAllMenus">FAQ</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/logs" @click.native="closeAllMenus">Logs</router-link>
          </li>
        </ul>
      </div>
      <div :class="['content', { 'content-expanded': isSidebarHidden }]">
        <router-view></router-view>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'App',
    data() {
      return {
        isSidebarHidden: false,
        isMobileSidebarOpen: false,
        menuStates: {
          radio: false,
          usuarios: false,
          campanhas: false
        }
      };
    },
    methods: {
      toggleSidebar() {
        this.isSidebarHidden = !this.isSidebarHidden;
        if (window.innerWidth < 768) {
          this.isMobileSidebarOpen = !this.isMobileSidebarOpen;
        }
      },
      toggleMenu(menuName) {
        // Fecha todos os outros menus
        for (let menu in this.menuStates) {
          if (menu !== menuName) {
            this.$set(this.menuStates, menu, false);
          }
        }
        // Alterna o menu selecionado
        this.$set(this.menuStates, menuName, !this.menuStates[menuName]);
      },
      closeAllMenus() {
        for (let menu in this.menuStates) {
          this.$set(this.menuStates, menu, false);
        }
        // Fecha a sidebar em dispositivos móveis após a seleção
        if (window.innerWidth < 768) {
          this.isMobileSidebarOpen = false;
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
      // Fecha a sidebar por padrão em dispositivos móveis
      if (window.innerWidth < 768) {
        this.isSidebarHidden = true;
      }
      // Adiciona listener para ajustar a sidebar ao redimensionar a janela
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
  