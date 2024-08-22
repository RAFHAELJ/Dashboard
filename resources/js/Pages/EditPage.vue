<template>
    <div>
      <Head title="Editor Visual" />
  
      <v-container>
        <v-row>
          <v-col cols="12">
            <v-card>
              <v-card-title>
                Editor Visual de HTML
              </v-card-title>
              <v-card-text>
                <div id="editor" style="height: 600px;"></div>
              </v-card-text>
              <v-card-actions>
                <v-btn @click="saveChanges" color="primary">Salvar</v-btn>
                <v-btn @click="addComponent('text')" color="secondary">Adicionar Texto</v-btn>
                <v-btn @click="addComponent('image')" color="secondary">Adicionar Imagem</v-btn>
                <v-btn @click="addComponent('button')" color="secondary">Adicionar Botão</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import grapesjs from 'grapesjs';
  import { Head } from '@inertiajs/vue3';
  
  const editor = ref(null);
  
  onMounted(() => {
    editor.value = grapesjs.init({
      container: '#editor',
      fromElement: true,
      height: '100%',
      width: 'auto',
      storageManager: false,
      panels: { defaults: [] },
      blockManager: {
        blocks: [
          {
            id: 'text-block',
            label: 'Texto',
            content: '<div class="text-block">Texto</div>',
          },
          {
            id: 'image-block',
            label: 'Imagem',
            content: '<img src="https://via.placeholder.com/150" class="image-block" />',
          },
          {
            id: 'button-block',
            label: 'Botão',
            content: '<button class="button-block">Clique Aqui</button>',
          },
        ],
      },
      styleManager: {
        sectors: [{
          name: 'General',
          buildProps: ['width', 'height', 'float', 'display'],
          properties: [
            {
              name: 'Width',
              property: 'width',
              type: 'integer',
              units: ['px', '%'],
            },
            {
              name: 'Height',
              property: 'height',
              type: 'integer',
              units: ['px', '%'],
            },
          ],
        }],
      },
      selectorManager: {
        componentFirst: true,
      },
    });
  });
  
  const saveChanges = async () => {
    const html = editor.value.getHtml();
    const css = editor.value.getCss();
    
    try {
      const response = await fetch(route('save.page'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ html, css }),
      });
  
      if (!response.ok) {
        throw new Error('Falha ao salvar');
      }
  
      alert('Alterações salvas com sucesso!');
    } catch (error) {
      console.error('Erro:', error);
      alert('Erro ao salvar alterações.');
    }
  };
  
  const addComponent = (type) => {
    const components = {
      text: '<div class="text-block">Texto</div>',
      image: '<img src="https://via.placeholder.com/150" class="image-block" />',
      button: '<button class="button-block">Clique Aqui</button>',
    };
    
    if (components[type]) {
      editor.value.addComponents(components[type]);
    }
  };
  </script>
  
  <style scoped>
  #editor {
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  .text-block {
    padding: 20px;
    border: 1px solid #ddd;
  }
  
  .image-block {
    max-width: 100%;
  }
  
  .button-block {
    padding: 10px 20px;
    border: none;
    background-color: #007bff;
    color: white;
    cursor: pointer;
  }
  
  .text-block, .button-block {
    margin: 10px;
  }
  </style>
  