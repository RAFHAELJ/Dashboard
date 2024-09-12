<script setup>
  import { ref, computed } from 'vue';
  import { Head, usePage, router } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import DataList from '@/Components/DataList.vue';
  import UserForm from '@/Components/forms/UserForm.vue';
  
  const { props } = usePage();
  const radios = ref(props.radios || []);
  const isEditModalOpen = ref(false);
  const isEditing = ref(false);
  const editRadios = ref({
    id: null,
      radio: '',      
      mac:'',
      geo:'',
      endereco:'',
      info:'',
     
  });
  
  const search = ref('');
  
  const headers = [
    { text: 'ID', value: 'id', sortable: true,title:'ID',key:'id'}, 
    { text: 'Radio', value: 'radio', sortable: true,title:'Radio',key:'radio' },
    { text: 'Mac', value: 'mac', sortable: true,title:'Mac',key:'mac' },
    { text: 'Geo', value: 'geo', sortable: true,title:'Geo',key:'geo' },
    { text: 'Endereço', value: 'endereco', sortable: true,title:'Endereço',key:'endereco' },
    { text: 'Info', value: 'info', sortable: true,title:'Info',key:'info' },
    { text: 'Regiao', value: 'regiao', sortable: true,title:'Regiao',key:'regiao' },
    { text: 'Ações', value: 'actions', sortable: false },
  ];
  
  const deleteRadios = async (id) => {
    if (confirm('Tem certeza que deseja deletar este radio?')) {
      try {
        
        
        await router.delete(route('radios.destroy', id));
        radios.value = radios.value.filter(radios => radios.id !== id);
      } catch (error) {
        console.error('Erro ao deletar radio:', error);
      }
    }
  };
  
  const handleViewItem = (item) => {
    console.log('Visualizar item:', item);
  };
  
  const handleCreateItem = () => {
    isEditing.value = false;
    editRadios.value = {
      id: null,
      radio: '',      
      mac:'',
      geo:'',
      info:'',
      regiao:''
      
    };
    isEditModalOpen.value = true;
  };
  
  const handleEditItem = (item) => {
    isEditing.value = true;
    editRadios.value = { ...item };
    isEditModalOpen.value = true;
  };
  
  const closeEditModal = () => {
    isEditModalOpen.value = false;
  };
  
  const handleDeleteItem = (item) => {
    deleteRadios(item.id);
  };
  </script>
  
  <template>
    <Head title="Radios" />
  
    <AuthenticatedLayout>
      <v-container fluid fill-height>
        <DataList
          :headers="headers"
          :items="radios"
          :columnTitles="['radio','mac']"
          searchPlaceholder="Pesquisar Usuários Radio"
          createButtonLabel="Add Radios Radio"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
        />
        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
        <UserForm
          :showCreateRegiao="true"
          :formData="editUser"
          :fields="{
            radio: { label: 'Nome Radio', rules: [(v) => !!v || 'RADIO é obrigatório'], required: true },
            mac: { label: 'Mac adress', rules: [(v) => !!v || 'Mac é obrigatório'], required: true, type: 'text' },
            geo: { label: 'GeoLocalização', rules: [(v) => !!v || 'Geo é obrigatório'], required: true, type: 'text' },
            endereco: { label: 'Endereço', rules: [(v) => !!v || 'Endereço é obrigatório'], required: true },
            info: { label: 'Info', rules: [(v) => !!v || 'Info é obrigatório'], required: true},    
             
          }"
          :isEditing="isEditing"
          title="Radios Radio"
          createRoute="radios.store"
          updateRoute="radios.update"
          returnRoute="radios.index"
          @cancel="closeEditModal"
        />
      </v-dialog>
      </v-container>
    </AuthenticatedLayout>
  </template>
  