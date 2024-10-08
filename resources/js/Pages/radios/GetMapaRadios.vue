<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onMounted, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

const { props } = usePage();
const mapInitialized = ref(false);
const googleMapsApiKey = 'AIzaSyDdU4d2Zw4bcg9hPC0gB6VY62uHQvJzXzY'; // Substitua pela sua chave de API

// Função para carregar o script do Google Maps
const loadGoogleMapsScript = (callback) => {
  if (typeof google !== 'undefined' && google.maps) {
    // Google Maps já carregado
    callback();
    return;
  }

  // Cria o script da API do Google Maps
  const script = document.createElement('script');
  script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsApiKey}`;
  script.async = true;
  script.defer = true;
  script.onload = callback;
  document.head.appendChild(script);
};

// Função para inicializar o mapa
const initMap = () => {
  if (!props || !props.data || !props.data.data) {
    console.error("Dados dos rádios não estão disponíveis.");
    return;
  }

  const map = new google.maps.Map(document.getElementById('map'), {
    zoom: 10,
    center: { lat: -23.55052, lng: -46.633308 } // Coordenadas de São Paulo como exemplo
  });

  props.data.data.forEach((radio) => {
    const [lat, lng] = radio.geo.split(',').map(Number);

    new google.maps.Marker({
      position: { lat, lng },
      map: map,
      icon: radio.img, // Use a URL da imagem para o ícone do marcador
      title: `Radio: ${radio.mac}` // Opcional: Exibe o MAC como título do marcador
    });
  });

  mapInitialized.value = true;
};

// Carrega o script do Google Maps e inicializa o mapa ao montar o componente
onMounted(() => {
  loadGoogleMapsScript(() => {
    if (typeof google !== 'undefined' && google.maps) {
      initMap();
    } else {
      console.error("Google Maps não está carregado.");
    }
  });
});

const acessadoshj = props.data?.acessadoshj || 0;
const acessadosontem = props.data?.acessadosontem || 0;
const naoacessados = props.data?.naoacessados || 0;
</script>


<template>
  <AuthenticatedLayout>
    <v-container class="bg-light-gray" fluid>
      <v-row class="mb-4">
        <v-col cols="12" md="8">
          <v-card class="pa-4 mb-4">
            <v-card-title class="text-h5">Mapa dos Rádios</v-card-title>
            <v-card-text>
              <div id="map"></div> <!-- Mapa será renderizado aqui -->
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card class="bg-green text-center mb-4">
            <v-card-title class="white--text">Acessados Hoje</v-card-title>
            <v-card-text class="d-flex flex-column align-center justify-center py-4">
              <v-icon color="white" class="rounded-circle mb-2">mdi-check-circle</v-icon>
              <span class="white--text display-2">{{ acessadoshj }}</span>
            </v-card-text>
          </v-card>

          <v-card class="bg-orange text-center mb-4">
            <v-card-title class="white--text">Acessados Ontem</v-card-title>
            <v-card-text class="d-flex flex-column align-center justify-center py-4">
              <v-icon color="white" class="rounded-circle mb-2">mdi-help-circle</v-icon>
              <span class="white--text display-2">{{ acessadosontem }}</span>
            </v-card-text>
          </v-card>

          <v-card class="bg-red text-center mb-4">
            <v-card-title class="white--text">Não Acessados Há 3 Dias</v-card-title>
            <v-card-text class="d-flex flex-column align-center justify-center py-4">
              <v-icon color="white" class="rounded-circle mb-2">mdi-cancel</v-icon>
              <span class="white--text display-2">{{ naoacessados }}</span>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AuthenticatedLayout>
</template>

<style scoped>
.bg-light-gray {
  background-color: #f5f5f5;
  min-height: 100vh;
}

.bg-green {
  background-color: #4caf50;
}

.bg-orange {
  background-color: #ff9800;
}

.bg-red {
  background-color: #f44336;
}

#map {
  width: 100%;
  height: 500px; /* Ajuste a altura conforme necessário */
  border-radius: 8px;
}

.rounded-circle {
  padding: 10px;
  border-radius: 50%;
}

.d-flex {
  display: flex;
}

.flex-column {
  flex-direction: column;
}

.align-center {
  align-items: center;
}

.justify-center {
  justify-content: center;
}

.py-4 {
  padding-top: 16px;
  padding-bottom: 16px;
}

.mb-4 {
  margin-bottom: 16px;
}

.white--text {
  color: #ffffff;
}

.text-center {
  text-align: center;
}

.display-2 {
  font-size: 2rem;
  font-weight: 600;
}

.text-h5 {
  font-size: 1.25rem;
  font-weight: 500;
}
</style>
