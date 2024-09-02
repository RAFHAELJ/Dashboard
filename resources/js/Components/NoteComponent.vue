<template>
  <v-col :key="note.id" cols="12" sm="2" md="2">
    <v-sheet
      class="note-sheet"
      :style="{ backgroundColor: selectedColor || '#f5f5f5' }"
      outlined
      max-width="400"
      min-height="150"
      @mousedown="startDragging"
      @mouseup="stopDragging"
      @mousemove="onDragging"
    >
      <v-card-title>
        <span>Nota {{ note.id }}</span>
        <v-spacer></v-spacer>
        <v-btn icon @click="removeNote" class="close-btn">
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
          <v-color-picker v-model="selectedColor" hide-inputs @input="updateColor" />
        </v-menu>
      </v-card-actions>
    </v-sheet>
  </v-col>
</template>

<script setup>
import { defineProps, ref, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  note: Object,
});

const selectedColor = ref(props.note.color || '#f5f5f5');

watch(() => selectedColor.value, (newColor) => {
  emitUpdateColor(newColor);
});

const createNote = async () => {
  try {
    const response = await axios.post('/notes', {
      color: '#fffff',
      
    });

    // Atualizar a nota com o ID gerado pelo servidor
    note.id = response.data.id;
  } catch (error) {
    console.error('Erro ao criar nota:', error);
  }
};

const updateColor = (color) => {
  selectedColor.value = color;
  emitUpdateColor(color);
};

const emitUpdateColor = (color) => {
  axios.put(`/notes/${props.note.id}`, { color });
};

const removeNote = async () => {
  if (confirm('Tem certeza que deseja deletar esta nota?')) {
    try {
      await axios.delete(`/notes/${props.note.id}`);
      // Emitir evento para remoção da lista de notas pai
      emit('remove', props.note.id);
    } catch (error) {
      console.error('Erro ao deletar nota:', error);
    }
  }
};

// Lógica de arrastar
let dragging = false;
const startDragging = (event) => {
  dragging = true;
  document.addEventListener('mousemove', onDragging);
  document.addEventListener('mouseup', stopDragging);
};

const stopDragging = () => {
  dragging = false;
  document.removeEventListener('mousemove', onDragging);
  document.removeEventListener('mouseup', stopDragging);
  // Atualizar posição no backend
  axios.put(`/notes/${props.note.id}`, {
    position_x: notePosition.x,
    position_y: notePosition.y,
  });
};

const onDragging = (event) => {
  if (dragging) {
    // Atualizar a posição da nota
    notePosition.x = event.clientX;
    notePosition.y = event.clientY;
  }
};

const notePosition = ref({ x: props.note.position_x, y: props.note.position_y });

// Chama a função de criar a nota se o ID não estiver definido (nova nota)
onMounted(() => {
  console.log('Criando nova nota...');
  
    if(!props.note.id) {
      createNote();
    }
   
  
});
</script>

<style scoped>
.note-sheet {
  border-radius: 8px;
  box-shadow: none;
  margin-top: 10px;
  padding: 0;
  position: absolute;
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
