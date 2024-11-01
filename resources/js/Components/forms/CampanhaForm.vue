<template>
  <v-card>
    <v-card-title>
      {{ isEditing ? 'Editar' : 'Adicionar' }} {{ title }}
      <v-spacer></v-spacer>
      <v-btn icon @click="$emit('cancel')">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <v-form ref="dynamicForm" v-model="valid">
        <!-- Nome da campanha -->
        <v-text-field
          v-model="form.nome"
          :label="fields.nome.label"
          :rules="fields.nome.rules"
          :required="fields.nome.required"
        ></v-text-field>

        <!-- Período da campanha (intervalo de datas) -->
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="form.comeco"
              :label="fields.periodo.startLabel"
              type="date"
              :rules="fields.periodo.rules"
              :required="fields.periodo.required"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="form.fim"
              :label="fields.periodo.endLabel"
              type="date"
              :rules="fields.periodo.rules"
              :required="fields.periodo.required"
            ></v-text-field>
          </v-col>
        </v-row>

        <!-- Rádios da campanha -->
        <v-select
          v-model="form.radios"
          :items="radios"
          :label="fields.radio.label"
          :rules="fields.radio.rules"
          multiple
          item-title="radio"
          item-value="id"
        ></v-select>

        <!-- Público -->
        <v-select
          v-model="form.publico"
          :items="fields.publico.options"
          :label="fields.publico.label"
          :required="fields.publico.required"
        ></v-select>

        <!-- Região -->
        <regioes-select
          v-model="form.regiao"
          label="Selecione uma região"
          :rules="[v => !!v || 'A seleção de uma região é obrigatória']"
        ></regioes-select>

        <!-- Idade mínima e máxima -->
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="form.minimo"
              :label="fields.idade.startLabel"
              type="number"
              :rules="fields.idade.rules"
              :required="fields.idade.required"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="form.maxima"
              :label="fields.idade.endLabel"
              type="number"
              :rules="fields.idade.rules"
              :required="fields.idade.required"
            ></v-text-field>
          </v-col>
        </v-row>

        <!-- Tipo de anúncio (radio) -->
        <v-radio-group
          v-model="form.tipo"
          :label="fields.tipo.label"
          :rules="fields.tipo.rules"
          :required="fields.tipo.required"
        >
          <v-radio
            v-for="option in fields.tipo.options"
            :key="option.value"
            :label="option.text"
            :value="option.value"
          ></v-radio>
        </v-radio-group>

        <!-- Campo para o URL do Google Forms -->
<!-- Campo para o URL do Google Forms e Upload de Capa -->
<div v-if="form.tipo === 'formulario'">
  <!-- URL do Google Forms -->
  <v-text-field
    v-model="form.urlForms"
    label="URL do Google Forms"
    :rules="[v => !!v || 'URL é obrigatória']"
    required
  ></v-text-field>

  <!-- Upload de Capa -->
  <v-row>
    <v-col cols="12">
      <v-file-input
        v-model="form.capa"
        label="Upload de Capa (Imagem)"
        accept="image/*"
        prepend-icon="mdi-image"
        :show-size="true"
        :rules="[v => !!v || 'Capa é obrigatória']"
        @change="onFileChange"
      ></v-file-input>
    </v-col>
  </v-row>

  <!-- Visualizador de Capa -->
  <v-row v-if="originalCapa">
    <v-col cols="12">
      <v-img
        :src="`/storage/${originalCapa}`"
        max-height="150"
        alt="Capa do formulário"
      ></v-img>
    </v-col>
  </v-row>

  <!-- Duração do Formulário -->
  <v-row>
    <v-col cols="12">
      <v-text-field
        v-model="form.duracao"
        label="Duração (em segundos)"
        type="number"
        :rules="[v => !!v || 'Campo obrigatório']"
        required
      ></v-text-field>
    </v-col>
  </v-row>
</div>

        <!-- Upload de Imagem -->
        <div v-if="form.tipo === 'imagem'">
          <v-row>
            <v-col cols="12">
              <v-file-input
                v-model="form.imagem"
                label="Upload de Imagem"
                accept="image/*"
                prepend-icon="mdi-image"
                :show-size="true"
                :rules="[v => !!v || 'Imagem é obrigatória']"
                @change="onFileChange"
              ></v-file-input>
            </v-col>
          </v-row>

          <!-- Visualizador de Imagem -->
          <v-row v-if="originalImagem">
            <v-col cols="12">
              <v-img
                :src="`/storage/${originalImagem}`"
                max-height="150"
                alt="Imagem da campanha"
              ></v-img>
            </v-col>
          </v-row>

          <!-- Duração da Imagem -->
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.duracao"
                label="Duração (em segundos)"
                type="number"
                :rules="[v => !!v || 'Campo obrigatório']"
                required
              ></v-text-field>
            </v-col>
          </v-row>
        </div>

        <!-- Upload de Vídeo e Capa -->
        <div v-if="form.tipo === 'video'">
          <v-row>
            <v-col cols="6">
              <v-file-input
                v-model="form.video"
                label="Upload de Vídeo"
                accept="video/*"
                prepend-icon="mdi-video"
                :show-size="true"
                :rules="[v => !!v || 'Vídeo é obrigatório']"
                @change="onFileChange"
              ></v-file-input>
            </v-col>

            <v-col cols="6">
              <v-file-input
                v-model="form.capa"
                label="Upload de Capa (Imagem)"
                accept="image/*"
                prepend-icon="mdi-image"
                :show-size="true"
                :rules="[v => !!v || 'Capa é obrigatória']"
                @change="onFileChange"
              ></v-file-input>
            </v-col>
          </v-row>

          <!-- Visualizadores de Vídeo e Capa -->
          <v-row v-if="originalVideo">
            <v-col cols="6">
              <video
                :src="`/storage/${originalVideo}`"
                width="320"
                height="240"
                controls
              >
                Seu navegador não suporta vídeos.
              </video>
            </v-col>
          </v-row>

          <v-row v-if="originalCapa">
            <v-col cols="6">
              <v-img
                :src="`/storage/${originalCapa}`"
                max-height="150"
                alt="Capa do vídeo"
              ></v-img>
            </v-col>
          </v-row>

          <!-- Duração do Vídeo -->
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.duracao"
                label="Duração do Vídeo (em segundos)"
                type="number"
                :rules="[v => !!v || 'Campo obrigatório']"
                required
              ></v-text-field>
            </v-col>
          </v-row>
        </div>

        <!-- Campo URL -->
        <v-text-field
          v-model="form.url"
          :label="fields.url.label"
          :rules="fields.url.rules"
          :required="fields.url.required"
        ></v-text-field>

        <!-- Campo Tempo (slider) -->
        <v-row v-if="form.tempo">
          <v-col>
            <v-slider
              v-model="form.tempo"
              :min="fields.tempo.min"
              :max="fields.tempo.max"
              :label="fields.tempo.label"
              tick-size="2"
              thumb-label="always"
              thumb-size="24"
            ></v-slider>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn :loading="loading" color="primary" @click="submitForm">Salvar</v-btn>
      <v-btn text @click="$emit('cancel')">Cancelar</v-btn>
    </v-card-actions>
  </v-card>
  <v-snackbar v-model="snackbar.show" :timeout="snackbar.timeout">
    {{ snackbar.text }}
  </v-snackbar>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import RegioesSelect from '../RegioesSelect.vue'; // ajuste o caminho se necessário
const errors = reactive({});
const props = defineProps({
  formData: {
    type: Object,
    default: () => ({}),
  },
  fields: {
    type: Object,
    required: true,
  },
  radios: {
    type: Array,
    default: () => [],
    required: true,
  },
  isEditing: Boolean,
  title: {
    type: String,
    default: 'Item',
  },
  createRoute: {
    type: String,
    required: true,
  },
  updateRoute: {
    type: String,
    required: true,
  },
});
const dynamicForm = ref(null);
const snackbar = reactive({ show: false, text: '', timeout: 3000 });
const emit = defineEmits(['cancel']);
const form = reactive({
  ...props.formData,

  // Verifica se `radios` é válido antes de tentar fazer o JSON.parse
  radios: props.formData.radios ? JSON.parse(props.formData.radios).map(item => parseInt(item)) : [],

  // Inicializa os campos de idade
  minimo: props.formData.idade ? parseInt(props.formData.idade.split(',')[0]) : null,
  maxima: props.formData.idade ? parseInt(props.formData.idade.split(',')[1]) : null,

  // Inicializa imagem, capa e vídeo como null se forem strings
  imagem: typeof props.formData.imagem === 'string' ? null : props.formData.imagem,
  capa: typeof props.formData.capa === 'string' ? null : props.formData.capa,
  video: typeof props.formData.video === 'string' ? null : props.formData.video,
});

// Variáveis para armazenar os valores originais das mídias (caso existam)
const originalImagem = ref(typeof props.formData.imagem === 'string' ? props.formData.imagem : null);
const originalCapa = ref(typeof props.formData.capa === 'string' ? props.formData.capa : null);
const originalVideo = ref(typeof props.formData.video === 'string' ? props.formData.video : null);
const valid = ref(true);
const loading = ref(false);

watch(
  () => props.formData,
  (newData) => {
    Object.assign(form, newData);
  }
  
);

const onFileChange = (file) => {
  console.log("Arquivo selecionado:", file);
};

const submitForm = async () => {
  const isValid = dynamicForm.value.validate();

  if (!isValid) {
    snackbar.text = 'Por favor, preencha todos os campos obrigatórios corretamente.';
    snackbar.show = true;
    return;
  }

  loading.value = true;
  errors.value = {}; // Limpa os erros anteriores

  const formData = new FormData();

  for (const key in form) {
    if (form[key] !== null && form[key] !== 'null') {
      if (form[key] instanceof File) {
        formData.append(key, form[key]);
      } else if (Array.isArray(form[key])) {
        form[key].forEach((item) => {
          formData.append(`${key}[]`, item);
        });
      } else {
        formData.append(key, form[key]);
      }
    }
  }

  if (props.isEditing) {
    formData.append('_method', 'PUT');
  }

  const routeName = props.isEditing ? props.updateRoute : props.createRoute;
  const routeParams = props.isEditing ? { id: props.formData.id } : {};

  try {
    const response = await fetch(route(routeName, routeParams, true), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
       
      }
    });

    if (response.status === 422) {
      // Captura os erros de validação (status 422)
      const responseData = await response.json();
      handleErrors(responseData.errors);

      // Concatena todas as mensagens de erro
      const errorMessages = Object.values(responseData.errors).flat().join(' ');
      snackbar.text = errorMessages || 'Erro de validação. Corrija os erros e tente novamente.';
      snackbar.show = true;
    } else if (response.ok) {
      snackbar.text = 'Formulário enviado com sucesso!';
      snackbar.show = true;
      
      // Emite um evento para o componente pai e fecha a modal
      emit('formSubmitted');
      
      // Fecha a modal após o sucesso
      setTimeout(() => {
        emit('cancel'); // Emite o evento para fechar a modal
      }, 500);
    } else {
      const responseData = await response.json();
      console.error('Erro no servidor:', responseData);
      snackbar.text = 'Erro ao enviar o formulário. Tente novamente.';
      snackbar.show = true;
    }
  } catch (error) {
    console.error('Erro ao submeter o formulário:', error);
    snackbar.text = 'Erro ao enviar o formulário. Tente novamente.';
    snackbar.show = true;
  } finally {
    loading.value = false;
  }
};

// Função para processar os erros de validação e vinculá-los aos campos
const handleErrors = (errorsData) => {
  for (const field in errorsData) {
    if (errorsData.hasOwnProperty(field)) {
      errors[field] = errorsData[field];
    }
  }
};

</script>
