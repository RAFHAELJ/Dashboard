<template>
  <div>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
      <template v-slot="{ canAccess }">
        <v-container fluid fill-height>
          <v-app-bar app color="primary" dark>
          <v-toolbar-title>Tela para ajustes e controle das controladoras</v-toolbar-title>
        </v-app-bar>
        

          <!-- Cards de Servidores -->
          <v-row class="align-start" v-if="canAccess('Dashboard','ler')">
            <v-col v-for="server in accessData.data" :key="server.id" cols="12" sm="6" md="4">
              <v-card 
                class="server-card" 
                :elevation="server.online ? 12 : 2"
                @click="redirectToHost(server)"
                style="cursor: pointer;">
                <v-card-title>
                  <v-icon>{{ server.icon }}</v-icon>
                  <span class="ms-2">{{ server.nome }}</span>
                </v-card-title>
                <v-card-subtitle>
                  IP: {{ server.ip }}
                </v-card-subtitle>
                <v-card-text>
                  Local: {{ server.local }}
                  <v-chip :color="server.online ? 'green' : 'red'" class="ml-2">
                    {{ server.online ? 'Online' : 'Offline' }}
                  </v-chip>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>

          <!-- Paginação -->
          <v-pagination
            v-model="currentPage"
            :length="accessData.last_page"
            @input="fetchServers"
          ></v-pagination>

          <!-- Diálogo para adicionar servidor -->
          <v-dialog v-model="dialog" max-width="500px">
            <v-card>
              <v-card-title>
                <span class="headline">Adicionar Servidor</span>
              </v-card-title>
              <v-card-text>
                <v-form>
                  <v-text-field v-model="newServer.nome" label="Nome" required></v-text-field>
                  <v-text-field v-model="newServer.ip" label="IP" required></v-text-field>
                  <v-text-field v-model="newServer.local" label="Local" required></v-text-field>
                  <v-textarea v-model="newServer.description" label="Descrição"></v-textarea>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn @click="saveServer" color="primary">Salvar</v-btn>
                <v-btn @click="dialog = false" color="secondary">Cancelar</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-container>
      </template>
    </AuthenticatedLayout>
  </div>
</template>

<script setup>
import { ref, computed,onMounted } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const { props } = usePage();
const accessData = computed(() => props.accessData || { data: [], last_page: 1, current_page: 1 });

// Variáveis e estados
const dialog = ref(false);
const currentPage = ref(accessData.value.current_page);

const newServer = ref({
  nome: '',
  ip: '',
  local: '',
  description: ''
});

// Função para salvar o novo servidor localmente (apenas um exemplo)
const saveServer = () => {
  accessData.value.data.push({ ...newServer.value, id: Date.now(), icon: 'mdi-server', online: false });
  newServer.value = { nome: '', ip: '', local: '', description: '' };
  dialog.value = false;
};



// Função para redirecionar para o host do servidor
const redirectToHost = (server) => {
  let url;
  const isIpAddress = /^(?:\d{1,3}\.){3}\d{1,3}$/.test(server.ip);

  if (isIpAddress) {
    
    // Se for um endereço de IP, usa o IP e a porta
    url = `http://${server.ip}:${server.porta}`;
  } else {
    // Caso contrário, mantém o protocolo padrão e o hostname
    const protocol = server.ip.includes('https') ? 'https' : 'http';
    url = `${protocol}://${server.ip}`;
  }

  window.open(url, '_blank');
};


// Função para buscar dados de uma página específica ao clicar na paginação
const fetchServers = (page) => {
  router.visit(route('controladora.index', { page }), {
    method: 'get',
    replace: true,
    preserveState: true
  });
};


</script>


<style scoped>
.server-card {
  transition: transform 0.3s ease;
}
.server-card:hover {
  transform: perspective(1000px) rotateY(10deg) rotateX(10deg);
}
.v-container {
  background: rgb(182, 183, 183);
}
</style>
