<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormList from '@/Components/FormList.vue';
import FormBuilder from '@/Components/FormBuilder.vue';

const { props } = usePage();
const forms = ref(props.forms || []);
const isFormModalOpen = ref(false);
const isEditing = ref(false);
const editForm = ref({
  title: '',
  questions: []
});

// Cabeçalhos da tabela
const headers = [
  { text: 'ID', value: 'id', sortable: true },
  { text: 'Título', value: 'title', sortable: true },
  { text: 'Ações', value: 'actions', sortable: false }
];

// Abre o modal para criar um novo formulário
const handleCreateForm = () => {
  isEditing.value = false;
  editForm.value = { title: '', questions: [] };
  isFormModalOpen.value = true;
};

// Abre o modal para editar um formulário existente
const handleEditForm = (form) => {
  isEditing.value = true;
  editForm.value = { ...form };
  isFormModalOpen.value = true;
};

// Redireciona para a visualização do formulário
const handleViewForm = (form) => {
  router.get(route('forms.show', form.id));
};

// Deleta um formulário
const handleDeleteForm = async (formId) => {
  if (confirm('Tem certeza que deseja deletar este formulário?')) {
    try {
      await router.delete(route('forms.destroy', formId));
      forms.value = forms.value.filter(form => form.id !== formId);
    } catch (error) {
      console.error('Erro ao deletar o formulário:', error);
    }
  }
};

// Salva o formulário (criação/edição)
const saveForm = async (formData) => {
  try {
    if (isEditing.value && editForm.value.id) {
      await router.put(route('forms.update', editForm.value.id), formData);
    } else {
      await router.post(route('forms.store'), formData);
    }
    isFormModalOpen.value = false;
    await fetchForms();
  } catch (error) {
    console.error('Erro ao salvar o formulário:', error);
  }
};

// Busca os formulários do backend
const fetchForms = async () => {
  try {
    const response = await router.get(route('forms.index'));
    if (response && response.data) {
      forms.value = response.data;
    }
  } catch (error) {
    console.error('Erro ao buscar formulários:', error);
  }
};

// Carrega os formulários na inicialização
/*onMounted(() => {
  fetchForms();
});*/
</script>

<template>
  <Head title="Lista de Formulários" />

  <AuthenticatedLayout>
    <template v-slot="{ canAccess }">
      <v-container fluid fill-height>
        <FormList
          :headers="headers"
          :items="forms"
          @create="handleCreateForm"
          @edit="handleEditForm"
          @view="handleViewForm"
          @delete="handleDeleteForm"
          createRoute="forms"
        />

        <v-dialog v-model="isFormModalOpen" max-width="800px">
          <FormBuilder
            :form="editForm"
            @save="saveForm"
            @cancel="() => (isFormModalOpen.value = false)"
          />
        </v-dialog>
      </v-container>
    </template>
  </AuthenticatedLayout>
</template>
