<template>
  <div>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
      <v-container  fluid fill-height>
         <!-- Botão para adicionar servidores -->
         <v-btn @click="dialog = true" color="primary" class="mb-4 d-flex align-left justify-left">
          Adicionar Servidor
        </v-btn>
        <!-- Cards de Servidores -->
        <v-row class="align-start">
          <v-col v-for="server in servers" :key="server.id" cols="12" sm="6" md="4">
            <v-card class="server-card" :elevation="server.online ? 12 : 2">
              <v-card-title>
                <v-icon>{{ server.icon }}</v-icon>
                <span class="ms-2">{{ server.name }}</span>
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

       

        <!-- Diálogo para adicionar servidor -->
        <v-dialog v-model="dialog" max-width="500px">
          <v-card>
            <v-card-title>
              <span class="headline">Adicionar Servidor</span>
            </v-card-title>
            <v-card-text>
              <v-form>
                <v-text-field v-model="newServer.name" label="Nome" required></v-text-field>
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
    </AuthenticatedLayout>
  </div>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

// Dados de exemplo para os servidores
const servers = ref([
  { id: 1, name: 'Servidor 1', ip: '192.168.1.1', local: 'Data Center 1', description: 'Servidor principal', icon: 'mdi-server', online: true },
  { id: 2, name: 'Servidor 2', ip: '192.168.1.2', local: 'Data Center 2', description: 'Servidor secundário', icon: 'mdi-server', online: false },
  // Adicione mais servidores conforme necessário
]);

const dialog = ref(false);
const newServer = ref({
  name: '',
  ip: '',
  local: '',
  description: ''
});

const saveServer = () => {
  // Aqui você pode adicionar lógica para enviar os dados para o servidor
  // Por exemplo, usando axios ou outra biblioteca de requisições HTTP
  servers.value.push({ ...newServer.value, id: Date.now(), icon: 'mdi-server', online: false });
  newServer.value = { name: '', ip: '', local: '', description: '' };
  dialog.value = false;
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
  background: rgb(182, 183, 183)
}

</style>
