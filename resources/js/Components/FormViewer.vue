<script setup>
import { defineProps } from 'vue';

// Recebe os dados dos formulários como props
const props = defineProps({
  forms: {
    type: Array,
    default: () => []
  }
});

// Emite eventos para o componente pai
const emit = defineEmits(['edit', 'delete']);
</script>

<template>
  <v-card v-for="form in forms" :key="form.id" class="mb-4">
    <v-card-title class="d-flex justify-space-between align-center">
      <div>
        <h3>{{ form.id }} - {{ form.title || 'Sem Título' }}</h3>
      </div>

      <!-- Ações de edição e exclusão -->
      <div class="actions">
        <v-btn icon color="primary" @click="$emit('edit', form)">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn icon color="error" @click="$emit('delete', form.id)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </div>
    </v-card-title>

    <v-card-text>
      <div v-for="(question, index) in form.questions" :key="index" class="mb-2">
        <strong>Pergunta:</strong> {{ question.text }}
        <div v-if="question.type === 'radio'">
          <strong>Tipo:</strong> Escolha Única
          <ul>
            <li v-for="(option, idx) in question.options" :key="idx">
              {{ option.text }} <span v-if="option.isCorrect">✔️</span>
            </li>
          </ul>
        </div>
        <div v-else-if="question.type === 'checkbox'">
          <strong>Tipo:</strong> Múltipla Escolha
          <ul>
            <li v-for="(option, idx) in question.options" :key="idx">
              {{ option.text }} <span v-if="option.isCorrect">✔️</span>
            </li>
          </ul>
        </div>
        <div v-else-if="question.type === 'text'">
          <strong>Tipo:</strong> Texto
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.mb-4 {
  margin-bottom: 16px;
}

.actions {
  display: flex;
  gap: 8px;
}
</style>
