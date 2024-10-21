<template>
    <v-app>
      <v-container class="no-padding no-margin" fluid>
        <v-row class="no-padding no-margin fill-height d-flex justify-center align-center" align="center">
          <v-col cols="12" class="preview-column no-padding no-margin">
            <v-card class="preview-card" :style="previewStyles">
              <div v-if="screenData" class="preview-background" :style="backgroundStyles">
                <form @submit.prevent="handleRegister">
                  <!-- Renderiza os elementos dinamicamente -->
                  <div v-for="(element, index) in screenData?.elements" :key="element.id" :style="elementStyle(element, index)" class="draggable">
                    <!-- Campo de Nome -->
                    <div v-if="element.type === 'input' && element.name === 'nome'" class="input-field">
                      <v-text-field
                        v-model="formData.nome"
                        :placeholder="element.placeholder || 'Insira seu nome'"
                        label="Nome"
                        outlined
                        dense
                        required
                      ></v-text-field>
                    </div>
  
                    <!-- Campo de Nome de Usuário -->
                    <div v-if="element.type === 'input' && element.name === 'usuario'" class="input-field">
                      <v-text-field
                        v-model="formData.usuario"
                        :placeholder="element.placeholder || 'Insira seu nome de usuário'"
                        label="Nome de Usuário"
                        outlined
                        dense
                        required
                      ></v-text-field>
                    </div>
  
                    <!-- Campo de Email -->
                    <div v-if="element.type === 'input' && element.name === 'email'" class="input-field">
                      <v-text-field
                        v-model="formData.email"
                        :placeholder="element.placeholder || 'Insira seu email'"
                        label="Email"
                        outlined
                        dense
                        type="email"
                        required
                      ></v-text-field>
                    </div>
  
                    <!-- Campo de Senha -->
                    <div v-if="element.type === 'inputPassword' && element.name === 'senha'" class="input-field">
                      <v-text-field
                        v-model="formData.senha"
                        :placeholder="element.placeholder || 'Insira sua senha'"
                        label="Senha"
                        outlined
                        dense
                        type="password"
                        required
                      ></v-text-field>
                    </div>
  
                    <!-- Campo de CPF -->
                    <div v-if="element.type === 'input' && element.name === 'cpf'" class="input-field">
                      <v-text-field
                        v-model="formData.cpf"
                        :placeholder="element.placeholder || 'Insira seu CPF'"
                        label="CPF"
                        outlined
                        dense
                        required
                        v-mask="'###.###.###-##'"
                      ></v-text-field>
                    </div>
  
                    <!-- Campo de Telefone -->
                    <div v-if="element.type === 'input' && element.name === 'telefone'" class="input-field">
                      <v-text-field
                        v-model="formData.telefone"
                        :placeholder="element.placeholder || 'Insira seu telefone'"
                        label="Telefone"
                        outlined
                        dense
                        required
                        v-mask="'(##) #####-####'"
                      ></v-text-field>
                    </div>
  
                    <!-- Campo de Data de Nascimento -->
                    <div v-if="element.type === 'input' && element.name === 'nascimento'" class="input-field">
                      <v-text-field
                        v-model="formData.nascimento"
                        :placeholder="element.placeholder || 'Insira sua data de nascimento'"
                        label="Data de Nascimento"
                        outlined
                        dense
                        required
                        v-mask="'##/##/####'"
                      ></v-text-field>
                    </div>
  
                    <!-- Campo de Sexo -->
                    <div v-if="element.type === 'select' && element.name === 'sexo'" class="input-field">
                      <v-select
                        v-model="formData.sexo"
                        :items="['Masculino', 'Feminino', 'Não Informar']"
                        label="Sexo"
                        outlined
                        dense
                        required
                      ></v-select>
                    </div>
  
                    <!-- Botão de Cadastro -->
                    <v-btn
                      v-if="element.type === 'button' && element.name === 'cadastrar'"
                      @click="handleRegister"
                      :style="buttonStyle(element)"
                    >
                      {{ element.text || 'Cadastrar' }}
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
  import { usePage } from '@inertiajs/vue3';
  
  export default {
    setup() {
      const { props } = usePage();
      let customization = props.login;
  
      // Convertendo os elementos recebidos do backend
      if (typeof customization.elements === 'string') {
        customization.elements = JSON.parse(customization.elements);
      }
  
      const screenData = customization;
      return { screenData };
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
      };
    },
    methods: {
      async handleRegister() {
        // Envia os dados para o backend
        try {
          const response = await this.$inertia.post(`/hotspot/${this.region}/register`, this.formData);
          if (response.success) {
            this.$notify({ type: 'success', text: 'Cadastro realizado com sucesso!' });
            window.location.href = response.url;
          } else {
            this.$notify({ type: 'error', text: response.error });
          }
        } catch (error) {
          console.error('Erro ao cadastrar:', error);
        }
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
          color: element.text_color || '#fff',
          borderRadius: element.shape + 'px',
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
  .input-field {
    width: 100%;
    margin-bottom: 16px;
  }
  </style>
  