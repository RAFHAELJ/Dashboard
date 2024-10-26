<template>
  <v-app>
    <v-container class="no-padding no-margin" fluid>
      <v-row class="no-padding no-margin fill-height d-flex justify-center align-center" align="center">
        <v-col cols="12" class="preview-column no-padding no-margin">
          <v-card class="preview-card" :style="previewStyles">
            <div v-if="exibe_propaganda" class="preview-background" :style="backgroundStyles">
              
              <!-- Imagem da Campanha -->
              <v-img
                v-if="campanha.tipo === 'imagem'"
                :src="`/storage/${campanha.imagem}`"
                class="ad-content"
                @click="handleAdClick"
              ></v-img>

              <!-- Vídeo da Campanha -->
              <div v-if="campanha.tipo === 'video'" class="video-wrapper">
                <v-img
                  v-if="!isVideoPlaying"
                  :src="`/storage/${campanha.capa}`"
                  class="ad-content"
                  @click="startVideo"
                ></v-img>
                <video
                  v-else
                  :src="`/storage/${campanha.video}`"
                  controls
                  autoplay
                  class="ad-content"
                ></video>
              </div>

              <!-- Google Forms -->
             <!-- Botão para abrir o Google Forms em uma nova aba -->
              <v-btn
                v-if="campanha.urlForms"
                class="open-form-button"
                @click="openFormInNewTab"
              >
                Abrir Formulário
              </v-btn>



              <!-- Relógio de contagem regressiva -->
              <div v-if="remainingTime > 0" class="timer">{{ remainingTime }}s</div>

              <!-- Botão "Continuar" -->
              <v-btn
                v-if="remainingTime === 0"
                class="continue-button"
                @click="handleContinue"
              >
                Continuar
              </v-btn>
            </div>

            <!-- Customização Original -->
            <div v-else class="preview-background" :style="backgroundStyles">
              <form>
                <!-- Elementos da tela renderizados dinamicamente -->
                <div
                  v-for="(element, index) in screenData?.elements"
                  :key="element.id"
                  :style="elementStyle(element, index)"
                  class="draggable"
                >
                  <!-- Topo da página -->
                  <template v-if="element.type === 'topCard' && element.image">
                    <v-img :src="`/storage/${element.image}`" :max-height="element.height + 'px'" :max-width="element.width + 'px'"></v-img>
                  </template>

                  <!-- Botão -->
                  <v-btn v-if="element.type === 'button'" @click="handleLogin" :style="buttonStyle(element)">
                    {{ 'Continuar' }}
                  </v-btn>
                </div>
              </form>
            </div>
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
    let customization = props.login;
    let campanha = props.campanha;
    let exibe_propaganda = true; //props.exibe_propaganda;

    // Verificações e conversões dos dados de customization
    if (typeof customization.elements === 'string') {
      customization.elements = JSON.parse(customization.elements);
    }
    const screenData = customization;

    // Estados e variáveis reativas
    const remainingTime = ref(campanha.duracao);
    const isVideoPlaying = ref(false);
    const previewStyles = {
      width: '90%',
      height: '90%',
      border: 'none',
      position: 'relative',
      overflow: 'hidden',
      borderRadius: '12px',
      boxShadow: '0 4px 10px rgba(0, 0, 0, 0.3)',
    };

    // Computed para backgroundStyles
    const backgroundStyles = computed(() => ({
      backgroundImage: screenData.background_type === 'Imagem' && screenData.background_value
        ? `url(/storage/${screenData.background_image})`
        : 'none',
      backgroundColor: screenData.background_type === 'Cor' && screenData.background_value
        ? screenData.background_value
        : 'transparent',
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
      width: '100%',
      height: '100%',
      borderRadius: '0',
    }));

    // Define a função elementStyle
    const elementStyle = (element, index) => ({
      top: element.top + 'px',
      left: element.left + 'px',
      position: 'absolute',
      width: element.width + 'px',
      height: element.height + 'px',
      zIndex: index + 1,
    });

    // Define a função buttonStyle
    const buttonStyle = (element) => ({
      width: element.width + 'px',
      backgroundColor: element.backgroundColor,
      borderRadius: element.shape + 'px',
      boxShadow: `0 ${element.elevation}px ${element.elevation}px rgba(0,0,0,0.2)`,
      color: screenData?.text_color,
    });

    const openFormInNewTab = () => {
  const formWindow = window.open(campanha.urlForms, '_blank');
  
  // Adiciona um intervalo para verificar se o formulário foi submetido
  const formInterval = setInterval(() => {
    try {
      // Verifica se a janela ainda está aberta
      if (formWindow.closed) {
        clearInterval(formInterval);
        handleContinue(); // Chama a função Continuar
      }
    } catch (e) {
      console.error('Erro ao acessar a janela do formulário:', e);
    }
  }, 1000);
};

    // Adiciona o método para lidar com o evento de carregamento do formulário
const handleFormLoad = (event) => {
  const iframe = event.target;
  const observer = new MutationObserver((mutationsList) => {
    for (const mutation of mutationsList) {
      // Verifica se o formulário foi submetido (mudança no DOM)
      if (mutation.type === 'childList' && iframe.contentDocument.body.innerText.includes('Obrigado')) {
        handleContinue(); // Chama a função Continuar
        observer.disconnect(); // Para de observar após a submissão
      }
    }
  });

  // Configura o observador para o body do iframe
  observer.observe(iframe.contentDocument.body, {
    childList: true,
    subtree: true,
  });
};


    // Inicia o cronômetro
    const startTimer = () => {
      const interval = setInterval(() => {
        if (remainingTime.value > 0) {
          remainingTime.value--;

          // Inicia o vídeo automaticamente se 1/4 do tempo não for clicado
          if (!isVideoPlaying.value && remainingTime.value === Math.floor(campanha.duracao / 4)) {
            startVideo();
          }
        } else {
          clearInterval(interval);
        }
      }, 1000);
    };

    // Inicia o vídeo
    const startVideo = () => {
      isVideoPlaying.value = true;
    };

    // Ação ao clicar na imagem da campanha
    const handleAdClick = () => {
      if (campanha.url) {
        window.location.href = campanha.url;
      }
    };

    // Ação ao clicar em "Continuar"
    const handleContinue = () => {
      console.log('Continuar');
      window.location.href = '/hotspot/campolargo/logon';
    };

    // Ação ao clicar no botão de login
    const handleLogin = () => {
      // Implementação da lógica de login
    };

    onMounted(() => {
      if (exibe_propaganda) {
        startTimer();
      }
    });

    return {
      campanha,
      screenData,
      exibe_propaganda,
      remainingTime,
      isVideoPlaying,
      previewStyles,
      backgroundStyles,
      elementStyle,
      buttonStyle,
      handleAdClick,
      handleContinue,
      handleLogin,
      startVideo,
    };
  },
};

const openFormInNewTab = () => {
  const formWindow = window.open(campanha.urlForms, '_blank');
  
  // Adiciona um intervalo para verificar se o formulário foi submetido
  const formInterval = setInterval(() => {
    try {
      // Verifica se a janela ainda está aberta
      if (formWindow.closed) {
        clearInterval(formInterval);
        handleContinue(); // Chama a função Continuar
      }
    } catch (e) {
      console.error('Erro ao acessar a janela do formulário:', e);
    }
  }, 1000);
};

</script>

<style scoped>
.no-padding {
  padding: 0 !important;
}
.no-margin {
  margin: 0 !important;
}
.fill-height {
  height: 100vh;
}
.preview-column {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0;
  margin: 0;
}
.preview-card {
  width: 100%;
  max-width:367px ;
  min-height: 730px;
  background-color: #fff;
  overflow: hidden;
  position: relative;
}
.preview-background {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}
.ad-content {
  width: 90%;
  height: 90%;
  object-fit: cover;
  cursor: pointer;
}
.google-form {
  width: 90%;
  height: 90%;
  border: none;
}
.video-wrapper {
  width: 90%;
  height: 90%;
  display: flex;
  justify-content: center;
  align-items: center;
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
iframe {
  touch-action: manipulation;
}
</style>
