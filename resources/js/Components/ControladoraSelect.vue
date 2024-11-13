<template>
    <v-select
      :items="controladoras"
      :label="label"
      v-bind="attrs"
      v-model="localModelValue"
      :rules="rules"
      item-title="nome" 
      item-value="id"
     
      
    ></v-select>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue';
  import { useAttrs, defineEmits } from 'vue';
  
  // Definir as props recebidas
  const props = defineProps({
    modelValue: [String, Number, Object],
    label: String,
    rules: Array,
  });
  
  // Usar os attrs (atributos extras passados no componente)
  const attrs = useAttrs();
  const emit = defineEmits(['update:modelValue']);
  
  // Estado local para a lista de controladoras e o valor selecionado
  const controladoras = ref([]);
  const localModelValue = ref(props.modelValue);
  
  // Buscar controladoras da API
  const fetchControladoras = async () => {
    try {
      const response = await axios.get('/controladora');
      if (response.data && response.data.data) {
        controladoras.value = response.data.data;
  
        // Verificar se já temos um valor de modelValue e inicializar o localModelValue corretamente
        if (props.modelValue && typeof props.modelValue === 'object') {
          localModelValue.value = props.modelValue.id;
        } else {
          localModelValue.value = props.modelValue;
        }
  
        // Emitir o valor para garantir que o pai tenha o ID correto, mesmo após o carregamento
        emit('update:modelValue', localModelValue.value);
      } else {
        console.error('Estrutura de resposta inesperada:', response.data);
      }
    } catch (error) {
      console.error('Erro ao buscar controladoras:', error);
    }
  };
  
  // Sincronizar o valor recebido via props com o estado local do componente
  watch(
    () => props.modelValue,
    (newValue) => {
      if (newValue && typeof newValue === 'object') {
        localModelValue.value = newValue.id;
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
  
  // Buscar controladoras ao montar o componente
  onMounted(() => {
    fetchControladoras();
  });
  </script>
  