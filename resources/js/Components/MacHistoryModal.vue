<template>
    <v-dialog v-model="isOpenLocal" persistent max-width="600px">
      <v-card>
        <v-card-title>Histórico de MACs</v-card-title>
        <v-card-text>
          <v-data-table :headers="headers" :items="macHistory" :items-per-page="5">
            <template v-slot:item.updated_at="{ item }">
              {{ new Date(item.updated_at).toLocaleString() }}
            </template>
          </v-data-table>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" @click="closeModal">Fechar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    isOpen: Boolean,
    macHistory: Array,
  });
  
  const emit = defineEmits(['update:isOpen']);
  
  const isOpenLocal = ref(props.isOpen);
  
  watch(() => props.isOpen, (newVal) => {
    isOpenLocal.value = newVal;
  });
  
  watch(isOpenLocal, (newVal) => {
    emit('update:isOpen', newVal);
  });
  
  const headers = [
    { text: 'MAC Antigo', value: 'mac_antigo' },
    { text: 'MAC Novo', value: 'mac_novo' },
    { text: 'Data de Atualização', value: 'updated_at' },
  ];
  
  const closeModal = () => {
    isOpenLocal.value = false;
  };
  </script>
  