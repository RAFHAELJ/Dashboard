<template>
  <v-app>
    <v-container class="no-padding no-margin" fluid>
      <v-row class="no-padding no-margin fill-height d-flex justify-center align-center" align="center">
        <v-col cols="12" class="preview-column no-padding no-margin">
          <v-card class="preview-card" :style="previewStyles">
            <div v-if="screenData" class="preview-background" :style="backgroundStyles">
              <form>
                <!-- Elementos da tela renderizados dinamicamente -->
                <div v-for="(element, index) in screenData?.elements" :key="element.id" :style="elementStyle(element, index)" class="draggable">
                  <!-- Topo da página -->
                  <template v-if="element.type === 'topCard' && element.image">
                    <v-img :src="`/storage/${element.image}`" :max-height="element.height + 'px'" :max-width="element.width + 'px'"></v-img>
                  </template>

                  <!-- Botão -->
                  <v-btn v-if="element.type === 'button'" @click="handleLogin" :style="buttonStyle(element)">
                    {{ screenData?.login_button_textt || 'Continuar' }}
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
import { usePage } from '@inertiajs/vue3'; // Importar o usePage

export default {
  setup() {
    const { props } = usePage();
    let customization = props.login;
    let campanha = props.campanha;


    // Verificações e conversões semelhantes ao fetchData
    if (typeof customization.login_method === 'string') {
      customization.login_method = JSON.parse(customization.login_method);
    }
    if (typeof customization.elements === 'string') {
      customization.elements = JSON.parse(customization.elements);
    }
    if (typeof customization.login_password_method === 'string') {
      customization.login_password_method = JSON.parse(customization.login_password_method);
    }

    // Definindo a screenData com os valores convertidos
    const screenData = customization;

    console.log("Dados de login recebidos e processados:", screenData);

    return {
      screenData,
    };
  },
  data() {
    return {
      previewStyles: {
        width: '412px',
        height: '915px',
        border: 'none',
        position: 'relative',
        overflow: 'hidden',
        borderRadius: '0',
      },
      topCardHeight: 150,
      topCardWidth: 375,
    };
  },
  computed: {
    backgroundStyles() {
      return {
        backgroundImage: this.screenData?.background_type === 'Imagem' && this.screenData?.background_value
          ? `url(/storage/${this.screenData.background_image})`
          : 'none',
        backgroundColor: this.screenData?.background_type === 'Cor' && this.screenData?.background_value
          ? this.screenData?.background_value
          : 'transparent',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
        width: '100%',
        height: '100%',
        borderRadius: '0',
      };
    },
  },
  mounted() {
    // Captura a parte da URL para identificar a região
    const pathParts = window.location.pathname.split('/');
    const regionIndex = pathParts.indexOf('hotspot') + 1;
    this.region = pathParts[regionIndex] || 'default';
  },
  
  methods: {
async handleLogin() {

    let username = '';
    let password = '';

    // Itera pelos elementos para capturar os valores
    this.screenData.elements.forEach(element => {
      if (element.type === 'input') {
        username = element.value; // Captura o valor do input de login
      }
      if (element.type === 'inputPassword') {
        password = element.value; // Captura o valor do input de senha
      }
    });
    try {
      const response = await this.$inertia.post(`/hotspot/${this.region}/authenticate`, {
        username: username,
        password: password,
      });
      if (response.success) {
        window.location.href = response.url;
      } else {
        this.$notify({ type: 'error', text: response.error });
      }
    } catch (error) {
      console.error('Erro ao fazer login:', error);
    }
  },

  async handleCreateAccount() {
    // Redireciona para a página de cadastro
    this.$inertia.visit(`/hotspot/${this.region}/register`);
  },

  async handleHelpAccount() {
    // Implementa lógica para ajuda
    console.log('Ajuda acionada');
  },
    elementStyle(element, index) {
      return {
        top: element.top + 'px',
        left: element.left + 'px',
        position: 'absolute',
        width: element.width + 'px',
        height: element.height + 'px',
        zIndex: index + 1,
      };
    },
    buttonStyle(element) {
      return {
        width: '150 px',
        background: element.backgroundColor,
        borderRadius: element.shape + 'px',
        boxShadow: '0 ' + element.elevation + 'px ' + element.elevation + 'px rgba(0,0,0,0.2)',
        color: this.screenData?.text_color,
      };
    },
    linkStyle(element) {
      return {
        color: element.color || this.screenData?.textColorButton,
        fontSize: '14px',
        textDecoration: 'underline',
        cursor: 'pointer',
        top: element.top + 'px',
        left: element.left + 'px',
      };
    },
    textStyle(element) {
      return {
        color: element.color || this.screenData?.text_color,
        fontSize: '16px',
        fontWeight: 'bold',
        fontStyle: 'italic',
        top: element.top + 'px',
        left: element.left + 'px',
      };
    },
    inputStyle(element) {
      return {
        top: element.top + 'px',
        left: element.left + 'px',
        width: element.width + 'px',
        height: element.height + 'px',
        backgroundColor: element.backgroundColor,
        border: '1px solid #ccc',
        borderRadius: element.shape + 'px',
      };
    },
    inputInternalStyle(element) {
      return {
        width: '100%',
        height: '100%',
        backgroundColor: element.backgroundColor,
        borderRadius: element.shape + 'px',
        opacity: element.opacity,
        boxShadow: '0 ' + element.elevation + 'px ' + element.elevation + 'px rgba(0,0,0,0.2)',
        padding: '8px',
        outline: 'none',
      };
    },
  },
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
  max-width: 400px;
  min-height: 667px;
  background-color: #fff;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}
.preview-background {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #fff;
  border-radius: 12px;
}
.editable-text {
  font-size: 16px;
  color: #000;
  padding: 8px;
  border: none;
  background-color: transparent;
  min-height: 50px;
  min-width: 150px;
}
.input-field {
  width: 100%;
  margin-bottom: 16px;
}
input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 8px;
  outline: none;
  font-size: 14px;
}
</style>
