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
          v-model="form.name"
          :label="fields.name.label"
          :rules="fields.name.rules"
          :required="fields.name.required"
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
          v-model="form.radio" 
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
        <!-- Regiao -->
        <v-select
          v-model="form.regiao"
          :items="fields.regiao.options"
          :label="fields.regiao.label"
          :required="fields.regiao.required"
        ></v-select>

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


        <!-- Condicional: se tipo for 'imagem', mostra upload de imagem e campo de duração -->
        <div v-if="form.tipo === 'imagem'">
          <v-file-input
            v-model="form.imagem"
            label="Upload de Imagem"
            accept="image/*"
            prepend-icon="mdi-image"
            :show-size="true"
            :rules="[v => !!v || 'Imagem é obrigatória']"
            @change="onFileChange"
          ></v-file-input>
          <v-text-field
            v-model="form.duracao"
            label="Duração (em segundos)"
            type="number"
            :rules="[v => !!v || 'Campo obrigatório']"
            required
          ></v-text-field>
        </div>

        <!-- Condicional: se tipo for 'vídeo', mostra upload de vídeo, capa e campo de duração -->
        <div v-if="form.tipo === 'video'">
          <v-file-input
            v-model="form.video"
            label="Upload de Vídeo"
            accept="video/*"
            prepend-icon="mdi-video"
            :show-size="true"
            :rules="[v => !!v || 'Vídeo é obrigatório']"
            @change="onFileChange"
          ></v-file-input>
          <v-file-input
            v-model="form.capa"
            label="Upload de Capa (Imagem)"
            accept="image/*"
            prepend-icon="mdi-image"
            :show-size="true"
            :rules="[v => !!v || 'Capa é obrigatória']"
            @change="onFileChange"
          ></v-file-input>
          <v-text-field
            v-model="form.duracao"
            label="Duração do Vídeo (em segundos)"
            type="number"
            :rules="[v => !!v || 'Campo obrigatório']"
            required
          ></v-text-field>
        </div>

        <!-- Campo URL -->
        <v-text-field
          v-model="form.url"
          :label="fields.url.label"
          :rules="fields.url.rules"
          :required="fields.url.required"
        ></v-text-field>

        <!-- Campo Tempo (slider) -->
        <v-row>
          <v-col>
            <v-slider
              v-model="form.tempo"
              :min="fields.tempo.min"
              :max="fields.tempo.max"
              :label="fields.tempo.label"
              ticks
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
import { useForm } from '@inertiajs/vue3';

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

const snackbar = reactive({ show: false, text: '', timeout: 3000 });
const emit = defineEmits(['cancel']);
const form = reactive({ ...props.formData });
const valid = ref(true);
const loading = ref(false);

watch(
  () => props.formData,
  (newData) => {
    Object.assign(form, newData);
  }
);

const onFileChange = (file) => {
  console.log("Arquivo selecionado: ", file);
};

const submitForm = async () => {
  loading.value = true;

  const formData = new FormData();
  for (const key in form) {
    if (form[key] instanceof File || form[key] instanceof Blob) {
      formData.append(key, form[key]);
    } else if (Array.isArray(form[key])) {
      form[key].forEach((item) => {       
        formData.append(`${key}[]`, item);
      });
    } else {
      formData.append(key, form[key]);
    }
  }

  console.log('FormData:', [...formData.entries()]);

  const routeName = props.isEditing ? props.updateRoute : props.createRoute;
  const method = props.isEditing ? 'PUT' : 'POST';
  const routeParams = props.isEditing ? { id: props.formData.id } : {};

  try {
    const response = await fetch(route(routeName, routeParams), {
      method: method,
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    if (response.ok) {
      snackbar.text = 'Formulário enviado com sucesso!';
      snackbar.show = true;
      emit('cancel');
    } else {
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

</script>
