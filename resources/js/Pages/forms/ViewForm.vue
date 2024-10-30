<script setup>
import { ref } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormViewer from '@/Components/FormViewer.vue';
import FormList from '@/Components/FormList.vue';

// Obtém os dados dos formulários vindos das props
const { props } = usePage();

// Converte o campo 'questions' de string para array de objetos
const forms = ref(
  props.forms.map(form => ({
    ...form,
    questions: JSON.parse(form.questions)
  }))
);

// Estado do modal de criação/edição de formulário
const isFormModalOpen = ref(false);
const isEditing = ref(false); // Controla se está no modo de edição

// Dados do formulário em edição
const editForm = ref({
  id: null,
  title: '',
  questions: []
});

// Abre o modal para criação de um novo formulário
const handleCreateForm = () => {
  isEditing.value = false;
  editForm.value = { title: '', questions: [] };
  isFormModalOpen.value = true;
};

// Abre o modal para edição de um formulário
const handleEditForm = (form) => {
  isEditing.value = true;
  editForm.value = { ...form };
  isFormModalOpen.value = true;
};

// Salva o formulário (criação/edição)
const saveForm = async (formData) => {
  try {
    const formToSave = {
      ...formData,
      questions: formData.questions // Mantém como array
    };

    if (isEditing.value && editForm.value.id) {
      // Atualiza o formulário existente
      await router.put(route('forms.update', editForm.value.id), formToSave);
      forms.value = forms.value.map(form => 
        form.id === editForm.value.id ? { ...form, ...formData } : form
      );
    } else {
      // Cria um novo formulário
      const response = await router.post(route('forms.store'), formToSave);
      forms.value.push({
        ...formData,
        id: response.data.id, // Usando o ID retornado da resposta
        questions: formData.questions
      });
    }

    // Fecha o modal após salvar
    isFormModalOpen.value = false;
  } catch (error) {
    console.error('Erro ao salvar o formulário:', error);
  }
};

// Exclui um formulário
const handleDeleteForm = async (formId) => {
  if (confirm('Tem certeza que deseja excluir este formulário?')) {
    try {
      await router.delete(route('forms.destroy', formId));
      forms.value = forms.value.filter(form => form.id !== formId);
    } catch (error) {
      console.error('Erro ao excluir o formulário:', error);
    }
  }
};
</script>

<template>
  <Head title="Listagem de Formulários" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container fluid>
        <!-- Cabeçalho da página -->
        <v-row class="mb-4">
          <v-col>
            <h1 class="text-h4">Listagem de Formulários</h1>
          </v-col>
          <v-col class="d-flex justify-end">
            <v-btn
              color="primary"
              @click="handleCreateForm"
              v-if="canAccess('forms.create', 'gravar')"
            >
              <v-icon left>mdi-plus</v-icon>
              Adicionar Novo Formulário
            </v-btn>
          </v-col>
        </v-row>

        <!-- Componente de visualização de formulários -->
        <FormViewer 
          :forms="forms" 
          @edit="handleEditForm" 
          @delete="handleDeleteForm" 
        />

        <!-- Modal para criação/edição de novo formulário -->
        <v-dialog v-model="isFormModalOpen" max-width="800px" persistent>
          <FormList
            :form="editForm"
            @save="saveForm"
            @cancel="() => (isFormModalOpen.value = false)"
          />
        </v-dialog>
      </v-container>
    </template>
  </AuthenticatedLayout>
</template>

<style scoped>
.mb-4 {
  margin-bottom: 16px;
}
.text-h4 {
  font-size: 24px;
  font-weight: bold;
}
</style>
