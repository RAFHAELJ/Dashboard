<template>
  <v-app>
    <v-container class="no-padding no-margin fill-height" fluid>
      <v-row class="fill-height d-flex justify-center align-center">
        <v-col
          :class="['d-flex justify-center align-center', isMobile ? 'full-screen' : 'fixed-size']"
          cols="12"
        >
          <v-card class="preview-card" :style="previewStyles">
            <v-sheet class="preview-background" :style="backgroundStyles">
              <!-- Tela padrão quando não há campanha -->
              <template v-if="!campanha || !campanha.id">
                <v-img v-if="topCard" :src="`/storage/${topCard.image}`" :style="logoStyle" class="logo-image"></v-img>
                <v-btn class="continue-button" @click="handleContinue" dark>Continuar</v-btn>
              </template>

              <!-- Tela da campanha -->
              <template v-else>
                <!-- Capa da campanha -->
                <v-row class="d-flex justify-center">
                  <v-col cols="12" class="d-flex justify-center">
                    <v-img
                      v-if="(campanha.tipo === 'video' || campanha.tipo === 'formulario') && !isVideoPlaying && !showFormButton"
                      :src="`/storage/${campanha.capa}`"
                      class="video-cover"
                      @click="campanha.tipo === 'video' ? startVideo() : null"
                      cover
                    ></v-img>
                  </v-col>
                </v-row>

                <!-- Logo e botão do formulário -->
                <v-img
                  v-if="showFormButton && topCard && campanha.tipo !== 'video' && campanha.tipo !== 'imagem'"
                  :src="`/storage/${topCard.image}`"
                  :style="logoStyle"
                  class="logo-image"
                ></v-img>
                <v-row class="d-flex justify-center mt-3" v-if="showFormButton">
                  <v-col cols="12" class="d-flex justify-center">
                    <v-btn
                      v-if="campanha.tipo === 'formulario' && campanha.urlForms"
                      class="open-form-button"
                      @click="openFormInline"
                      color="primary"
                      dark
                    >
                      Abrir Formulário
                    </v-btn>
                  </v-col>
                </v-row>

                <!-- Conteúdo de vídeo e imagem -->
                <div v-if="campanha.tipo === 'video' && isVideoPlaying" class="video-container">
                  <div class="video-wrapper">
                    <video
                      ref="videoPlayer"
                      :src="`/storage/${campanha.video}`"
                      controls
                      autoplay
                      muted
                      class="video-content"
                      @ended="handleVideoEnd"
                    ></video>
                  </div>
                </div>
                <v-img
                  v-if="campanha.tipo === 'imagem'"
                  :src="`/storage/${campanha.imagem}`"
                  class="ad-content"
                  @click="handleAdClick"
                  cover
                  :style="{ width: '100%', height: '100%' }"
                ></v-img>

                <!-- Relógio de contagem regressiva -->
                <v-chip v-if="remainingTime > 0" class="timer" color="red" dark>{{ remainingTime }}s</v-chip>

                <!-- Botão de Continuar -->
                <v-btn v-if="remainingTime === 0 && campanha.tipo !== 'formulario'" class="continue-button" @click="handleContinue" dark>
                  Continuar
                </v-btn>
              </template>
            </v-sheet>
          </v-card>
          <!-- Diálogo de carregamento ao centro -->
          <v-dialog v-model="isLoading" max-width="300" persistent>
            <v-card class="d-flex flex-column align-center justify-center pa-4">
              <v-progress-circular indeterminate color="primary" class="mb-4"></v-progress-circular>
              <span>Carregando...</span>
            </v-card>
          </v-dialog>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default {
  setup() {
    const { props } = usePage();
    const campanha = props.campanha || null;
    const customization = props.login || {};
    const elements = JSON.parse(customization.elements || '[]');
    const redirectUrl = ref(props.url || null);
   
    const topCard = elements.find((element) => element.type === 'topCard');
    const exibe_propaganda = true;
    const videoPlayer = ref(null);

    const totalDuration = campanha?.duracao || 10;
    const capaDuration = Math.floor(totalDuration / 2);
    const remainingTime = ref(totalDuration);
    const isVideoPlaying = ref(false);
    const isMobile = ref(window.innerWidth <= 768);
    const showFormButton = ref(false);
    const isLoading = ref(false);

    const previewStyles = computed(() => ({
      width: isMobile.value ? '100%' : '340px',
      height: isMobile.value ? '100%' : '680px',
      border: 'none',
      position: 'absolute',
      overflow: 'hidden',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
    }));

    const backgroundStyles = computed(() => ({
      backgroundImage: customization.background_type === 'Imagem' && customization.background_value
        ? `url(/storage/${customization.background_image})`
        : 'none',
      backgroundColor: customization.background_type === 'Cor' && customization.background_value
        ? customization.background_value
        : 'blue',
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
    }));

    const logoStyle = computed(() => {
      if (!topCard) return {};
      return {
        position: 'absolute',
        top: `${topCard.top}px`,
        left: `${topCard.left}px`,
        width: `${topCard.width}px`,
        height: `${topCard.height}px`,
        zIndex: 10,
      };
    });

    const startTimer = () => {
      const interval = setInterval(() => {
        if (remainingTime.value > 0 && campanha?.id) {
          remainingTime.value--;

          if (remainingTime.value === totalDuration - capaDuration) {
            showFormButton.value = true;
          }

          if (remainingTime.value === capaDuration && campanha.tipo === 'video') {
            startVideo();
          }
        } else {
          clearInterval(interval);
        }
      }, 1000);
    };

    const startVideo = () => {
      isVideoPlaying.value = true;
      if (videoPlayer.value) {
        videoPlayer.value.currentTime = 0;
        videoPlayer.value.play();
      }
    };

    const handleVideoEnd = () => {
      isVideoPlaying.value = false;
      remainingTime.value = 0;
    };

    const openFormInline = async () => {
      if (campanha?.urlForms) {
        await handleContinue();
        window.location.href = campanha.urlForms;
      }
    };

    const handleAdClick = () => {
      if (campanha?.url) {
        window.location.href = campanha.url;
      }
    };

    const handleContinue = async () => {
  isLoading.value = true;
  
  if (redirectUrl.value) {
    try {
      // Envia a solicitação de autenticação em segundo plano
      const response = await fetch(redirectUrl.value, {
        method: 'POST', // Use o método adequado para autenticação, por exemplo, POST
        headers: {
          'Content-Type': 'application/json', // Ajuste os headers conforme necessário
          // Inclua mais headers conforme a autenticação requer
        },
        // Inclua aqui os dados de autenticação se necessário
        body: JSON.stringify({ /* dados de autenticação */ })
      });
      
      if (!response.ok) {
        console.error('Falha na autenticação', response.statusText);
      }
      
      // Processa a resposta conforme necessário
      const data = await response.json();
      console.log('Autenticação realizada:', data);

    } catch (error) {
      console.error('Erro durante a autenticação:', error);
    } finally {
      isLoading.value = false;
    }
  } else {
    console.error('URL de redirecionamento não encontrada');
    isLoading.value = false;
  }
};


    const updateIsMobile = () => {
      isMobile.value = window.innerWidth <= 768;
    };

    window.addEventListener('resize', updateIsMobile);

    onMounted(() => {
      if (exibe_propaganda && campanha?.id) {
        startTimer();
      }
      updateIsMobile();
    });

    return {
      campanha,
      topCard,
      exibe_propaganda,
      remainingTime,
      isVideoPlaying,
      isMobile,
      showFormButton,
      videoPlayer,
      previewStyles,
      backgroundStyles,
      logoStyle,
      handleAdClick,
      handleContinue,
      redirectUrl,
      openFormInline,
      handleVideoEnd,
      isLoading,
    };
  },
};
</script>

<style scoped>
.logo-image {
  position: absolute;
  max-width: 100%;
  height: auto;
  z-index: 10;
}
.fill-height {
  height: 100vh;
}
.fixed-size {
  width: 360px;
  height: 740px;
}
.full-screen {
  width: 100%;
  height: 100%;
}
.preview-card {
  background-color: #fff;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}
.preview-background {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}
.ad-content,
.video-cover,
.video-content {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.video-container,
.video-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: black;
}
.timer {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.7);
  color: #fff;
  padding: 5px 10px;
  border-radius: 20px;
}
.continue-button {
  position: absolute;
  bottom: 80px;
  right: 20px;
  background: rgba(0, 0, 0, 0.6);
  color: #fff;
  padding: 8px 10px;
  border-radius: 20px;
  cursor: pointer;
}
.open-form-button {
  margin-top: 10px;
  margin-left: 10px;
  margin-right: auto;
  background: rgba(0, 0, 0, 0.7);
  color: #fff;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
}
</style>
