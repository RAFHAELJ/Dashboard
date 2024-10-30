<template>
    <v-container>
      <v-card class="pa-4">
        <v-form ref="formBuilder" @submit.prevent="handleSubmit">
          <v-row v-for="(question, index) in questions" :key="index" class="mb-4">
            <v-col cols="12">
              <v-text-field
                v-model="question.text"
                label="Texto da Pergunta"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-select
                v-model="question.type"
                :items="questionTypes"
                label="Tipo de Resposta"
                required
              ></v-select>
            </v-col>
            <v-col cols="12" v-if="question.type === 'Escolha Múltipla'">
              <v-text-field
                v-for="(option, optIndex) in question.options"
                :key="optIndex"
                v-model="question.options[optIndex]"
                label="Opção de Resposta"
                @click:append="addOption(index)"
                append-icon="mdi-plus"
              ></v-text-field>
            </v-col>
            <v-btn color="red" @click="removeQuestion(index)">Remover Pergunta</v-btn>
          </v-row>
  
          <v-btn color="primary" @click="addQuestion">Adicionar Pergunta</v-btn>
          <v-btn color="success" type="submit">Salvar Formulário</v-btn>
        </v-form>
      </v-card>
    </v-container>
  </template>
  
<script >
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default {
  setup() {
    const questions = ref([]);
    const questionTypes = ['Texto', 'Escolha Múltipla', 'Resposta Única'];

    // Adiciona uma nova pergunta
    const addQuestion = () => {
      questions.value.push({
        text: '',
        type: '',
        options: [],
      });
    };

    // Remove uma pergunta existente
    const removeQuestion = (index) => {
      questions.value.splice(index, 1);
    };

    // Adiciona uma opção de resposta
    const addOption = (index) => {
      questions.value[index].options.push('');
    };

    // Submete o formulário para o backend
    const handleSubmit = () => {
      axios.post('/forms/store', { questions: questions.value })
        .then((response) => {
          // Lida com a resposta do backend
          console.log('Formulário salvo com sucesso:', response.data);
        })
        .catch((error) => {
          console.error('Erro ao salvar o formulário:', error);
        });
    };

    return {
      questions,
      questionTypes,
      addQuestion,
      removeQuestion,
      addOption,
      handleSubmit,
    };
  },
};
</script>
