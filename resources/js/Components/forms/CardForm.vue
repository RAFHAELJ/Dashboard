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
        <v-textarea
          v-if="cardData.type === 'Texto'"
          v-model="cardData.content"
          label="Conteúdo"
          :style="{ backgroundColor: 'transparent' }"
        ></v-textarea>

        <!-- Campo oculto para o parâmetro page -->
        <v-text-field
          v-model="cardData.page"
          type="hidden"
        ></v-text-field>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-btn @click="saveCard" color="primary">Salvar</v-btn>
      <v-btn @click="closeDialog" color="secondary">Cancelar</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref, reactive, defineProps, defineEmits } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  dialog: Boolean,
  card: Object,
  page: String,
});

const emit = defineEmits(['update', 'close']);

const urlOptions = ref([
  { title: 'Usuários (Dashboard)', value: 'users/count' },
  { title: 'Usuários (Rádio)', value: 'users/radio/count' },
  { title: 'Rádios', value: 'radios/count' },
  { title: 'Radius', value: 'radius/count' },
]);

const cardData = reactive({
  id: props.card?.id || null,
  url: props.card?.url || '',
  title: props.card?.title || '',
  type: props.card?.type || '',
  content: props.card?.content || '',
  chart_options: props.card?.chartOptions || {},
  page: props.page || '', // Adiciona o parâmetro page aqui
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
  const data = await fetchData(cardData.url);

  if (cardData.type === 'Texto') {
    cardData.content = data;
  } else if (cardData.type === 'Gráfico') {
    cardData.chartOptions = data;
  }

  // Verifique se a página está sendo incluída corretamente
  console.log('Dados do card antes de salvar:', cardData);

  const method = cardData.id ? 'put' : 'post';
  const url = cardData.id ? `/cards/${cardData.id}` : '/cards';

  try {
    // Envia o cardData diretamente para garantir que todos os dados sejam enviados
    await useForm(cardData).submit(method, url, {
      onSuccess: () => {
        emit('update');
        emit('close');
      },
      onError: (errors) => {
        console.error('Erro ao salvar o card:', errors);
      }
    });
  } catch (error) {
    console.error('Erro ao salvar o card:', error);
  }
};

const closeDialog = () => {
  emit('close');
};
</script>

<style scoped>
/* Adicione estilos específicos para o formulário aqui */
</style>
