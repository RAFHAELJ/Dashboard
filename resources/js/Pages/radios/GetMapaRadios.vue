<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onMounted, ref, onBeforeUnmount, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MarkerClusterer from "@google/markerclustererplus";

const { props } = usePage();
const mapInitialized = ref(false);
const googleMapsApiKey = 'AIzaSyDdU4d2Zw4bcg9hPC0gB6VY62uHQvJzXz';
//'AIzaSyDdU4d2Zw4bcg9hPC0gB6VY62uHQvJzXzY'
const markersCache = ref([]);
const map = ref(null);
const updateInterval = 120 * 60 * 1000; // Atualizar a cada 120 minutos
let updateTimeout;

// Contadores de monitoramento
const scriptLoadCounter = ref(0);
const markerUpdateCounter = ref(0);

const loadGoogleMapsScript = (callback) => {
  if (typeof google !== 'undefined' && google.maps) {
    callback();
    return;
  }

  const script = document.createElement('script');
  script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsApiKey}&libraries=places`;
  script.async = true;
  script.defer = true;
  script.onload = () => {
    scriptLoadCounter.value++; // Incrementa o contador ao carregar o script
    console.log(`Script do Google Maps carregado ${scriptLoadCounter.value} vezes.`);
    callback();
  };
  document.head.appendChild(script);
};

const initMap = () => {
  nextTick(() => {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
      console.error("Elemento do mapa não está disponível.");
      return;
    }

    const mapCenter = props.data.center || { lat: -25.4284, lng: -49.2733 };
    map.value = new google.maps.Map(mapElement, {
      zoom: 14,
      center: mapCenter
    });

    loadMarkers();
    mapInitialized.value = true;
  });
};

const loadMarkers = () => {
  clearMarkers(); // Limpa marcadores antigos
  markerUpdateCounter.value++; // Incrementa o contador ao atualizar os marcadores
  console.log(`Marcadores atualizados ${markerUpdateCounter.value} vezes.`);

  const markers = props.data.data.map((radio) => {
    const [lat, lng] = radio.geo.split(',').map(Number);
    return new google.maps.Marker({
      position: { lat, lng },
      icon: radio.img,
      title: `Radio: ${radio.mac}`
    });
  });
  markersCache.value = markers; // Salva em cache

  if (map.value) {
    new MarkerClusterer(map.value, markersCache.value, {
      imagePath: "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m"
    });
  }
};

// Função para limpar marcadores antigos do mapa
const clearMarkers = () => {
  markersCache.value.forEach((marker) => marker.setMap(null));
  markersCache.value = [];
};

const scheduleMarkerUpdates = () => {
  updateTimeout = setInterval(() => {
    loadMarkers(); // Recarrega os marcadores a cada intervalo
  }, updateInterval);
};

onMounted(() => {
  loadGoogleMapsScript(() => {
    if (typeof google !== 'undefined' && google.maps) {
      initMap();
      scheduleMarkerUpdates();
    } else {
      console.error("Google Maps não foi carregado corretamente.");
    }
  });
});

onBeforeUnmount(() => {
  if (updateTimeout) {
    clearInterval(updateTimeout); // Limpa o intervalo de atualização
  }
  clearMarkers(); // Limpa os marcadores ao desmontar o componente
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

