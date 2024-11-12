<template>
  <v-card>
    <v-card-title>
      <span class="headline">Adicionar/Editar Card</span>
    </v-card-title>
    <v-card-text>
      <v-form>
        <v-select
          v-model="cardData.url"
          :items="urlOptions"
          item-text="title"
          item-value="value"
          label="Selecionar Dados"
          required
        ></v-select>
        <v-text-field
          v-model="cardData.title"
          label="Nome"
          required
        ></v-text-field>
        <v-select
          v-model="cardData.type"
          :items="['Gráfico', 'Texto']"
          label="Tipo de Exibição"
          required
        ></v-select>
        <v-select
          v-if="cardData.type === 'Gráfico'"
          v-model="cardData.format"
          :items="['line', 'bar']"
          label="Formato do Gráfico"
          required
        ></v-select>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-btn @click="saveCard" :disabled="isSaving" color="primary">
        <!-- Ícone de carregamento enquanto o estado de "isSaving" for verdadeiro -->
        <v-icon v-if="isSaving" class="mr-2">mdi-loading</v-icon>
        Salvar
      </v-btn>
      <v-btn @click="closeDialog" color="secondary" :disabled="isSaving">Cancelar</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref, reactive,  defineEmits } from 'vue';
import { useForm } from '@inertiajs/vue3'; // Importa useForm do Inertia.js

const isSaving = ref(false); // Estado para controlar o loading

const emit = defineEmits(['add', 'close']);

const urlOptions = ref([
  { title: 'Estatísticas de Acessos', value: 'statistics/access' },
  { title: 'Uso de Armazenamento', value: 'statistics/storage' },
  { title: 'Info geral user/radio', value: 'radios/getRadiosInfo' },
  { title: 'usuarios por regiao e nivel', value: 'users/count' },
  { title: 'banco de dados /controller', value: 'database/showStatistics' }
]);

const cardData = reactive({
  url: '',
  title: '',
  type: '',
  page: 'home',
  format: 'line'
});
const fetchData = async (url) => {
  try {
    const response = await fetch(url);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
    return null;
  }
};
const saveCard = async () => {
  isSaving.value = true; // Inicia o estado de carregamento

  const data = await fetchData(cardData.url);

  const form = useForm(cardData);
  const method = cardData.id ? 'put' : 'post';
  const url = cardData.id ? `/cards/${cardData.id}` : '/cards';

  try {
    await form.submit(method, url, {
      onSuccess: () => {
        emit('add', cardData);
        emit('close');
      },
      onError: (errors) => {
        console.error('Erro ao salvar o card:', errors);
      }
    });
  } catch (error) {
    console.error('Erro ao salvar o card:', error);
  } finally {
    isSaving.value = false; // Finaliza o estado de carregamento
  }
};

const closeDialog = () => {
  emit('close');
};
</script>

<style scoped>
/* Estilos específicos para o formulário */
</style>
