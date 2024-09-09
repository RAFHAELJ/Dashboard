<template>
  <Head title="Campanhas" />

  <AuthenticatedLayout>
    <v-container fluid>
      <v-card>
        <v-card-title>
          {{ isEditing ? 'Editar' : 'Adicionar' }} Campanha
          <v-spacer></v-spacer>
          <v-btn icon @click="$emit('cancel')">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <v-form ref="dynamicForm" v-model="valid">
            <v-row>
              <!-- Nome da Campanha -->
              <v-col cols="12">
                <v-text-field
                  v-model="form.name"
                  label="Nome da Campanha"
                  :rules="[rules.required]"
                  required
                  full-width
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <!-- Período da Campanha -->
              <v-col cols="6">
                <v-text-field
                  v-model="form.comeco"
                  label="Começo"
                  type="date"
                  :rules="[rules.required]"
                  required
                  full-width
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="form.fim"
                  label="Fim"
                  type="date"
                  :rules="[rules.required]"
                  required
                  full-width
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <!-- Público da Campanha -->
              <v-col cols="12">
                <v-select
                  v-model="form.publico"
                  :items="['Homens', 'Mulheres', 'Todos']"
                  label="Público"
                  :rules="[rules.required]"
                  required
                  full-width
                ></v-select>
              </v-col>
            </v-row>

            <v-row>
              <!-- Tipo de Anúncio -->
              <v-col cols="12">
                <v-radio-group v-model="form.tipo" label="Tipo de Anúncio" @change="handleTipoChange" full-width>
                  <v-radio label="Imagem" value="imagem"></v-radio>
                  <v-radio label="Vídeo" value="video"></v-radio>
                </v-radio-group>
              </v-col>
            </v-row>

            <!-- Upload de Arquivos -->
            <v-row v-if="form.tipo === 'imagem'">
              <v-col cols="12">
                <v-file-input
                  v-model="form.imagem"
                  label="Upload de Imagem"
                  accept="image/*"
                  @change="previewImagem"
                  required
                  full-width
                ></v-file-input>
                <v-img v-if="form.previewImagem" :src="form.previewImagem" max-width="200"></v-img>
              </v-col>
            </v-row>

            <v-row v-if="form.tipo === 'video'">
              <v-col cols="6">
                <v-file-input
                  v-model="form.capa"
                  label="Upload da Capa"
                  accept="image/*"
                  @change="previewCapa"
                  required
                  full-width
                ></v-file-input>
                <v-img v-if="form.previewCapa" :src="form.previewCapa" max-width="200"></v-img>
              </v-col>
              <v-col cols="6">
                <v-file-input
                  v-model="form.video"
                  label="Upload do Vídeo"
                  accept="video/*"
                  @change="previewVideo"
                  required
                  full-width
                ></v-file-input>
                <v-video v-if="form.previewVideo" :src="form.previewVideo" max-width="200" controls></v-video>
              </v-col>
            </v-row>

            <v-row>
              <!-- Duração -->
              <v-col cols="12">
                <v-slider
                  v-model="form.tempo"
                  label="Duração (segundos)"
                  :min="1"
                  :max="60"
                  required
                  full-width
                ></v-slider>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="submitForm">Salvar</v-btn>
          <v-btn text @click="$emit('cancel')">Cancelar</v-btn>
        </v-card-actions>
      </v-card>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


const form = reactive({
  name: '',
  comeco: '',
  fim: '',
  publico: '',
  tipo: '',
  imagem: null,
  capa: null,
  video: null,
  tempo: 30,
  previewImagem: '',
  previewCapa: '',
  previewVideo: ''
});

const rules = {
  required: value => !!value || 'Campo obrigatório'
};

const handleTipoChange = () => {
  form.previewImagem = '';
  form.previewCapa = '';
  form.previewVideo = '';
};

const previewImagem = (file) => {
  const reader = new FileReader();
  reader.onload = e => form.previewImagem = e.target.result;
  reader.readAsDataURL(file);
};

const previewCapa = (file) => {
  const reader = new FileReader();
  reader.onload = e => form.previewCapa = e.target.result;
  reader.readAsDataURL(file);
};

const previewVideo = (file) => {
  const reader = new FileReader();
  reader.onload = e => form.previewVideo = e.target.result;
  reader.readAsDataURL(file);
};

const submitForm = () => {
  // Lógica para enviar o formulário
  console.log('Form data:', form);
};
</script>
