<template>
    <v-app>
      <v-container class="no-padding no-margin" fluid>
        <v-row class="no-padding no-margin fill-height d-flex justify-center align-center" align="center">
          <v-col cols="12" class="preview-column no-padding no-margin">
            <v-card class="preview-card" :style="previewStyles">
              <div v-if="screenData" class="preview-background" :style="backgroundStyles">
                <form @submit.prevent="handleRegister">
                  <!-- Renderiza os elementos dinamicamente de acordo com caditens -->
                  <div v-if="caditens.nome" class="input-field left-aligned">
                    <v-text-field
                      v-model="formData.nome"
                      label="Nome"
                      placeholder="Insira seu nome"
                      outlined
                      dense
                      required
                      :error-messages="errors.nome"
                      :style="{ color: textColor }"
                    ></v-text-field>
                  </div>
  
                  <!-- Campo de Nome de Usuário -->
                  <div v-if="caditens.usuario" class="input-field left-aligned">
                    <v-text-field
                      v-model="formData.usuario"
                      label="Nome de Usuário"
                      placeholder="Insira seu nome de usuário"
                      outlined
                      dense
                      required
                      :error-messages="errors.usuario"
                      :style="{ color: textColor }"
                    ></v-text-field>
                  </div>
  
                  <!-- Campo de Email -->
                  <div v-if="caditens.email" class="input-field left-aligned">
                    <v-text-field
                      v-model="formData.email"
                      label="Email"
                      placeholder="Insira seu email"
                      outlined
                      dense
                      type="email"
                      required
                      :error-messages="errors.email"
                      :style="{ color: textColor }"
                    ></v-text-field>
                  </div>
  
                  <!-- Campo de Senha -->
                  <div v-if="caditens.senha" class="input-field left-aligned">
                    <v-text-field
                      v-model="formData.senha"
                      label="Senha"
                      placeholder="Insira sua senha"
                      outlined
                      dense
                      type="password"
                      required
                      :error-messages="errors.senha"
                      :style="{ color: textColor }"
                    ></v-text-field>
                  </div>
  
                  <!-- Campo de CPF -->
                  <div v-if="caditens.cpf" class="input-field left-aligned">
                    <v-text-field
                      v-model="formData.cpf"
                      label="CPF"
                      placeholder="Insira seu CPF"
                      outlined
                      dense
                      required
                      :error-messages="errors.cpf"
                      v-mask="'###.###.###-##'"
                      :style="{ color: textColor }"
                    ></v-text-field>
                  </div>
  
                  <!-- Campo de Telefone -->
                  <div v-if="caditens.telefone" class="input-field left-aligned">
                    <v-text-field
                      v-model="formData.telefone"
                      label="Telefone"
                      placeholder="Insira seu telefone"
                      outlined
                      dense
                      required
                      :error-messages="errors.telefone"
                      v-mask="'(##) #####-####'"
                      :style="{ color: textColor }"
                    ></v-text-field>
                  </div>
  
                  <!-- Campo de Data de Nascimento -->
                  <div v-if="caditens.nascimento" class="input-field left-aligned">
                    <v-text-field
                      v-model="formData.nascimento"
                      label="Data de Nascimento"
                      placeholder="Insira sua data de nascimento"
                      outlined
                      dense
                      required
                      v-mask="'##/##/####'"
                      :error-messages="errors.nascimento"
                      :style="{ color: textColor }"
                    ></v-text-field>
                  </div>
  
                  <!-- Campo de Sexo -->
                  <div v-if="caditens.sexo" class="input-field left-aligned">
                    <v-select
                      v-model="formData.sexo"
                      :items="['Masculino', 'Feminino', 'Não Informar']"
                      label="Sexo"
                      outlined
                      dense
                      required
                      :error-messages="errors.sexo"
                      :style="{ color: textColor }"
                    ></v-select>
                  </div>
  
                  <!-- Botão de Cadastro -->
                  <v-btn @click="handleRegister" color="primary" class="mt-4">
                    Cadastrar
                  </v-btn>
                                  <!-- Mensagem de erro geral -->
                    <div v-if="errors.message" class="error-message">
                      {{ errors.message }}
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
  import { usePage } from '@inertiajs/vue3';
  import { ref, reactive, computed } from 'vue';
  
  export default {
    setup() {
      const { props } = usePage();
      const customization = props.Customization;
      const regiao = ref(props.regiao);
      const errors = ref(props.errors || {}); 


  
      // Convertendo caditens de string JSON para objeto
      let caditens = JSON.parse(customization.caditens || '{}');
      caditens = Object.fromEntries(
        Object.entries(caditens).map(([key, value]) => [key, value === 'true'])
      );
  
      // Definindo a screenData
      const screenData = ref(customization || {});
  
      // Estilos do preview
      const previewStyles = reactive({
        width: '412px',
        height: '915px',
        border: 'none',
        position: 'relative',
        overflow: 'hidden',
        borderRadius: '0px',
      });

  
      // Estilos de fundo
      const backgroundStyles = reactive({
        backgroundImage:
          screenData.value?.background_type === 'Imagem' && screenData.value?.background_value
            ? `url(/storage/${screenData.value.background_image})`
            : 'none',
        backgroundColor:
          screenData.value?.background_type === 'Cor' && screenData.value?.background_value
            ? screenData.value?.background_value
            : 'transparent',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
        width: '100%',
        height: '100%',
        borderRadius: '0px',
      });
  
      // Calcula a cor oposta ao fundo
      const textColor = computed(() => {
        const bgColor = screenData.value?.background_value || '#ffffff';
        return getOppositeColor(bgColor);
      });
  
      // Função para calcular a cor oposta
      function getOppositeColor(hex) {
        // Remove o '#' do início se existir
        hex = hex.replace('#', '');
        // Converte para RGB
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        // Calcula a cor oposta
        const rOpp = 255 - r;
        const gOpp = 255 - g;
        const bOpp = 255 - b;
        // Retorna a cor oposta em formato hexadecimal
        return `rgb(${rOpp}, ${gOpp}, ${bOpp})`;
      }
  
      return { screenData, caditens, previewStyles, backgroundStyles, textColor, regiao };
    },
    data() {
      return {
        formData: {
          nome: '',
          usuario: '',
          email: '',
          senha: '',
          cpf: '',
          telefone: '',
          nascimento: '',
          sexo: '',
        }, 
        errors: {},     
      };
    },
   
    methods: {
  async handleRegister() {
    try {
      await this.$inertia.post(`/hotspot/${this.regiao}/register`, this.formData, {
        onError: (errors) => {
          console.error('Erro ao cadastrar:', errors);
          this.errors = errors; // Atualiza os erros para o frontend
        },
      });
    } catch (error) {
      console.error('Erro ao cadastrar:', error);
    }
  },
},

  };
  </script>
  
  <style scoped>
  
  .error-message {
  color: red;
  margin-top: 10px;
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
  .input-field.left-aligned {
  width: 300px;
  margin-bottom: 16px;
  display: flex;
  background-color: rgba(255, 255, 255, 0.297); /* Branco com 80% de opacidade */
  border-radius: 30px;
  justify-content: flex-start;
}

  </style>
  