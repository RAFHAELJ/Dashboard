<template>
  <AuthenticatedLayout>
    <v-container fluid>
      <v-row>
        <!-- Coluna de Customização -->
        <v-col cols="12" md="6">
          <h1>Customizações de Login</h1>

          

          <!-- Input para o nome do layout -->
          <v-text-field
            v-model="form.layout_name"
            label="Nome do Layout"
            :style="{ width: '300px' }"
          ></v-text-field>

          <!-- Escolha de Topo (Cor ou Imagem) -->
          <v-radio-group v-model="form.top_type" label="Escolher Topo" inline>
            <v-radio label="Cor" value="Cor"></v-radio>
            <v-radio label="Imagem" value="Imagem"></v-radio>
          </v-radio-group>

          <template v-if="form.top_type === 'Cor'">
            Selecione a cor:
            <v-btn
              class="my-2"
              :style="{ backgroundColor: form.top_value, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
              @click="showTopColorPicker = true"
            ></v-btn>
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

          <template v-if="form.background_type === 'Cor'">
            Selecione a cor:
            <v-btn
              class="my-2"
              :style="{ backgroundColor: form.background_value, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
              @click="showBackgroundColorPicker = true"
            ></v-btn>
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

          <!-- Tipos de Login com Checkboxes -->
          <v-checkbox
            v-model="form.types.conta"
            label="Conta"
            @change="handleToggleConta"
          ></v-checkbox>

          <template v-if="form.types.conta">
            <v-select
              v-model="form.login_method"
              :items="['Email', 'Nome', 'Telefone']"
              label="Login Usando"
              multiple
              @change="handleLoginMethodChange"
            ></v-select>
            <v-select
              v-model="form.password_method"
              :items="['CPF', 'Data de Nascimento', 'Telefone', 'Livre']"
              label="Senha Usando"
              multiple
              @change="handlePasswordMethodChange"
            ></v-select>
          </template>

          <v-checkbox
            v-model="form.types.rede_social"
            label="Rede Social"
            @change="handleToggleRedeSocial"
          ></v-checkbox>

          <template v-if="form.types.rede_social">
            <v-select
              v-model="form.selected_social_networks"
              :items="['Facebook', 'Google', 'Twitter']"
              label="Selecione Redes Sociais"
              multiple
            ></v-select>
          </template>

          <v-checkbox
            v-model="form.types.login_click"
            label="Login com um click"
            @change="handleToggleLoginClick"
          ></v-checkbox>

          <!-- Estilização do Botão de Login -->
          <v-text-field
            label="Texto do Botão de Login"
            v-model="form.login_button_text"
            :style="{ width: '200px' }"
          ></v-text-field>

          <!-- Selecione a cor do botão de login -->
           Cor botao:
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

          <!-- Selecione a cor do botão do input -->
           Cor input:
          <v-btn
            small
            :style="{ backgroundColor: form.input_color, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
            @click="showInputColorPicker = true"
          ></v-btn>

          <v-dialog v-model="showInputColorPicker" max-width="300px">
            <v-card>
              <v-color-picker
                v-model="form.input_color"
                hide-inputs
                @change="closeInputColorPicker"
              ></v-color-picker>
            </v-card>
          </v-dialog>

          <!-- Selecione a cor do botão do texto -->
           Cor texto:
          <v-btn
            small
            :style="{ backgroundColor: form.text_color, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
            @click="showTextColorPicker = true"
          ></v-btn>

          <v-dialog v-model="showTextColorPicker" max-width="300px">
            <v-card>
              <v-color-picker
                v-model="form.text_color"
                hide-inputs
                @change="closeTextColorPicker"
              ></v-color-picker>
            </v-card>
          </v-dialog>

          <!-- Sliders para controle do botão -->
          <v-slider label="Formato (Arredondamento)" v-model="form.login_button_shape" min="0" max="50" thumb-label></v-slider>
          <v-slider label="Elevação do Botão" v-model="form.button_elevation" min="0" max="10" thumb-label></v-slider>          

          <!-- Controles de Inputs -->
          <v-slider label="Largura do Input" v-model="form.input_width" min="100" max="400" thumb-label></v-slider>
          <v-slider label="Altura do Input" v-model="form.input_height" min="25" max="100" thumb-label></v-slider>
          <v-slider label="Formato (Arredondamento)" v-model="form.input_shape" min="0" max="50" thumb-label></v-slider>
          <v-slider label="Elevação do Input" v-model="form.input_elevation" min="0" max="10" thumb-label></v-slider>

          <!-- Controles para editar o texto -->
              <v-slider
                v-model="fontSize"
                label="Tamanho da Fonte"
                min="12"
                max="40"
                thumb-label
              ></v-slider>

                        <!-- Sliders para ajustar o tamanho da caixa de texto -->
          <v-slider
            v-model="textBoxWidth"
            label="Largura da Caixa de Texto"
            min="50"
            max="400"
            thumb-label
          ></v-slider>

          <v-slider
            v-model="textBoxHeight"
            label="Altura da Caixa de Texto"
            min="50"
            max="200"
            thumb-label
          ></v-slider>
              Cor do Texto
              <v-btn small :style="{ backgroundColor: form.text_color, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }" @click="showTextColorPicker = true">
               
              </v-btn>

              <v-dialog v-model="showTextColorPicker" max-width="300px">
                <v-card>
                  <v-color-picker v-model="form.textColor" hide-inputs @change="closeTextColorPicker"></v-color-picker>
                </v-card>
              </v-dialog>

              <!-- Controles para Negrito e Itálico -->
              <v-checkbox v-model="isBold" label="Negrito"></v-checkbox>
              <v-checkbox v-model="isItalic" label="Itálico"></v-checkbox>

          

        </v-col>

        <!-- Coluna de Preview Fixa -->
        <v-col cols="12" md="6" class="preview-sticky">
          <h2>Preview da Tela de Login</h2>
          <v-card :style="previewStyles">
           

            <div :style="backgroundStyles" class="preview-background ">

              <div 
                  ref="topCard" 
                  class="resizable draggable preview-top" 
                  :style="{ 
                    height: topCardHeight + 'px', 
                    width: topCardWidth + 'px', 
                    top: topCardTop + 'px',       
                    left: topCardLeft + 'px',     
                    position: 'absolute',          
                    backgroundColor: form.top_type === 'Cor' ? form.top_value : null,
                    border: 'none'  // Removendo as bordas
                  }"
                >
                  <template v-if="form.top_type === 'Imagem' && previewTopImage">
                    <v-img :src="previewTopImage" :max-height="topCardHeight + 'px'" :max-width="topCardWidth + 'px'"></v-img>
                  </template>
                </div>


                <div
                    class="draggable"
                    v-for="element in previewElements"
                    :key="element.id"
                    :data-id="element.id"  
                    :style="{
                      top: element.top + 'px',
                      left: element.left + 'px',
                      position: 'absolute',
                      width: element.width ? element.width + 'px' : form.input_width + 'px',
                      height: element.height ? element.height + 'px' : form.input_height + 'px',
                    }"
                  >


                <v-btn v-if="element.type === 'button'" :style="{ background: form.login_button_color, borderRadius: form.login_button_shape + 'px', boxShadow: '0 ' + form.button_elevation + 'px ' + form.button_elevation + 'px rgba(0,0,0,0.2)', opacity: form.button_opacity,color: form.text_color }">
                  {{ form.login_button_text }}
                </v-btn>
                        <a
                        v-if="element.type === 'buttonC'"
                        href="#"
                        @click="handleCreateAccount"
                        :style="{ color: form.text_color, fontSize: '14px', textDecoration: 'underline', cursor: 'pointer' }"
                      >
                        Criar nova conta
                      </a>

                      <a
                        v-if="element.type === 'buttonA'"
                        href="#"
                        @click="handleHelpAccount"
                        :style="{ color: form.text_color, fontSize: '14px', textDecoration: 'underline', cursor: 'pointer' }"
                      >
                        Ajuda
                      </a>

                        <!-- Adicionando um editor de texto dentro do draggable -->
                        
                        <div
                            v-if="element.type === 'text'"
                            contenteditable="true"
                            ref="editableText"
                            class="editable-text"
                            :style="{
                              color: form.textColor,
                              fontSize: fontSize + 'px',
                              fontWeight: fontWeight,
                              fontStyle: fontStyle
                            }"
                            @input="updateEditableText($event, element.id)"
                          >
                            {{ form.editableTextContent }}
                          </div>



                              <!-- Campo de login com máscara dinâmica -->
                    <template v-if="element && form.types.conta">
                    <div style="display: flex; flex-direction: column; gap: 16px;"> <!-- Flex container with gap for spacing -->
                      <!-- Campo de login -->
                      <div>
                        <label v-if="element.label === 'username'" :for="element.label">Login: </label>
                        <input
                          v-if="element.type === 'input' && element.label"
                          :id="element.label"
                          :placeholder="form.login_method.join(' ou ')"
                          :style="{
                            width: form.input_width + 'px',
                            height: form.input_height + 'px',
                            backgroundColor: form.input_color,
                            borderRadius: form.input_shape + 'px',
                            boxShadow: '0 ' + form.input_elevation + 'px ' + form.input_elevation + 'px rgba(0,0,0,0.2)',
                            color: form.text_color,
                            opacity: form.input_opacity,
                            border: '1px solid #ccc',
                            padding: '8px',
                            outline: 'none'
                          }"
                          v-model="form.input_value"
                          v-mask="getMaskForMethod"
                        />
                      </div>

                      <!-- Campo de senha -->
                      <div>
                        <label v-if="element.label === 'password'" for="password">Senha: </label>
                        <input
                          v-if="element.type === 'inputPassword' && element.label"
                          id="password"
                          :placeholder="form.password_method.join(' ou ')" 
                          :style="{
                            width: form.input_width + 'px',
                            height: form.input_height + 'px',
                            backgroundColor: form.input_color,
                            borderRadius: form.input_shape + 'px',
                            boxShadow: '0 ' + form.input_elevation + 'px ' + form.input_elevation + 'px rgba(0,0,0,0.2)',
                            color: form.text_color,
                            opacity: form.input_opacity,
                            border: '1px solid #ccc',
                            padding: '8px',
                            outline: 'none'
                          }"
                          type="password"
                          v-model="form.password_value"
                          v-mask="getMaskForMethod"
                        />
                      </div>
                    </div>
                  </template>


                            <v-btn v-if="element.type === 'social'" :style="{ background: form.login_button_color, opacity: form.button_opacity }">
                              {{ element.social }}
                            </v-btn>
                          </div>
                        </div>
                      </v-card>
                      <v-btn @click="saveScreenAsJson" color="primary">Salvar como JSON</v-btn>
                    
                    </v-col>
                  </v-row>
                </v-container>
              </AuthenticatedLayout>
            </template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import interact from 'interactjs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { mask } from 'vue-the-mask'; 
import { color } from 'zrender';

const directives = {
  mask,  // Registrando a diretiva v-mask
};

const handleCreateAccount = () => {
  console.log("Botão 'Criar nova conta' foi clicado.");
  // Aqui você pode adicionar lógica para a criação de uma nova conta
};

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
  username_mask: '',
  password_field: '',
  selected_social_networks: [],
  login_method: [], 
  password_method: [],
  editableTextContent: 'Digite Aqui seu texto',
  
});
const topCardTop = ref(0);    // Posição inicial top
const topCardLeft = ref(0);   // Posição inicial left
const fontSize = ref(16);
const textColor = ref('#000000');
const isBold = ref(false);
const isItalic = ref(false);
const fontWeight = computed(() => isBold.value ? 'bold' : 'normal');
const fontStyle = computed(() => isItalic.value ? 'italic' : 'normal');
const showTopColorPicker = ref(false);
const showBackgroundColorPicker = ref(false);
const showLoginButtonColorPicker = ref(false);
const showInputColorPicker = ref(false);
const showTextColorPicker = ref(false);
const previewTopImage = ref(null);
const backgroundImage = ref(null);
const textBoxWidth = ref(200); // Largura inicial da caixa de texto
const textBoxHeight = ref(100); // Altura inicial da caixa de texto



const updateEditableText = (event, elementId) => {
  const element = previewElements.value.find(el => el.id === elementId);
  if (element) {
    element.text = event.target.innerText;
  }
};

const handleLoginMethodChange = () => {
  
  // Remover elementos de login que não estão mais selecionados
  previewElements.value = previewElements.value.filter(el => !(el.type === 'input' && !form.value.login_method.includes(el.label)));

  // Adicionar novos elementos de login
  form.value.login_method.forEach(method => {
    const existingElement = previewElements.value.find(el => el.label === method);
    if (!existingElement) {
      previewElements.value.push({
        id: previewElements.value.length + 1,
        type: 'input',
        label: method,
        top: 100 + previewElements.value.length * 50,
        left: 50,
        width: form.value.input_width,
        height: form.value.input_height,
        color: form.value.input_color,
        shape: form.value.input_shape,
        opacity: form.value.input_opacity,
      });
    }
  });
};



const handlePasswordMethodChange = () => {
 
  // Remover elementos de senha que não estão mais selecionados
  previewElements.value = previewElements.value.filter(el => !(el.type === 'input' && !form.value.password_method.includes(el.label)));

  // Adicionar novos elementos de senha
  form.value.password_method.forEach(method => {
    const existingElement = previewElements.value.find(el => el.label === method );
    if (!existingElement) {
      previewElements.value.push({
        id: previewElements.value.length + 1,
        type: 'input',
        label: method,
        top: previewElements.value.length * 50,
        left: 50,
        width: form.value.input_width,
        height: form.value.input_height,
        color: form.value.input_color,
        shape: form.value.input_shape,
        opacity: form.value.input_opacity,
      });
    }
  });
};


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

const handleToggleConta = () => {
  if (form.value.types.conta) {
    addContaInputs();
  } else {
    removePreviewElement('input', 'username');
    removePreviewElement('inputPassword', 'password');
    removePreviewElement('button');
    removePreviewElement('buttonC');
    removePreviewElement('buttonA');
    removePreviewElement('text');
  }
};

const handleToggleRedeSocial = () => {
  if (form.value.types.rede_social) {
    form.value.selected_social_networks.forEach(network => {
      previewElements.value.push({
        id: previewElements.value.length + 1,
        type: 'social',
        social: network,
        top: 200 + previewElements.value.length * 50,
        left: 50,
      });
    });
  } else {
    removePreviewElement('social');
  }
};

const handleToggleLoginClick = () => {
  if (form.value.types.login_click) {
    previewElements.value.push({
      id: previewElements.value.length + 1,
      type: 'button',
      top: 250,
      left: 50,
      opacity: form.value.button_opacity,
    });
  } else {
    removePreviewElement('button');
  }
};

const previewElements = ref([]);

const addContaInputs = () => {
  const baseTop = 100;
  const baseLeft = 50;
  const spacing = 60; // Espaçamento entre os elementos

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'input',
    label: 'username',
    top: baseTop,
    left: baseLeft,
    backgroundColor: form.value.input_color,
    width: form.value.input_width,
    height: form.value.input_height,
    color: form.value.input_color,
    shape: form.value.input_shape,
    opacity: form.value.input_opacity,
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'inputPassword',
    label: 'password',
    top: baseTop + spacing, // Distanciando do input anterior
    left: baseLeft,
    backgroundColor: form.value.input_color,
    width: form.value.input_width,
    height: form.value.input_height,
    color: form.value.input_color,
    shape: form.value.input_shape,
    opacity: form.value.input_opacity,
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'button',
    top: baseTop + spacing * 2, // Aumentando o espaçamento para o botão
    left: baseLeft,
    opacity: form.value.button_opacity,
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'buttonC',
    width: 200,
    top: baseTop + spacing * 3, // Outro espaçamento
    left: baseLeft + 40, // Ajustando a posição horizontal do botão
    opacity: form.value.button_opacity,
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'buttonA',
    width: 100,
    top: baseTop + spacing * 2.5, // Colocando entre os dois botões
    left: baseLeft - 30, // Ajustando a posição horizontal
    opacity: form.value.button_opacity,
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'text',
    top: baseTop + spacing * 4, // Espaçamento maior para o texto
    left: baseLeft,
    width: 300,
    color: form.value.textColor,
    height: 100,
    text: 'Texto editável',
  });
};


const removePreviewElement = (type, label = null) => {
  previewElements.value = previewElements.value.filter(
    element => element.type !== type || (label && element.label !== label)
  );
};

const previewStyles = ref({
  width: '375px',
  height: '667px',
  border: '1px solid #ccc',
  position: 'relative',
  overflow: 'hidden',
  borderRadius: '12px',
});

const topCardHeight = ref(150);
const topCardWidth = ref(400);

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
      end(event) {
        const target = event.target;

        // Pegue o ID do elemento
        const elementId = parseInt(target.getAttribute('data-id'), 10);
        const element = previewElements.value.find(el => el.id === elementId);

        if (element) {
          // Recupere as posições finais
          const deltaX = parseFloat(target.getAttribute('data-x')) || 0;
          const deltaY = parseFloat(target.getAttribute('data-y')) || 0;
          const initialLeft = parseFloat(target.style.left) || 0;
          const initialTop = parseFloat(target.style.top) || 0;

          // Calcule as novas posições
          const newLeft = initialLeft + deltaX;
          const newTop = initialTop + deltaY;

          // Atualize o estado do Vue com as novas posições
          element.left = newLeft;
          element.top = newTop;

          // Resetar os valores temporários para evitar acúmulo de valores de transformação
          target.style.transform = 'none';
          target.setAttribute('data-x', 0);
          target.setAttribute('data-y', 0);

          // Aplique as novas posições diretamente
          target.style.left = `${newLeft}px`;
          target.style.top = `${newTop}px`;
        }
      },
    },
  });

  interact('.resizable').resizable({
    edges: { left: true, right: true, bottom: true, top: true },
    listeners: {
      move(event) {
        const target = event.target;
        const { width, height } = event.rect;

        target.style.width = width + 'px';
        target.style.height = height + 'px';

        const elementId = parseInt(target.getAttribute('data-id'));
        const element = previewElements.value.find(el => el.id === elementId);
        if (element) {
          element.width = width;
          element.height = height;
        }
      },
    },
  });
});


// Função para retornar a máscara baseada no método de login
const getMaskForMethod = computed(() => {
  const loginMethod = form.value.login_method[0]; // Seleciona o primeiro método de login
  if (loginMethod === 'CPF') {
    return '###.###.###-##'; // Máscara para CPF
  } else if (loginMethod === 'Telefone') {
    return '(##) ####-####'; // Máscara para Telefone
  } else {
    return ''; // Sem máscara para Email ou Nome
  }
});

// Função para retornar a máscara baseada no método de senha
const getMaskForPassword = computed(() => {
  const passwordMethod = form.value.password_method[0]; // Seleciona o primeiro método de senha
  if (passwordMethod === 'CPF') {
    return '###.###.###-##'; // Máscara para CPF
  } else {
    return ''; // Senha Livre ou Data de Nascimento
  }
});


// Função para capturar o estado da tela e salvar como JSON
const saveScreenAsJson = () => {
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
    input_width: form.value.input_width,
    input_height: form.value.input_height,
    input_color: form.value.input_color,
    elements: previewElements.value.map(element => ({
      id: element.id,
      type: element.type,
      top: element.top,   // Aqui deve ir a posição atualizada
      left: element.left, // Aqui deve ir a posição atualizada
      backgroundColor: form.value.input_color,
      width: element.type === 'input' || element.type === 'inputPassword' ? form.value.input_width : element.width || 100,   // Usa input_width se for input
      height: element.type === 'input' || element.type === 'inputPassword' ? form.value.input_height : element.height || 40, // Usa input_height se for input
      text: element.text || null,
      color: element.type === 'text' ? form.value.textColor : element.color || form.value.text_color, // Verifica se é um texto e usa textColor 
      shape: element.shape || form.value.login_button_shape,
      opacity: element.opacity || form.value.button_opacity,
      elevation: element.elevation || form.value.button_elevation,
      image: element.image || null,
    }))
  };

  // Converte para JSON e faz download
  const jsonString = JSON.stringify(screenData, null, 2);
  downloadJsonFile(jsonString, 'login_customization.json');
};



// Função para baixar o arquivo JSON
const downloadJsonFile = (jsonString, fileName) => {
  const blob = new Blob([jsonString], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = fileName;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

</script>

<style scoped>
.editable-text {
  padding: 8px;
  border: 'none';
  min-height: 50px;
  min-width: 150px;
  cursor: text;
}
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
  border: none !important;  /* Remover borda */
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
