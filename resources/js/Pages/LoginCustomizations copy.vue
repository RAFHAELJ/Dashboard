<template>
  <AuthenticatedLayout>
    <v-container fluid>
      <v-row>
        <!-- Coluna de Customização -->
        <v-col cols="12" md="6">
          <h1>Customizações de Login</h1>

          <!-- Escolha de Topo (Cor ou Imagem) -->
          <v-radio-group v-model="form.top_type" label="Escolher Topo" inline>
            <v-radio label="Cor" value="Cor"></v-radio>
            <v-radio label="Imagem" value="Imagem"></v-radio>
          </v-radio-group>

          <!-- Se for Cor, exibe o botão de escolha de cor -->
          <template v-if="form.top_type === 'Cor'">
            Selecione a cor:
            <v-btn
              class="my-2"
              :style="{ backgroundColor: form.top_value, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
              @click="showTopColorPicker = true"
            ></v-btn>

            <!-- Color Picker modal para escolher a cor -->
            <v-dialog v-model="showTopColorPicker" max-width="300px">
              <v-card>
                <v-color-picker
                  v-model="form.top_value"
                  hide-inputs
                  @change="closeTopColorPicker"
                ></v-color-picker>
              </v-card>
            </v-dialog>
          </template>
          <template v-else>
            <v-file-input
              label="Upload Imagem de Topo"
              accept="image/*"
              prepend-icon="mdi-upload"
              @change="handleTopImageUpload"
            ></v-file-input>
          </template>

          <!-- Escolha de Fundo (Cor ou Imagem) -->
          <v-radio-group v-model="form.background_type" label="Escolher Fundo" inline>
            <v-radio label="Cor" value="Cor"></v-radio>
            <v-radio label="Imagem" value="Imagem"></v-radio>
          </v-radio-group>

          <!-- Se for Cor, exibe o botão de escolha de cor -->
          <template v-if="form.background_type === 'Cor'">
            Selecione a cor:
            <v-btn
              class="my-2"
              :style="{ backgroundColor: form.background_value, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
              @click="showBackgroundColorPicker = true"
            ></v-btn>

            <!-- Color Picker modal para escolher a cor -->
            <v-dialog v-model="showBackgroundColorPicker" max-width="300px">
              <v-card>
                <v-color-picker
                  v-model="form.background_value"
                  hide-inputs
                  @change="closeBackgroundColorPicker"
                ></v-color-picker>
              </v-card>
            </v-dialog>
          </template>

          <template v-else>
            <v-file-input
              label="Upload Imagem de Fundo"
              accept="image/*"
              prepend-icon="mdi-upload"
              @change="handleBackgroundImageUpload"
            ></v-file-input>
          </template>

          <!-- Estilização do Botão de Login -->
          <v-text-field
            label="Texto do Botão de Login"
            v-model="form.login_button_text"
            :style="{ width: '200px' }"
          ></v-text-field>

          Selecione a cor do botão:
          <v-btn
            small
            :style="{ backgroundColor: form.login_button_color, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
            @click="showLoginButtonColorPicker = true"
          ></v-btn>

          <v-dialog v-model="showLoginButtonColorPicker" max-width="300px">
            <v-card>
              <v-color-picker
                v-model="form.login_button_color"
                hide-inputs
                @change="closeLoginButtonColorPicker"
              ></v-color-picker>
            </v-card>
          </v-dialog>

          <v-slider label="Formato (Arredondamento)" v-model="form.login_button_shape" min="0" max="50" thumb-label></v-slider>
          <v-slider label="Elevação do Botão" v-model="form.button_elevation" min="0" max="10" thumb-label></v-slider>
          <v-slider label="Transparência" v-model="form.button_opacity" min="0" max="1" step="0.1" thumb-label></v-slider>

          Selecione a cor do input:
            <v-btn
              class="my-2"
              :style="{ backgroundColor: form.input_color, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
              @click="showInputButtonColorPicker = true"
            ></v-btn>

            <!-- Color Picker modal para escolher a cor -->
            <v-dialog v-model="showInputButtonColorPicker" max-width="300px">
              <v-card>
                <v-color-picker
                  v-model="form.input_color"
                  hide-inputs
                  @change="closeInputButtonColorPicker"
                ></v-color-picker>
              </v-card>
            </v-dialog>
          <!-- Controles de Inputs -->
          <v-slider label="Largura do Input" v-model="form.input_width" min="100" max="400" thumb-label></v-slider>
          <v-slider label="Altura do Input" v-model="form.input_height" min="40" max="100" thumb-label></v-slider>        
         
          <v-slider label="Formato (Arredondamento) do Input" v-model="form.input_shape" min="0" max="50" thumb-label></v-slider>
          <v-slider label="Elevação do Input" v-model="form.input_elevation" min="0" max="10" thumb-label></v-slider>
          <v-slider label="Transparência do Input" v-model="form.input_opacity" min="0" max="1" step="0.1" thumb-label></v-slider>

          <!-- Botões para adicionar elementos no preview -->
          <v-btn color="primary" @click="addButton">Adicionar Botão de Login</v-btn>
          <v-btn color="primary" @click="addInput">Adicionar Inputs de Usuário e Senha</v-btn>

          <!-- Combo de Tipos de Login -->
          <v-select
            v-model="form.login_method"
            :items="['Email', 'Nome', 'Telefone']"
            label="Login Usando"
            multiple
            @change="handleLoginMethodChange"
          ></v-select>

          <!-- Combo de Tipos de Senha -->
          <v-select
            v-model="form.password_method"
            :items="['CPF', 'Data de Nascimento', 'Telefone', 'Livre']"
            label="Senha Usando"
            multiple
            @change="handlePasswordMethodChange"
          ></v-select>

          <!-- Checkbox de Redes Sociais -->
          <v-checkbox
            v-model="form.use_social_login"
            label="Usar Redes Sociais"
            @change="handleSocialLoginToggle"
          ></v-checkbox>

          <template v-if="form.use_social_login">
            <v-select
              v-model="form.selected_social_network"
              :items="['Facebook', 'Google', 'Twitter']"
              label="Escolher Rede Social"
              @change="handleSocialLogins"
            ></v-select>
          </template>

          <!-- Botão de Salvar -->
          <v-btn color="primary" @click="save">Salvar</v-btn>
        </v-col>

        <!-- Coluna de Preview Fixa -->
        <v-col cols="12" md="6" class="preview-sticky">
          <h2>Preview da Tela de Login</h2>
          <v-card :style="previewStyles">
            <div ref="topCard" class="resizable preview-top" :style="{ height: topCardHeight + 'px', width: topCardWidth + 'px', backgroundColor: form.top_type === 'Cor' ? form.top_value : null }">
              <template v-if="form.top_type === 'Imagem' && previewTopImage">
                <v-img :src="previewTopImage" :max-height="topCardHeight + 'px'" :max-width="topCardWidth + 'px'"></v-img>
              </template>
            </div>

            <div :style="backgroundStyles" class="preview-background">
              <div class="draggable" v-for="element in previewElements" :key="element.id" :style="{ top: element.top + 'px', left: element.left + 'px', position: 'absolute', opacity: element.opacity, width: element.width + 'px', height: element.height + 'px', backgroundColor: element.color, borderRadius: element.shape + 'px', boxShadow: '0 ' + element.elevation + 'px ' + element.elevation + 'px rgba(0,0,0,0.2)' }">
                <v-btn v-if="element.type === 'button'" :style="{ background: form.login_button_color, borderRadius: form.login_button_shape + 'px', boxShadow: '0 ' + form.button_elevation + 'px ' + form.button_elevation + 'px rgba(0,0,0,0.2)', opacity: form.button_opacity }">
                  {{ form.login_button_text }}
                </v-btn>
                <v-text-field
                  v-if="element.type === 'input'"
                  :label="element.label"
                  :style="{
                    width: element.width + 'px',
                    height: element.height + 'px',
                    backgroundColor: element.color,
                    borderRadius: element.shape + 'px',
                    boxShadow: '0 ' + element.elevation + 'px ' + element.elevation + 'px rgba(0,0,0,0.2)',
                    opacity: element.opacity
                  }"
                  solo
                  flat
                ></v-text-field>                <v-btn v-if="element.type === 'social'" :style="{ background: element.background_color, opacity: element.opacity }">{{ element.social }}</v-btn>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import interact from 'interactjs';

const form = ref({
  top_type: 'Cor',
  top_value: '#ffffff',
  background_type: 'Cor',
  background_value: '#ffffff',
  login_button_text: 'Login',
  login_button_color: '#1976d2',
  login_button_shape: 10,
  button_elevation: 2,
  button_opacity: 1,
  input_width: 300,
  input_height: 50,
  input_color: '#f0f0f0',
  input_shape: 5,
  input_elevation: 1,
  input_opacity: 1,
  social_logins: [],
  login_method: [],
  password_method: [],
  use_social_login: false,
  selected_social_network: null,
});

const showTopColorPicker = ref(false);
const showBackgroundColorPicker = ref(false);
const showLoginButtonColorPicker = ref(false);
const showInputButtonColorPicker = ref(false);

const previewElements = ref([]);

// Função para fechar os color pickers
const closeTopColorPicker = () => {
  showTopColorPicker.value = false;
};

const closeBackgroundColorPicker = () => {
  showBackgroundColorPicker.value = false;
};

const closeLoginButtonColorPicker = () => {
  showLoginButtonColorPicker.value = false;
};
const closeInputButtonColorPicker = () => {
  showInputButtonColorPicker.value = false;
};

// Funções para adicionar elementos no preview
const addButton = () => {
  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'button',
    top: 150,
    left: 50,
    opacity: form.value.button_opacity,
  });
};

const addInput = () => {
  form.value.login_method.forEach((method) => {
    previewElements.value.push({
      id: previewElements.value.length + 1,
      type: 'input',
      label: method,
      top: 200 + previewElements.value.length * 50,
      left: 50,
      width: form.value.input_width,
      height: form.value.input_height,
      color: form.value.input_color,
      shape: form.value.input_shape,
      opacity: form.value.input_opacity,
      elevation: form.value.input_elevation,
    });
  });

  form.value.password_method.forEach((method) => {
    previewElements.value.push({
      id: previewElements.value.length + 1,
      type: 'input',
      label: method,
      top: 200 + previewElements.value.length * 50,
      left: 50,
      width: form.value.input_width,
      height: form.value.input_height,
      color: form.value.input_color,
      shape: form.value.input_shape,
      opacity: form.value.input_opacity,
      elevation: form.value.input_elevation,
    });
  });
};

// Previsualizar imagem de topo
const previewTopImage = ref(null);
const handleTopImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewTopImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

// Previsualizar imagem de fundo
const backgroundImage = ref(null);
const handleBackgroundImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      backgroundImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

// Estilos de preview
const previewStyles = ref({
  width: '375px',
  height: '667px',
  border: '1px solid #ccc',
  position: 'relative',
  overflow: 'hidden',
  borderRadius: '12px',
});

const topCardHeight = ref(150);
const topCardWidth = ref(300);

const backgroundStyles = computed(() => {
  return form.value.background_type === 'Imagem' && backgroundImage.value
    ? { backgroundImage: `url(${backgroundImage.value})`, backgroundSize: 'cover', backgroundPosition: 'center' }
    : { backgroundColor: form.value.background_value };
});

onMounted(() => {
  interact('.draggable').draggable({
    listeners: {
      move(event) {
        const target = event.target;
        const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
        const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

        target.style.transform = `translate(${x}px, ${y}px)`;

        target.setAttribute('data-x', x);
        target.setAttribute('data-y', y);
      },
    },
  });

  interact('.resizable').resizable({
    edges: { left: true, right: true, bottom: true, top: true },
    listeners: {
      move(event) {
        const target = event.target;
        const { width, height } = event.rect;
        topCardWidth.value = width;
        topCardHeight.value = height;

        target.style.width = width + 'px';
        target.style.height = height + 'px';
      },
    },
  });
});
</script>

<style scoped>
.preview-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.preview-background {
  height: 100%;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.preview-top {
  border: 1px solid #ccc;
  position: relative;
}

.resizable {
  resize: both;
  overflow: auto;
}

.draggable {
  cursor: move;
}

.preview-sticky {
  position: sticky;
  top: 0;
}

.v-select {
  width: 200px;
}

.v-file-input {
  width: 300px;
}
</style>
