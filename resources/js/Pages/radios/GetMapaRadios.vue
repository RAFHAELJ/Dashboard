<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onMounted, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import MarkerClusterer from "@google/markerclustererplus";

const { props } = usePage();
const mapInitialized = ref(false);
const googleMapsApiKey = 'AIzaSyDdU4d2Zw4bcg9hPC0gB6VY62uHQvJzXzY'; // Substitua pela sua chave de API

// Função para carregar o script do Google Maps
const loadGoogleMapsScript = (callback) => {
  if (typeof google !== 'undefined' && google.maps) {
    callback();
    return;
  }

  const script = document.createElement('script');
  script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsApiKey}&libraries=places`;
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

  const mapCenter = props.data.center || { lat: -25.4284, lng: -49.2733 };
  const map = new google.maps.Map(document.getElementById('map'), {
    zoom: 10,
    center: mapCenter
  });

  // Criação de marcadores e cluster de marcadores
  const markers = props.data.data.map((radio) => {
    const [lat, lng] = radio.geo.split(',').map(Number);
    return new google.maps.Marker({
      position: { lat, lng },
      icon: radio.img,
      title: `Radio: ${radio.mac}`
    });
  });

  new MarkerClusterer(map, markers, {
    imagePath: "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m"
  });

  mapInitialized.value = true;
};

// Carrega o script do Google Maps e inicializa o mapa ao montar o componente
onMounted(() => {
  loadGoogleMapsScript(() => {
    if (typeof google !== 'undefined' && google.maps) {
      initMap();
    } else {
      console.error("Google Maps não foi carregado corretamente.");
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
      <v-row class="mb-4 justify-center">
        <!-- Cards de Status -->
        <v-col cols="12" md="4" lg="2">
          <v-card class="bg-green text-center status-card mb-4">
            <v-card-title class="white--text">Acessados Hoje</v-card-title>
            <v-card-text class="d-flex flex-column align-center justify-center py-2">
              <v-icon color="white" size="36" class="mb-1">mdi-check-circle</v-icon>
              <span class="white--text display-2">{{ acessadoshj }}</span>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4" lg="2">
          <v-card class="bg-orange text-center status-card mb-4">
            <v-card-title class="white--text">Acessados Ontem</v-card-title>
            <v-card-text class="d-flex flex-column align-center justify-center py-2">
              <v-icon color="white" size="36" class="mb-1">mdi-help-circle</v-icon>
              <span class="white--text display-2">{{ acessadosontem }}</span>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4" lg="2">
          <v-card class="bg-red text-center status-card mb-4">
            <v-card-title class="white--text">Não Acessados Há 3 Dias</v-card-title>
            <v-card-text class="d-flex flex-column align-center justify-center py-2">
              <v-icon color="white" size="36" class="mb-1">mdi-cancel</v-icon>
              <span class="white--text display-2">{{ naoacessados }}</span>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Mapa -->
      <v-row>
        <v-col cols="12">
          <v-card class="pa-1">
            <v-card-title class="text-h5">Mapa dos Rádios</v-card-title>
            <v-card-text>
              <div id="map"></div> <!-- Mapa será renderizado aqui -->
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

.status-card {
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

.status-card:hover {
  transform: translateY(-4px);
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
  height: 500px;
  border-radius: 8px;
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

.py-2 {
  padding-top: 8px;
  padding-bottom: 8px;
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
  font-size: 1.5rem;
  font-weight: 600;
}

.text-h5 {
  font-size: 1.25rem;
  font-weight: 500;
}
</style>
