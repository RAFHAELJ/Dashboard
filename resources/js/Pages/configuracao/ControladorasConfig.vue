<template>
  <div>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
      <template v-slot="{ canAccess }">
        <v-container fluid fill-height class="main-container">
          <!-- Barra de Navegação -->
          <v-app-bar app color="primary" dark>
            <v-toolbar-title>Configurar Controladoras</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="fetchServers(currentPage)" title="Atualizar">
              <v-icon>mdi-refresh</v-icon>
            </v-btn>
          </v-app-bar>

          <!-- Card Principal para Agrupar os Monitores -->
          <v-card class="dashboard-card" :elevation="6">
            <v-card-title class="text-center">Monitor de Controladoras</v-card-title>
            <v-card-subtitle class="text-center">Visualize e gerencie as controladoras conectadas</v-card-subtitle>

            <!-- Linha de Monitores -->
            <v-row class="monitor-row mt-4" align="start" justify="center" v-if="canAccess('Dashboard', 'ler')">
              <v-col v-for="server in accessData.data" :key="server.id" cols="12" sm="6" md="4" lg="3">
                <!-- Card Envolvendo Cada Monitor -->
                <v-card class="monitor-wrapper" :elevation="3">
                  <div class="monitor-card">
                    <div class="monitor-screen">
                      <v-card 
                        class="server-card"
                        :elevation="1"
                        @click="redirectToHost(server)"
                        style="cursor: pointer;"
                      >
                        <v-card-title>
                          <v-icon>{{ server.icon }}</v-icon>
                          <span class="ms-2">{{ server.nome }}</span>
                        </v-card-title>
                        <v-card-subtitle>IP: {{ server.ip }}</v-card-subtitle>
                        <v-card-subtitle>Região: {{ server.regiao?.cidade || 'Não especificada' }}</v-card-subtitle>
                        <v-card-subtitle>Senha: {{ server.senha }}</v-card-subtitle>
                        <v-card-text>Informações: {{ server.info }}</v-card-text>
                      </v-card>
                    </div>
                    <div class="monitor-stand"></div>
                  </div>
                </v-card>
              </v-col>
            </v-row>

            <!-- Paginação Centralizada -->
            <v-row justify="center" class="mt-4">
              <v-pagination
                v-model="currentPage"
                :length="accessData.last_page"
                @input="fetchServers"
                prev-icon="mdi-arrow-left"
                next-icon="mdi-arrow-right"
              ></v-pagination>
            </v-row>
          </v-card>
        </v-container>
      </template>
    </AuthenticatedLayout>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const { props } = usePage();
const accessData = computed(() => props.accessData || { data: [], last_page: 1, current_page: 1 });

const dialog = ref(false);
const currentPage = ref(accessData.value.current_page);

const newServer = ref({
  nome: '',
  ip: '',
  local: '',
  description: ''
});

const saveServer = () => {
  accessData.value.data.push({ ...newServer.value, id: Date.now(), icon: 'mdi-server', online: false });
  newServer.value = { nome: '', ip: '', local: '', description: '' };
  dialog.value = false;
};

const redirectToHost = (server) => {
  let url;
  const isIpAddress = /^(?:\d{1,3}\.){3}\d{1,3}$/.test(server.ip);

  if (isIpAddress) {
    url = `http://${server.ip}:${server.porta}`;
  } else {
    const protocol = server.ip.includes('http') ? 'http' : 'https';
    url = `${protocol}://${server.ip}`;
  }

  window.open(url, '_blank');
};

const fetchServers = (page) => {
  router.visit(route('controladora.index', { page }), {
    method: 'get',
    replace: true,
    preserveState: true,
  });
};
</script>

<style scoped>
.main-container {
  background: linear-gradient(to right, #f0f4f8, #d9e4ec);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
}

.dashboard-card {
  width: 98%;
  height: 98vh;
  padding: 20px;
  border-radius: 12px;
  background-color: #ffffff;
  box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
}

.monitor-wrapper {
  padding: 10px;
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

.monitor-card {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.monitor-screen {
  width: 100%;
  background-color: #ffffff;
  border: 2px solid #333;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

.server-card {
  background-color: #f9f9f9;
  transition: transform 0.2s ease;
}

.server-card:hover {
  transform: scale(1.02);
}

.monitor-stand {
  width: 40px;
  height: 10px;
  background-color: #333;
  border-radius: 4px;
  margin-top: 10px;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
}

.v-pagination {
  color: #1976D2;
}
</style>
