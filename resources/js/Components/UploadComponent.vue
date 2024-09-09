<template>
  <v-card class="pa-3">
    <v-card-title>
      <span class="headline">{{ label }}</span>
    </v-card-title>
    <v-card-text>
      <v-file-input
        v-model="file"
        :accept="accept"
        label="Escolher Arquivo"
        prepend-icon="mdi-upload"
        @change="onFileChange"
      ></v-file-input>
      
      <!-- Exibe a imagem pré-visualizada -->
      <v-img
        v-if="preview && !isVideo"
        :src="preview"
        max-width="100%"
        class="mt-3"
      ></v-img>

      <!-- Exibe o vídeo pré-visualizado -->
      <video
        v-if="preview && isVideo"
        controls
        :src="preview"
        max-width="100%"
        class="mt-3"
      ></video>
    </v-card-text>

    <v-card-actions>
      <v-btn v-if="showUpload" color="primary" @click="uploadFile">Upload</v-btn>
      <v-btn v-if="showRemove" color="error" @click="removeFile">Remover</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  label: {
    type: String,
    default: 'Upload de Arquivo',
  },
  accept: {
    type: String,
    default: 'image/*,video/*',
  },
  showUpload: {
    type: Boolean,
    default: true,
  },
  showRemove: {
    type: Boolean,
    default: true,
  },
});

const file = ref(null); // Armazena o arquivo selecionado
const preview = ref(null); // Armazena o URL de pré-visualização
const isVideo = ref(false); // Verifica se é vídeo

const onFileChange = (newFile) => {
  if (newFile && newFile instanceof File) {
    preview.value = URL.createObjectURL(newFile);
    isVideo.value = newFile.type.startsWith('video');
  } else {
    preview.value = null;
    isVideo.value = false;
  }
};

const uploadFile = () => {
  if (!file.value) {
    console.log('Nenhum arquivo selecionado');
    return;
  }

  const formData = new FormData();
  formData.append('file', file.value);

  useForm(formData).post(route('upload.store'), {
    onSuccess: () => {
      console.log('Upload realizado com sucesso');
    },
    onError: (errors) => {
      console.error('Erro no upload:', errors);
    }
  });
};

const removeFile = () => {
  file.value = null;
  preview.value = null;
  isVideo.value = false;
};
</script>
