<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  form: {
    type: Object,
    default: () => ({
      title: '',
      questions: []
    })
  }
});

const emit = defineEmits(['save', 'cancel']);

// Estado do formulário
const formState = ref({ ...props.form });

// Observa mudanças no prop `form` para atualizar o estado local
watch(
  () => props.form,
  (newForm) => {
    formState.value = { ...newForm };
  },
  { deep: true, immediate: true }
);

// Nova pergunta
const newQuestion = ref({
  text: '',
  type: 'text',
  options: [],
  correctAnswer: null
});

// Tipos de perguntas
const questionTypes = [
  { label: 'Texto', value: 'text' },
  { label: 'Escolha Única', value: 'radio' },
  { label: 'Múltipla Escolha', value: 'checkbox' }
];

// Adiciona uma nova pergunta ao formulário
const addQuestion = () => {
  if (newQuestion.value.text) {
    const questionToAdd = { ...newQuestion.value };
    formState.value.questions.push(questionToAdd);
    resetNewQuestion();
  }
};

// Reseta a nova pergunta
const resetNewQuestion = () => {
  newQuestion.value = {
    text: '',
    type: 'text',
    options: [],
    correctAnswer: null
  };
};

// Adiciona uma nova opção para perguntas do tipo 'radio' ou 'checkbox'
const addOption = (question) => {
  question.options.push({ text: '', isCorrect: false });
};

// Remove uma opção de resposta
const removeOption = (question, index) => {
  question.options.splice(index, 1);
};

// Define a resposta correta para perguntas do tipo 'radio'
const setCorrectAnswer = (question, index) => {
  question.correctAnswer = index;
};

// Salva o formulário
const saveForm = () => {
    console.log('formState', formState.value);
  // Verifica se o título do formulário está preenchido
  if (!formState.value.title.trim()) {
    alert('O título do formulário é obrigatório.');
    return;
  }

  // Verifica se há perguntas no formulário
  if (formState.value.questions.length === 0) {
    alert('Adicione pelo menos uma pergunta antes de salvar.');
    return;
  }

  // Emite o evento de salvar com o estado do formulário
  emit('save', formState.value);
};

// Cancela a edição do formulário
const cancelForm = () => {
  emit('cancel');
};
</script>

<template>


 

    
  <v-card>
    <v-card-title>
      <v-text-field
        v-model="formState.title"
        label="Título do Formulário"
        variant="solo"
        required
      />
    </v-card-title>

    <v-card-subtitle class="pb-4">
      <h4>Adicionar Pergunta</h4>
      <v-text-field
        v-model="newQuestion.text"
        label="Texto da Pergunta"
        variant="outlined"
        class="mb-2"
        required
      />
      <v-select
        v-model="newQuestion.type"
        :items="questionTypes"
        label="Tipo de Pergunta"
        item-title="label"
        item-value="value"
        variant="outlined"
      />
      <v-btn @click="addQuestion" color="primary" class="mt-2">Adicionar Pergunta</v-btn>
    </v-card-subtitle>

    <v-divider></v-divider>

    <v-card-text>
      <div v-for="(question, index) in formState.questions" :key="index" class="mb-6">
        <h5>{{ question.text }}</h5>

        <!-- Tipo de pergunta: Texto -->
        <div v-if="question.type === 'text'">
          <v-text-field label="Resposta (Texto)" variant="outlined" disabled />
        </div>

        <!-- Tipo de pergunta: Escolha Única -->
        <div v-else-if="question.type === 'radio'">
          <div v-for="(option, idx) in question.options" :key="idx" class="d-flex align-center mb-2">
            <v-radio
              :label="option.text || 'Opção'"
              :value="idx"
              v-model="question.correctAnswer"
              @change="setCorrectAnswer(question, idx)"
            />
            <v-text-field
              v-model="option.text"
              label="Opção"
              class="ml-2"
            />
            <v-btn icon @click="removeOption(question, idx)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </div>
          <v-btn small @click="addOption(question)" color="primary">Adicionar Opção</v-btn>
        </div>

        <!-- Tipo de pergunta: Múltipla Escolha -->
        <div v-else-if="question.type === 'checkbox'">
          <div v-for="(option, idx) in question.options" :key="idx" class="d-flex align-center mb-2">
            <v-checkbox
              v-model="option.isCorrect"
            />
            <v-text-field
              v-model="option.text"
              label="Opção"
              class="ml-2"
            />
            <v-btn icon @click="removeOption(question, idx)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </div>
          <v-btn small @click="addOption(question)" color="primary">Adicionar Opção</v-btn>
        </div>
      </div>
    </v-card-text>

    <v-card-actions>
      <v-btn color="primary" @click="saveForm">Salvar</v-btn>
      <v-btn @click="cancelForm">Cancelar</v-btn>
    </v-card-actions>
  </v-card>
</template>

<style scoped>
.mb-6 {
  margin-bottom: 24px;
}
</style>
