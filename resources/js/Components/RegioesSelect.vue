<template>
  <v-select
    :items="regioes"
    :label="label"
    v-bind="attrs"
    v-model="localModelValue"
    :rules="rules"
    item-title="cidade" 
    item-value="id"
    density="compact"
      
    
  ></v-select>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useAttrs, defineEmits } from 'vue';  // Corrigido para usar defineEmits corretamente

// Definir as props recebidas
const props = defineProps({
  modelValue: [String, Number, Object],  // O valor inicial da seleção
  label: String,
  rules: Array,
});

// Usar os attrs (atributos extras passados no componente)
const attrs = useAttrs();
const emit = defineEmits(['update:modelValue']);  // Usar 'emit' corretamente

// Estado local para a lista de regiões e o valor selecionado
const regioes = ref([]);
const localModelValue = ref(props.modelValue);

// Buscar regiões da API
const fetchRegioes = async () => {
  try {
    const response = await fetch(route('regioes.index'), {
      headers: { 'Accept': 'application/json' },
    });

    if (response.ok) {
      const regioesData = await response.json();
      if (regioesData && regioesData.data) {
        regioes.value = regioesData.data; // Garante que a propriedade 'data' está presente

        // Verificar se já temos um valor de modelValue e inicializar o localModelValue corretamente
        if (props.modelValue && typeof props.modelValue === 'object') {
          localModelValue.value = props.modelValue.id;  // Se modelValue for um objeto, definimos o ID
        } else {
          localModelValue.value = props.modelValue;  // Caso contrário, o próprio valor é usado
        }

        // Emitir o valor para garantir que o pai tenha o ID correto, mesmo após o carregamento
        emit('update:modelValue', localModelValue.value);
      } else {
        console.error('Estrutura de resposta inesperada:', regioesData);
      }
    } else {
      console.error('Erro ao buscar Regiao:', response.statusText);
    }
  } catch (error) {
    console.error('Erro ao buscar Regiao:', error);
  }
};

// Sincronizar o valor recebido via props com o estado local do componente
watch(
  () => props.modelValue,
  (newValue) => {
    if (newValue && typeof newValue === 'object') {
      localModelValue.value = newValue.id; // Se for um objeto, definir o ID
    } else {
      localModelValue.value = newValue;
    }
  }
);

// Emitir o valor atualizado para o componente pai
watch(localModelValue, (newValue) => {
  if (newValue !== props.modelValue) {
    emit('update:modelValue', newValue);
  }
});

// Buscar regiões ao montar o componente
onMounted(() => {
  fetchRegioes();
});
</script>
