<template>
  <v-app>
    <v-container class="no-padding no-margin" fluid>
      <v-row class="no-padding no-margin fill-height d-flex justify-center align-center" align="center">
        <v-col cols="12" class="preview-column no-padding no-margin">
          <v-card class="preview-card" :style="previewStyles">
            <div v-if="screenData" class="preview-background" :style="backgroundStyles">
              <form @submit.prevent="handleLogin">
                <!-- Elementos da tela renderizados dinamicamente -->
                <div v-for="(element, index) in screenData?.elements" :key="element.id" :style="elementStyle(element, index)" class="draggable">
                  
                  <!-- Topo da página -->
                  <template v-if="element.type === 'topCard' && element.image">
                    <v-img :src="`/storage/${element.image}`" :max-height="element.height + 'px'" :max-width="element.width + 'px'"></v-img>
                  </template>

                  <!-- Botão -->
                  <v-btn v-if="element.type === 'button'" @click.prevent="handleLogin" :style="buttonStyle(element)">
                    {{ screenData?.login_button_text || 'Login' }}
                  </v-btn>

                  <!-- Links de texto -->
                  <a v-if="element.type === 'buttonC'" href="#" @click="handleCreateAccount" :style="linkStyle(element)">
                    Criar nova conta
                  </a>
                  <a v-if="element.type === 'buttonA'" href="#" @click="handleHelpAccount" :style="linkStyle(element)">
                    Ajuda
                  </a>

                  <!-- Texto editável -->
                  <div v-if="element.type === 'text'" contenteditable="true" class="editable-text" :style="textStyle(element)">
                    {{ element.text || 'Editável! Adicione seu texto aqui...' }}
                  </div>

                 <!-- Campo de Login -->
                      <div v-if="element.type === 'input'" class="input-field" :style="inputStyle(element)">
                        <input v-model="element.value"
                              :placeholder="screenData?.login_method?.join(' ou ') || 'Login'"
                              :style="inputInternalStyle(element)"
                               />
                      </div>

                      <!-- Campo de Senha -->
                      <div v-if="element.type === 'inputPassword'" class="input-field" :style="inputStyle(element)">
                        <input v-model="element.value"                              
                              :placeholder="screenData?.login_password_method?.join(' ou ') || 'Senha'"
                              :style="inputInternalStyle(element)"
                              v-mask="getMask(screenData?.login_password_method)" />
                      </div>
                </div>

                <!-- Campos ocultos para customization ID e regiao ID -->
                <input type="hidden" :value="screenData?.id" v-if="screenData?.id" />
                <input type="hidden" :value="regionId" v-if="regionId" />

              

              </form>
                            <!-- Diálogo de erro -->
                            <v-dialog v-model="errorDialog" max-width="400px">
                                <v-card>
                                  <v-card-title class="red--text">Erro de Login</v-card-title>
                                  <v-card-text>
                                    <div v-for="(error, key) in errors" :key="key" class="error-message">
                                      {{ error }}
                                    </div>
                                  </v-card-text>
                                  <v-card-actions>
                                    <v-btn color="red" @click="errorDialog = false">Fechar</v-btn>
                                  </v-card-actions>
                                </v-card>
                              </v-dialog>
            </div>
          </v-card>
        </v-col>
      </v-row> 
    </v-container>
  </v-app>
</template>

<script>
import { usePage } from '@inertiajs/vue3';
import { mask } from 'vue-the-mask';
export default {

  directives: {
    mask,
  },
  setup() {
    const { props } = usePage();
    let customization = props.login;

    if (typeof customization.login_method === 'string') {
      customization.login_method = JSON.parse(customization.login_method);
    }
    if (typeof customization.elements === 'string') {
      customization.elements = JSON.parse(customization.elements);
    }
    if (typeof customization.login_password_method === 'string') {
      customization.login_password_method = JSON.parse(customization.login_password_method);
    }

    const screenData = customization; 

    return {
      screenData,
    };
  },
  data() {
    return {
      emailTokens: {
        'X': { pattern: /[0-9a-zA-Z._%+-]/ }, // Alfanumérico + caracteres especiais válidos em email
        '@': { pattern: /[@]/ },
        '.': { pattern: /[.]/ }
      },
      
      previewStyles: {
        width: '412px',
        height: '915px',
        border: 'none',
        position: 'relative',
        overflow: 'hidden',
        borderRadius: '0px',
      },
      topCardHeight: 150,
      topCardWidth: 375,
      regionId: null,
      errorDialog: false,
      errors: {},
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
        borderRadius: '0px',
      };
    },
  },
  mounted() {
    const pathParts = window.location.pathname.split('/');
    const regionIndex = pathParts.indexOf('hotspot') + 1;
    this.region = pathParts[regionIndex] || 'default';

    // Definindo o ID da região a partir do `usePage` ou de outro local de configuração
   
  },
  
  methods: {
    getMask(methods) {
      console.log(methods);
    if (!methods || methods.length === 0) return ''; // Sem máscara se não houver métodos definidos

    // Define a máscara baseada nos métodos disponíveis
    if (methods.includes('Email')) {
      return 'X*@X.X*';
    }
    if (methods.includes('CPF')) {
      return '###.###.###-##'; // Máscara de CPF
    }
    if(methods.includes('Data de Nascimento')) {
      return '##/##/####'; // Máscara de data
    }
    if (methods.includes('Telefone')) {
      return '(##) #####-####'; // Máscara de telefone
    }
    if (methods.includes('nome')) {
      return ''; // Nome não requer máscara
    }

    return ''; // Retorna sem máscara como padrão
  },
  cleanValue(value) {
    // Remove todos os caracteres não numéricos
    return value.replace(/\D/g, '');
  },
    async handleLogin() {
      let username = '';
      let password = '';
      let customizationId = this.screenData?.id || null;
     

      this.screenData.elements.forEach(element => {
        if (element.type === 'input') {
          username = element.value;
        }
        if (element.type === 'inputPassword') {
          password = this.cleanValue(element.value);
        }
      });
      try {
        await this.$inertia.post(`/hotspot/${this.region}/wfd/authenticate`, {
          username: username,
          password: password,
          customization_id: customizationId,
        }, { onError: (errors) => {
         console.log('Erro ao fazer login:', errors);
                
                    this.errors = { message: errors.message }; // Captura a mensagem de erro corretamente
                    this.errorDialog = true;
                
            }
        });
      } catch (error) {
        console.error('Erro ao fazer login:', error);
        this.errorMessage = 'Erro ao tentar autenticar. Tente novamente.';
        this.errorDialog = true; 
      }
    },



    async handleCreateAccount() {
      const customizationId = this.screenData?.id;

      if (!customizationId) {
        console.error('ID de customização não encontrado.');
        return;
      }

      this.$inertia.visit(`/hotspot/${this.region}/new/${customizationId}`);
    },

    async handleHelpAccount() {
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
.error-dialog-card {
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.error-title {
  color: #e53935;
  font-weight: bold;
}

.error-text {
  color: #e53935;
  font-size: 16px;
  text-align: center;
}
.error-message {
  color: red;
  font-size: 12px;
  margin-top: 5px;
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
