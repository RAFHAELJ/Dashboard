<template>
  <Head title="Home" />
  <AuthenticatedLayout>
    <v-container fluid>
      
      <!-- Cards Fixos para Total Dispositivos, Novos Clientes e Acessos Hoje -->
      <v-row class="fixed-cards" dense>
        <v-col cols="12" sm="4">
          <FixedCard
            title="Total Dispositivos"
            icon="mdi-radio-tower"
            :value="cardsData.totalRadios"
            color="#42A5F5"
          />
        </v-col>
        <v-col cols="12" sm="4">
          <FixedCard
            title="Novos Clientes"
            icon="mdi-account-multiple-plus"
            :value="cardsData.novosUsers"
            color="#66BB6A"
          />
        </v-col>
        <v-col cols="12" sm="4">
          <FixedCard
            title="Acessos Hoje"
            icon="mdi-wifi"
            :value="cardsData.totalAcessos"
            color="#FFCA28"
          />
        </v-col>
      </v-row>
      
      <!-- Cards Dinâmicos -->
      <v-row class="draggable-cards">
        <v-col cols="12" sm="8" md="4" lg="12"  >
        <Draggable
          v-model="cards"
          tag="v-row"
          class="d-flex align-items-stretch"
          :itemKey="getItemKey"
        >
          <template #item="{ element, index, isDragging }">
            <CardComponent :card="element" :isDragging="isDragging" @remove="removeCard" />
          </template>
        </Draggable>
        </v-col>
      </v-row>
      
      <!-- Botão para adicionar cards -->
      <v-btn @click="openCardDialog" color="primary" class="add-card-button" fab dark>
        <v-icon>mdi-plus</v-icon>
      </v-btn>

      <!-- Diálogo para adicionar card -->
      <v-dialog v-model="cardDialog" max-width="500px">
        <CardForm @close="closeDialog" @add="saveCard" :page="'home'"/>
      </v-dialog>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router,usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Draggable from 'vuedraggable';
import CardComponent from '@/Components/CardComponent.vue';
import FixedCard from '@/Components/FixedCard.vue';
import dayjs from 'dayjs';
import CardForm from '@/Components/forms/CardForm.vue';

const { props } = usePage();
const cards = ref(
  props.cards?.map((card, index) => ({
    ...card,
    id: card.id || `card-${index}`, // Garante uma chave única para cada card
  })) || []
);

const notes = ref([]);
const cardDialog = ref(false);

const cardsData = ref({
  totalRadios: 0,
  novosUsers: 0,
  totalAcessos: 0
});

const getItemKey = (item, index) => {
  return item.id || `default-key-${index}`;
};

const fetchFixedCardsData = async () => {
  try {
    const response = await axios.get('radios/getRadiosInfo');
    if (response.data.success && response.data.data) {
      const info = response.data.data;
      cardsData.value.totalRadios = info.totalRadios;
      cardsData.value.novosUsers = info.novosUsers;
      cardsData.value.totalAcessos = info.totalAcessos;
    } else {
      console.error('Estrutura de dados inesperada:', response.data);
    }
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
  }
};


const fetchCardContent = async (card) => {
  card.loading = true;  // Iniciar estado de loading
  const iconMap = {
    users: 'mdi-account',
    campanhas: 'mdi-bullhorn',
    radio: 'mdi-radio-tower',
    radius: 'mdi-server',
    log: 'mdi-archive',
    database: 'mdi-database',
    controller: 'mdi-laptop',
    default: 'mdi-information',
  };

  try {
    const response = await axios.get(card.url);

    // Verificar se a resposta contém os dados esperados
    if (response.data && response.data.success) {
      const data = response.data.data;
      const urlParam = card.url.split('/')[0];
      card.icon = iconMap[urlParam] || iconMap.default;

      // Verifica o tipo de conteúdo a ser exibido no card
      if (card.type === 'Texto') {
        card.content = response.data;
      } else if (card.type === 'Gráfico') {
        // Diferentes tipos de gráficos baseados em URL
        if (card.url === 'statistics/access') {
          // Gráfico de Acessos Totais
          const dates = data.map(entry => dayjs(entry.date).format('DD/MM/YYYY'));
          const totalAccesses = data.map(entry => entry.total_accesses);

          card.chartOptions = {
            title: { text: card.title || 'Estatísticas de Acessos' },
            tooltip: { trigger: 'axis' },
            xAxis: {
              type: 'category',
              data: dates,
            },
            yAxis: { type: 'value' },
            series: [
              {
                name: 'Acessos Totais',
                type: card.format || 'line',
                data: totalAccesses,
              },
            ],
          };
        } else if (card.url === 'statistics/storage') {
          // Gráfico de Uso de Armazenamento
          const totalSize = data.total / (1024 * 1024 * 1024); // Converte para GB
          const usedSize = data.used / (1024 * 1024 * 1024); // Converte para GB

          card.chartOptions = {
            title: { text: card.title || 'Uso de Armazenamento' },
            tooltip: {
              trigger: 'item', // Trigger padrão do tooltip
            },
            legend: { top: 'bottom' },
            series: [
              {
                name: 'Armazenamento',
                type: 'pie',
                radius: ['40%', '70%'],
                data: [
                  { value: usedSize, name: 'Usado' },
                  { value: totalSize - usedSize, name: 'Livre' },
                ],
              },
            ],
          };
        } else if (card.url === 'radios/getRadiosInfo') {
          // Gráfico de Totais de Usuários e Rádios
          const totalRadios = data.totalRadios;
          const novosUsers = data.novosUsers;
          const totalAcessos = data.totalAcessos;

          card.chartOptions = {
            title: { text: card.title || 'Totais de Usuários e Rádios' },
            tooltip: { trigger: 'axis' },
            xAxis: {
              type: 'category',
              data: ['Rádios', 'Novos Usuários', 'Acessos Totais'],
            },
            yAxis: { type: 'value' },
            series: [
              {
                name: 'Totais',
                type: card.format || 'bar',
                data: [totalRadios, novosUsers, totalAcessos],
              },
            ],
          };
        } else if (card.url === 'users/count') {
          // Gráfico de Totais de Usuários por Região e Nível
          const usersByRegion = data.users_by_region.map(item => ({ value: item.value, name: item.name }));
          const usersByLevel = data.users_by_level.map(item => ({ value: item.value, name: item.name }));

          card.chartOptions = {
            title: { text: card.title || 'Usuários por Região e Nível' },
            tooltip: { trigger: 'item', formatter: '{b}: {c} ({d}%)' },
            legend: { top: 'bottom' },
            series: [
              {
                name: 'Usuários por Região',
                type: 'pie',
                radius: '50%',
                data: usersByRegion,
                label: { position: 'inner' },
              },
              {
                name: 'Usuários por Nível',
                type: 'pie',
                radius: ['60%', '80%'],
                data: usersByLevel,
              },
            ],
          };
        }
      }
    } else {
      console.error('Erro: Dados inesperados na resposta.', response.data);
      card.content = 'Erro ao carregar conteúdo';
    }
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
    card.content = 'Erro ao carregar';
  } finally {
    card.loading = false; // Carregamento concluído
  }
};


const initializeCards = async () => {
  // Carregar todos os cards em paralelo
  const fetchPromises = cards.value.map(card => fetchCardContent(card));  
  // Aguarde que todas as promessas sejam resolvidas
  await Promise.all(fetchPromises);
};

onMounted(() => {
  fetchFixedCardsData();
  initializeCards();
});

const openCardDialog = () => {
  cardDialog.value = true;
};
const closeDialog = () => {
  cardDialog.value = false;
};

const removeCard = (cardId) => {
  if (confirm('Tem certeza que deseja deletar este Painel?')) {
   
    try {
      router.delete(route('cards.destroy', cardId));
      cards.value = cards.value.filter((card) => card.id !== cardId);
    } catch (error) {
      console.error('Erro ao deletar Painel:', error);
    }
  }
};

</script>

<style scoped>
.fixed-cards {
  margin-top: 10px;
}

.draggable-cards {
  margin-top: 20px;
}

.add-card-button {
  position: fixed;
  bottom: 70px;
  right: 16px;
}

.add-note-button {
  position: fixed;
  bottom: 16px;
  right: 16px;
}
</style>
