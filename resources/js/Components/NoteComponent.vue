<template>
    <v-col :key="note.id" cols="12" sm="2" md="2">
      <v-sheet
        class="note-sheet"
        :style="{ backgroundColor: selectedColor || '#f5f5f5' }"
        outlined
        max-width="400"
        min-height="150"
      >
        <v-card-title>
          <span>Nota {{ note.id }}</span>
          <v-spacer></v-spacer>
          <v-btn icon @click="$emit('remove', note.id)" class="close-btn">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-textarea 
           v-model="note.content"
           rows="6"
           class="transparent-textarea"
           hide-details
           >
        </v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-menu
            offset-y
            :close-on-content-click="false"
          >
            <template #activator="{ props }">
              <v-btn v-bind="props" icon>
                <v-icon>mdi-palette</v-icon>
              </v-btn>
            </template>
            <v-color-picker v-model="selectedColor" hide-inputs />
          </v-menu>
        </v-card-actions>
      </v-sheet>
    </v-col>
  </template>
  
  <script setup>
  import { defineProps, ref, watch } from 'vue';
  
  const props = defineProps({
    note: Object,
  });
  
  const selectedColor = ref(props.note.color || '#f5f5f5');
  
  watch(() => props.note.color, (newColor) => {
    selectedColor.value = newColor;
  });
  
  const emitUpdateColor = (color) => {
    props.note.color = color;
  };
  </script>
  
  <style scoped>
  .note-sheet {
    border-radius: 8px;
    box-shadow: none;
    margin-top: 10px;
    padding: 0;
  }
  
  .close-btn {
    color: #aaa;
    margin-left: auto;
    width: 25px;
    height: 25px;
  }
  
  .close-btn:hover {
    color: #333;
  }
  .transparent-textarea {
  background: transparent;
  border: none;
  box-shadow: none;
  padding: 0;
  resize: none;
}
  </style>
  