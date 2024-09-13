<template>
    <v-select
      :items="regioes"
      :label="label"
      v-bind="attrs"
      v-model="localModelValue"
      :rules="rules"
       item-title="cidade" 
          item-value="id"
    ></v-select>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue';
  import { useAttrs } from 'vue';  // Captura os attrs adicionais que podem ser passados
  
  // Definir as props recebidas
  const props = defineProps({
    modelValue: [String, Number,Object],  // O valor inicial da seleção
    label: String,
    rules: Array,
  });
  
  // Usar os attrs (atributos extras passados no componente)
  const attrs = useAttrs();
  
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
      localModelValue.value = newValue;
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
  