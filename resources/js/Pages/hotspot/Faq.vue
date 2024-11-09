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
                <!-- Exibe o TopCard (logo) -->
              
  
                <!-- Conteúdo do FAQ -->
                <v-row class="faq-content">
                  <v-col cols="12">
                    <h2 class="faq-title">{{ faq?.nome || 'Perguntas Frequentes' }}</h2>
                    <div v-html="faq?.texto" class="faq-text"></div>
                  </v-col>
                </v-row>
  
                <!-- Botão "Voltar" -->
                <v-btn
                  class="voltar-button"
                  @click="handleBack"                
                  
                  dark
                >
                  Voltar
                </v-btn>
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
      const faq = ref(props.faq || {});       
      const customization = props.custom || {};
      console.log(customization);
      const elements = JSON.parse(customization.elements || '[]');
     
      const topCard = elements.find((element) => element.type === 'topCard');
      const isMobile = ref(window.innerWidth <= 768);
  
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
  
      const handleBack = () => {
        window.history.back(); // Volta para a página anterior
      };
  
      const updateIsMobile = () => {
        isMobile.value = window.innerWidth <= 768;
      };
  
      window.addEventListener('resize', updateIsMobile);
  
      onMounted(() => {
        updateIsMobile();
      });
  
      return {
        faq,
        topCard,
        isMobile,
        previewStyles,
        backgroundStyles,
        logoStyle,
        handleBack,
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
.faq-content {
  padding: 15px;
  overflow-y: auto;
  max-height: 80vh; /* Limita a altura para dispositivos móveis */
}
.faq-title {
  text-align: center;
  font-weight: bold;
  font-size: 1.2rem; /* Tamanho reduzido do título */
}
.faq-text {
  padding: 10px 0;
  font-size: 0.9rem; /* Tamanho reduzido do texto */
  line-height: 1.4; /* Melhor espaçamento entre linhas */
}
.faq-text p {
  margin: 5px 0; /* Reduz o espaçamento entre parágrafos */
}
.faq-text strong {
  font-weight: bold;
}
.faq-text ul,
.faq-text ol {
  padding-left: 20px;
  margin: 5px 0;
}
.faq-text li {
  font-size: 0.9rem;
  margin-bottom: 5px;
}
.voltar-button {
  position: absolute;
  bottom: 80px;
  right: 20px;
  background: rgba(0, 0, 0, 0.6);
  color: #fff;
  padding: 8px 10px;
  border-radius:20px;
  cursor: pointer;
}
</style>
