<template>
  <Head title="FAQ" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
    <v-app>
      <v-container class="editor-container">
        <v-row>
          <v-col cols="12">
            <h1>Editar FAQ</h1>
            <v-btn  v-if="canAccess('FAQ','gravar')" color="primary" @click="addNewFaq" class="mb-4">Adicionar Nova FAQ</v-btn>

            <!-- Seleção de FAQ por nome -->
            <v-select
              v-model="selectedFaq"
              :items="faqList"
              :hint="'Escolha um FAQ da lista'"
              item-title="nome"
              item-value="id"
              label="Selecione um FAQ"
              v-on:update:model-value="loadFaq"
            ></v-select>

            <!-- Campo para definir o nome do FAQ -->
            <v-text-field
              v-model="faqName"
              label="Nome do FAQ"
              class="mb-4"
              required
            ></v-text-field>

            <!-- Barra de ferramentas -->
            <v-row class="editor-toolbar">
              <v-btn icon @click="setBold"><v-icon>mdi-format-bold</v-icon></v-btn>
              <v-btn icon @click="setItalic"><v-icon>mdi-format-italic</v-icon></v-btn>
              <v-btn icon @click="setBlockquote"><v-icon>mdi-format-quote-close</v-icon></v-btn>
              <v-btn icon @click="setLink"><v-icon>mdi-link</v-icon></v-btn>
              <v-btn icon @click="setUnlink"><v-icon>mdi-link-off</v-icon></v-btn>
              <v-btn icon @click="undo"><v-icon>mdi-undo</v-icon></v-btn>
              <v-btn icon @click="redo"><v-icon>mdi-redo</v-icon></v-btn>
              <v-btn icon @click="setLeftAlign"><v-icon>mdi-format-align-left</v-icon></v-btn>
              <v-btn icon @click="setCenterAlign"><v-icon>mdi-format-align-center</v-icon></v-btn>
              <v-btn icon @click="setRightAlign"><v-icon>mdi-format-align-right</v-icon></v-btn>
              <v-btn icon @click="setJustifyAlign"><v-icon>mdi-format-align-justify</v-icon></v-btn>
              <v-btn icon @click="setOrderedList"><v-icon>mdi-format-list-numbered</v-icon></v-btn>
              <v-btn icon @click="addImage"><v-icon>mdi-image</v-icon></v-btn>
            </v-row>

            <!-- Editor de HTML com Tiptap -->
            <v-row>
              <v-col cols="12">
                <editor-content :editor="editor" class="editor-content"></editor-content>
              </v-col>
            </v-row>

            <v-btn v-if="canAccess('FAQ','atualizar')" color="primary" type="submit" class="mt-4" @click="saveFaq">Salvar</v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-app>
    </template>
  </AuthenticatedLayout>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { usePage, Head } from '@inertiajs/vue3';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import TextAlign from '@tiptap/extension-text-align';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Pega as props da página usando Inertia.js
const { props } = usePage();

const faq = reactive({ texto: props.faq?.texto || '' });
const faqName = ref(props.faq?.nome || '');
const faqList = ref(props.faqList || []);
const selectedFaq = ref(null);

// Inicializar o editor com estilo ajustado
const editor = useEditor({
  extensions: [
    StarterKit,
    Link,
    Image,
    TextAlign.configure({
      types: ['heading', 'paragraph'],
    }),
  ],
  content: faq.texto,
  editorProps: {
    attributes: {
      class: 'editor-content',
      style: 'min-height: 300px; padding: 10px; border: 1px solid #ddd; background-color: white;', // Estilo inline para definir altura e padding
    },
  },
  onUpdate: ({ editor }) => {
    faq.texto = editor.getHTML();
  },
});

// Funções para manipulação do editor
const setBold = () => {
  if (editor.value) {
    editor.value.chain().focus().toggleBold().run();
  }
};

const setItalic = () => {
  if (editor.value) {
    editor.value.chain().focus().toggleItalic().run();
  }
};

// Função para buscar o CSRF Token
const getCsrfToken = () => {
  const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
  if (!csrfTokenElement) throw new Error('CSRF token não encontrado');
  return csrfTokenElement.getAttribute('content');
};

// Função para carregar o FAQ
const loadFaq = async () => {
  if (!selectedFaq.value) return;

  try {
    const response = await fetch(route('faq.show', selectedFaq.value, true));
    if (!response.ok) throw new Error('Erro ao buscar os dados do FAQ');
    
    const data = await response.json();
    faq.texto = data.texto;
    faqName.value = data.nome;

    if (editor.value) {
      editor.value.commands.setContent(data.texto);
    }
  } catch (error) {
    console.error('Erro ao carregar o FAQ:', error);
  }
};

// Função para adicionar nova FAQ
const addNewFaq = () => {
  selectedFaq.value = null;
  faqName.value = '';
  faq.texto = '';
  
  if (editor.value) {
    editor.value.commands.clearContent();
  }
};

// Função para salvar o FAQ
const saveFaq = async () => {
  if (!faqName.value || !faq.texto) {
    alert('Por favor, preencha todos os campos.');
    return;
  }

  try {
    const csrfToken = getCsrfToken();
    const method = selectedFaq.value ? 'PUT' : 'POST';
    const url = selectedFaq.value
      ? route('faq.update', selectedFaq.value)
      : route('faq.store');
      
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({ nome: faqName.value, texto: faq.texto }),
    });

    if (response.ok) {
      alert('FAQ salvo com sucesso!');
    } else {
      alert('Erro ao salvar o FAQ.');
    }
  } catch (error) {
    console.error('Erro ao salvar FAQ:', error);
    alert('Erro ao salvar o FAQ.');
  }
};

// Carregar a lista de FAQs ao montar o componente
onMounted(() => {
  if (props.faqList) {
    faqList.value = [{ id: null, nome: 'Escolha um FAQ' }, ...props.faqList];
  }
});

</script>

<style scoped>
.editor-container {
  background-color: #f5f5f5;
  border: 1px solid #ccc;
  padding: 20px;
  border-radius: 8px;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.editor-content {
  min-height: 300px;
  border: 1px solid #ddd;
  padding: 10px;
  background-color: white;
  flex-grow: 1; /* O editor expande verticalmente */
}

.editor-toolbar {
  margin-bottom: 15px;
}
</style>
