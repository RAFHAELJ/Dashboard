<template>
    <v-form>
      <!-- Input para o nome do layout -->
      <v-text-field v-model="form.layout_name" label="Nome do Layout" :style="{ width: '300px' }" />
  
      <!-- Escolha de Topo (Cor ou Imagem) -->
      <v-radio-group v-model="form.top_type" label="Escolher imagem" inline>
        <v-radio label="Add imagem" value="Imagem"></v-radio>
      </v-radio-group>
  
      <template v-if="form.top_type === 'Imagem'">
        <v-file-input
          label="Upload Imagem de Topo"
          accept="image/*"
          prepend-icon="mdi-upload"
          @change="handleTopImageUpload"
        />
      </template>
  
      <!-- Escolha de Fundo (Cor ou Imagem) -->
      <v-radio-group v-model="form.background_type" label="Escolher Fundo" inline>
        <v-radio label="Cor" value="Cor"></v-radio>
        <v-radio label="Imagem" value="Imagem"></v-radio>
      </v-radio-group>
  
      <template v-if="form.background_type === 'Cor'">
        <v-btn
          class="my-2"
          :style="{ backgroundColor: form.background_value, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
          @click="showBackgroundColorPicker = true"
        />
        <v-dialog v-model="showBackgroundColorPicker" max-width="300px">
          <v-card>
            <v-color-picker v-model="form.background_value" hide-inputs @change="closeBackgroundColorPicker" />
          </v-card>
        </v-dialog>
      </template>
  
      <v-btn @click="saveLoginConfig" color="primary">
        Salvar Customização
      </v-btn>
    </v-form>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  import { mask } from 'vue-the-mask';
  
  const form = ref({
    layout_name: '',
    top_type: 'Cor',
    top_value: '#ffffff',
    background_type: 'Cor',
    background_value: '#ffffff',
    login_button_text: 'Login',
    login_button_color: '#1976d2',
    login_button_shape: 10,
    button_elevation: 2,
    button_opacity: 1,
    input_width: 150,
    input_height: 25,
    input_color: '#f0f0f0',
    input_shape: 5,
    input_elevation: 1,
    input_opacity: 1,
    types: {
      conta: false,
      rede_social: false,
      login_click: false,
    },
    login_method: [],
    password_method: [],
  });
  
  const saveLoginConfig = () => {
    const screenData = {
      layout_name: form.value.layout_name,
      top_type: form.value.top_type,
      top_value: form.value.top_value,
      background_type: form.value.background_type,
      background_value: form.value.background_value,
      login_button_text: form.value.login_button_text,
      login_button_color: form.value.login_button_color,
      login_method: form.value.login_method,
      login_password_method: form.value.password_method,
      input_color: form.value.input_color,
      region: 'campo largo',
    };
  
    axios
      .post('/login_customizations', screenData, {
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      })
      .then(() => {
        // Emita o evento para o componente pai
        emit('saved');
      })
      .catch((error) => {
        console.error('Erro ao salvar customização:', error);
      });
  };
  </script>
  