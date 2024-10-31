<template>
  <v-app>
    <v-container class="no-padding no-margin fill-height" fluid>
      <v-row class="fill-height d-flex justify-center align-center">
        <v-col
          :class="['d-flex justify-center align-center', isMobile ? 'full-screen' : 'fixed-size']"
          cols="12"
        >
          <v-card class="preview-card" :style="previewStyles">
            <v-sheet
              class="preview-background"
              :style="backgroundStyles"
            >
              <!-- Caso não haja campanha, exibir a tela padrão -->
              <template v-if="!campanha || !campanha.id">
                <!-- Exibir o TopCard (logo) na tela padrão -->
                <v-img
                  v-if="topCard"
                  :src="`/storage/${topCard.image}`"
                  :style="logoStyle"
                  class="logo-image"
                ></v-img>

                <!-- Botão "Continuar" na tela padrão -->
                <v-btn
                  class="continue-button"
                  @click="handleContinue"
                  color="success"
                  dark
                >
                  Continuar
                </v-btn>
              </template>

              <!-- Se houver campanha definida, exibir a tela da campanha -->
              <template v-else>
                <!-- Capa da Campanha para Vídeo ou Formulário -->
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

                <!-- Logo exibida junto ao botão do formulário, com posição customizada -->
                <v-img
                  v-if="showFormButton && topCard && campanha.tipo !== 'video'"
                  :src="`/storage/${topCard.image}`"
                  :style="logoStyle"
                  class="logo-image"
                ></v-img>

                <!-- Botão para abrir o Google Forms (Formulário) -->
                <v-row class="d-flex justify-center mt-3" v-if="showFormButton">
                  <v-col cols="12" class="d-flex justify-center">
                    <v-btn
                      v-if="campanha.tipo === 'formulario' && campanha.urlForms"
                      class="open-form-button"
                      @click="openFormInNewTab"
                      color="primary"
                      dark
                    >
                      Abrir Formulário
                    </v-btn>
                  </v-col>
                </v-row>

                <!-- Vídeo da Campanha -->
                <div
                  v-if="campanha.tipo === 'video' && isVideoPlaying"
                  class="video-container"
                >
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

                <!-- Imagem da Campanha -->
                <v-img
                  v-if="campanha.tipo === 'imagem'"
                  :src="`/storage/${campanha.imagem}`"
                  class="ad-content"
                  @click="handleAdClick"
                  cover
                ></v-img>

                <!-- Relógio de contagem regressiva -->
                <v-chip
                  v-if="remainingTime > 0"
                  class="timer"
                  color="red"
                  dark
                >
                  {{ remainingTime }}s
                </v-chip>

                <!-- Botão "Continuar" -->
                <v-btn
                  v-if="remainingTime === 0"
                  class="continue-button"
                  @click="handleContinue"
                  color="success"
                  dark
                >
                  Continuar
                </v-btn>
              </template>
            </v-sheet>
          </v-card>
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
    const campanha = props.campanha || null; // Pode ser null
    const customization = props.login || {};
    const elements = JSON.parse(customization.elements || '[]');
    const redirectUrl = ref(props.url || null);
   
    // Pegando o primeiro elemento do tipo "topCard"
    const topCard = elements.find((element) => element.type === 'topCard');
    
    const exibe_propaganda = true;
    const videoPlayer = ref(null);

    // Tempo total e tempo de exibição da capa
    const totalDuration = campanha?.duracao || 10;
    const capaDuration = Math.floor(totalDuration / 2);
    const remainingTime = ref(totalDuration);
    const isVideoPlaying = ref(false);
    const isMobile = ref(window.innerWidth <= 768);
    const showFormButton = ref(false);


    const previewStyles = computed(() => ({
      width: isMobile.value ? '98%' : '340px',
      height: isMobile.value ? '98%' : '680px',
      border: 'none',
      position: 'relative',
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

          // Mostrar o botão do formulário após o tempo de exibição da capa
          if (remainingTime.value === totalDuration - capaDuration) {
            showFormButton.value = true;
          }

          // Quando o tempo da capa terminar, inicia o vídeo (se houver)
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


    const openFormInNewTab = async () => {
  if (campanha?.urlForms) {
    // Executa a função handleContinue e espera 2 segundos antes de abrir a nova aba
    await handleContinue();

    setTimeout(() => {
      window.open(campanha.urlForms, '_blank');
    }, 2000);
  }
};

    const handleAdClick = () => {
      if (campanha?.url) {
        window.location.href = campanha.url;
      }
    };

    const handleContinue = () => {
      // Redireciona para a URL de resposta do login
      if (redirectUrl.value) {
        window.location.href = redirectUrl.value;
      } else {
        console.error('URL de redirecionamento não encontrada');
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
      openFormInNewTab,
      handleVideoEnd,
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
.no-padding {
  padding: 0 !important;
}
.no-margin {
  margin: 0 !important;
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
  border-radius: 5px;
}
.continue-button {
  position: absolute;
  bottom: 20px;
  right: 20px;
  background: rgba(0, 0, 0, 0.7);
  color: #fff;
  padding: 5px 10px;
  border-radius: 5px;
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
