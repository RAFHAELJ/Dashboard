<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onMounted, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import MarkerClusterer from "@google/markerclustererplus";

const { props } = usePage();
const mapInitialized = ref(false);
const googleMapsApiKey = 'AIzaSyDdU4d2Zw4bcg9hPC0gB6VY62uHQvJzXz'; // Substitua pela sua chave de API
// 'AIzaSyDdU4d2Zw4bcg9hPC0gB6VY62uHQvJzXzY'

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
    zoom: 14,
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
      <!-- Cards de Status e Mapa -->
      <v-row>
        <v-col cols="12">
          <v-card class="pa-1">
            <v-row class="mb-2 justify-center">
              <v-col cols="12" md="2" lg="2">
                <v-card class="text-center status-card mb-2" elevation="2">
                  <v-card-title class="text-caption py-1">Acessados Hoje</v-card-title>
                  <v-card-text class="d-flex flex-column align-center justify-center py-1">
                    <v-icon color="#4caf50" size="21" class="mb-1">mdi-check-circle</v-icon>
                    <span class="text-h6">{{ acessadoshj }}</span>
                  </v-card-text>
                </v-card>
              </v-col>

              <v-col cols="12" md="3" lg="2">
                <v-card class="text-center status-card mb-2" elevation="2">
                  <v-card-title class="text-caption py-1">Acessados Ontem</v-card-title>
                  <v-card-text class="d-flex flex-column align-center justify-center py-1">
                    <v-icon color="#ff9800" size="21" class="mb-1">mdi-help-circle</v-icon>
                    <span class="text-h6">{{ acessadosontem }}</span>
                  </v-card-text>
                </v-card>
              </v-col>

              <v-col cols="12" md="3" lg="2">
                <v-card class="text-center status-card mb-2" elevation="2">
                  <v-card-title class="text-caption py-1">Não Acessados Há 3 Dias</v-card-title>
                  <v-card-text class="d-flex flex-column align-center justify-center py-1">
                    <v-icon color="#f44336" size="21" class="mb-1">mdi-cancel</v-icon>
                    <span class="text-h6">{{ naoacessados }}</span>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>

            <v-card-text>
              <div id="map" style="height: calc(100vh - 300px);"></div> <!-- Mapa ocupará toda a tela -->
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
}

.status-card {
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
  background-color: #ffffff; /* Fundo branco */
  min-height: 70px; /* Deixa o card mais retangular */
}

.status-card:hover {
  transform: translateY(-4px);
}

#map {
  width: 100%;
  height: 80%;
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

.mb-2 {
  margin-bottom: 8px;
}

.text-h6 {
  font-size: 1rem;
  font-weight: 500;
}

.text-caption {
  font-size: 0.875rem;
  font-weight: 400;
}
</style>

