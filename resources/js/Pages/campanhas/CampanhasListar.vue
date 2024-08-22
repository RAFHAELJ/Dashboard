<script setup>
  import { ref, computed } from 'vue';
  import { Head, usePage, router } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import DataList from '@/Components/DataList.vue';
  import CampanhasForm from '@/Components/forms/UserForm.vue';
  
  const { props } = usePage();
  const campanhas = ref(props.campanhas || []);
  const isEditModalOpen = ref(false);
  const isEditing = ref(false);
  const editCampanhas = ref({
    id: null,
      name: '',      
      comeco:'',
      fim:'',
      publico:'',
      idade:'',
      tipo:'',
      video:'',
      capa:'',
      tempo:'',
      url:'',
      duracao:''
  });
  const search = ref('');
  
  const headers = [
    { text: 'ID', value: 'id', sortable: true,title:'ID',key:'id'}, 
    { text: 'Nome', value: 'name', sortable: true,title:'Nome',key:'name' },
    { text: 'Começo', value: 'comeco', sortable: true,title:'Começo',key:'comeco' },
    { text: 'Fim', value: 'fim', sortable: true,title:'Fim',key:'fim' },
    { text: 'Publico', value: 'publico', sortable: true,title:'Publico',key:'publico' },
    { text: 'Idade', value: 'idade', sortable: true,title:'Idade',key:'idade' },
    { text: 'Tipo', value: 'tipo', sortable: true,title:'Tipo',key:'tipo' },
    { text: 'Video', value: 'video', sortable: true,title:'Video',key:'video' },
    { text: 'Capa', value: 'capa', sortable: true,title:'Capa',key:'capa' },
    { text: 'Tempo', value: 'tempo', sortable: true,title:'Tempo',key:'tempo' },
    { text: 'Url', value: 'url', sortable: true,title:'Url',key:'url' },
    { text: 'Duração', value: 'duracao', sortable: true,title:'Duração',key:'duracao' },
    { text: 'Ações', value: 'actions', sortable: false },
  ];
  
  const deleteCampanhas = async (id) => {
    if (confirm('Tem certeza que deseja deletar este usuário?')) {
      try {
        await router.delete(route('campanhas.destroy', id));
        campanhas.value = campanhas.value.filter(campanhas => campanhas.id !== id);
      } catch (error) {
        console.error('Erro ao deletar usuário:', error);
      }
    }
  };
  
  const handleViewItem = (item) => {
    console.log('Visualizar item:', item);
  };
  
  const handleCreateItem = () => {
    isEditing.value = false;
    editCampanhas.value = {
      id: null,
      name: '',      
      comeco:'',
      fim:'',
      publico:'',
      idade:'',
      tipo:'',
      video:'',
      capa:'',
      tempo:'',
      url:'',
      duracao:''
    };
    isEditModalOpen.value = true;
  };
  
  const handleEditItem = (item) => {
    isEditing.value = true;
    editCampanhas.value = { ...item };
    isEditModalOpen.value = true;
  };
  
  const closeEditModal = () => {
    isEditModalOpen.value = false;
  };
  
  const handleDeleteItem = (item) => {
    deleteCampanhas(item.id);
  };
  </script>
  
  <template>
    <Head title="Campanhas" />
  
    <AuthenticatedLayout>
      <v-container fluid fill-height>
        <DataList
          :headers="headers"
          :items="campanhas"
          :columnTitles="['ID', 'Nome', 'Começo', 'Fim', 'Publico', 'Idade', 'Tipo', 'Video', 'Capa', 'Tempo', 'Url', 'Duração']"
          searchPlaceholder="Pesquisar Usuários Radio"
          createButtonLabel="Add Campanhas Radio"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
        />
        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
          <CampanhasForm
            v-if="isEditModalOpen"
            :campanhas="editCampanhas"
            :isEditing="isEditing"
            @cancel="closeEditModal"
          />
          
        </v-dialog>
      </v-container>
    </AuthenticatedLayout>
  </template>
  