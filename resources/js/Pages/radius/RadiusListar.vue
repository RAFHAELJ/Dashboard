<script setup>
  import { ref, computed } from 'vue';
  import { Head, usePage, router } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import DataList from '@/Components/DataList.vue';
  import RadiusForm from '@/Components/forms/UserForm.vue';
  
  const { props } = usePage();
  const nas = ref(props.radius || []);
  const isEditModalOpen = ref(false);
  const isEditing = ref(false);
  const editradius = ref({
    id: null,
      nasname: '', 
      shortname: '',     
      type:'other',
      ports:'',
      secret:'',      
      community:'',
      description:'',
     
  });
  
  const search = ref('');
  
  const headers = [
    { text: 'ID', value: 'id', sortable: true,title:'ID',key:'id'}, 
    { text: 'Nome do NAS', value: 'nasname', sortable: true,title:'Nome do NAS',key:'nasname' },
    { text: 'Nome curto', value: 'shortname', sortable: true,title:'Nome curto',key:'shortname' },
    { text: 'Tipo', value: 'type', sortable: true,title:'Tipo',key:'type' },
    { text: 'Porta', value: 'ports', sortable: true,title:'Porta',key:'ports' },
    { text: 'Senha', value: 'secret', sortable: true,title:'Senha',key:'secret' },    
    { text: 'Comunidade', value: 'community', sortable: true,title:'Comunidade',key:'community' },
    { text: 'Descrição', value: 'description', sortable: true,title:'Descrição',key:'description' },    
    { text: 'Ações', value: 'actions', sortable: false },
  ];
  
  const deleteradius = async (id) => {
    if (confirm('Tem certeza que deseja deletar este radio?')) {
      try {
        
        
        await router.delete(route('nas.destroy', id));
        nas.value = nas.value.filter(nas => nas.id !== id);
      } catch (error) {
        console.error('Erro ao deletar radio:', error);
      }
    }
  };
  const fetchPage = (page) => {  
  router.get(route('nas.index', { page }), {
    preserveState: true, // Mantém o estado da página
    onSuccess: (page) => {
      nas.value = page.props.nas;
    },
  });
};
  
  const handleCreateItem = () => {
    isEditing.value = false;
    editradius.value = {
      id: null,
      nasname: '', 
      shortname: '',     
      type:'other',
      ports:'',
      secret:'',      
      community:'',
      description:'',
     
      
    };
    isEditModalOpen.value = true;
  };
  
  const handleEditItem = (item) => {
    isEditing.value = true;
    editradius.value = { ...item };
    isEditModalOpen.value = true;
  };
  
  const closeEditModal = () => {
    isEditModalOpen.value = false;
  };
  
  const handleDeleteItem = (item) => {
    deleteradius(item.id);
  };
  </script>
  
  <template>
    <Head title="nas" />
  
    <AuthenticatedLayout>
      <template v-slot="{ canAccess }">
      <v-container fluid fill-height>
        <DataList
          :headers="headers"
          :items="nas"
          :columnTitles="['radio','mac']"
          searchPlaceholder="Pesquisar Usuários Radio"
          createButtonLabel="Add nas Radio"
          @create="handleCreateItem"
          @edit="handleEditItem"
          @delete="handleDeleteItem"
          :item-key="'id'"
          :canAccess="canAccess" 
          createRoute="nas"
          @page-changed="fetchPage"
        />
        <v-dialog v-model="isEditModalOpen" persistent max-width="600px">
        <RadiusForm
          :showCreateRegiao="false"
          :formData="editradius"
          :fields="{
            nasname: { label: 'IP/Mascara 0.0.0.0/0', rules: [(v) => !!v || ' é obrigatório'], required: true ,mask: '###.###.###.###/##'},
            shortname: { label: 'Nome curto nas', rules: [(v) => !!v || ' é obrigatório'], required: true, type: 'text'  },
            type: { label: 'Type', rules: [(v) => !!v || ' é obrigatório'], required: true, type: 'text' },
            ports: { label: 'Porta', rules: [(v) => !!v || ' é obrigatório'], required: true, type: 'text' },            
            secret: { label: 'secret', rules: [(v) => !!v || 'Endereço é obrigatório'], required: true },
            community: { label: 'comunidade', rules: [(v) => !!v || 'Info é obrigatório'], required: true},
            description: { label: 'Info', rules: [(v) => !!v || 'Info é obrigatório'], required: true},     
             
          }"
          :isEditing="isEditing"
          title="Nas"
          createRoute="nas.store"
          updateRoute="nas.update"
          returnRoute="nas.index"
          @cancel="closeEditModal"
        />
      </v-dialog>
      </v-container>
      </template> 
    </AuthenticatedLayout>
  </template>
  