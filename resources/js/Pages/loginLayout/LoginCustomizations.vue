<script setup>
import { ref, computed, onMounted } from 'vue';
import interact from 'interactjs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { mask } from 'vue-the-mask'; 
import axios from 'axios';
import { usePage,router } from '@inertiajs/vue3';
import RegioesSelect from '@/Components/RegioesSelect.vue';
import { color } from 'zrender';

const topImageFormData = ref(null);
const backgroundImageFormData = ref(null);
const errorMessage = ref(null); 
const { props } = usePage();
const previewElements = ref([]); // Inicializar os elementos de preview
const topCardHeight = ref(150);
const topCardWidth = ref(400);
const topCardTop = ref(0);    // Posição inicial top
const topCardLeft = ref(0);   // Posição inicial left
const fontSize = ref(16);
const textColor = ref('#000000');
const textColorButon = ref('#000000');
const isBold = ref(false);
const isItalic = ref(false);
const fontWeight = computed(() => isBold.value ? 'bold' : 'normal');
const fontStyle = computed(() => isItalic.value ? 'italic' : 'normal');
const showTopColorPicker = ref(false);
const showBackgroundColorPicker = ref(false);
const showLoginButtonColorPicker = ref(false);
const showInputColorPicker = ref(false);
const showTextColorPicker = ref(false);
const showTextColorButtonPicker = ref(false);
const previewTopImage = ref(null);
const backgroundImage = ref(null);
const textBoxWidth = ref(200); // Largura inicial da caixa de texto
const textBoxHeight = ref(100); // Altura inicial da caixa de text
const imageTamanho = ref(50); 
const customization = props.customization; 
const directives = {
  mask,  // Registrando a diretiva v-mask
};
const isEditMode = ref(!!customization);


const handleCreateAccount = () => {
  console.log("Botão 'Criar nova conta' foi clicado.");
  // Aqui você pode adicionar lógica para a criação de uma nova conta
};

const form = ref({
  layout_name: '',
  top_type:  'Cor',
  top_value:  '#ffffff',
  background_type:  'Cor',
  background_value:  '#ffffff',
  login_button_text:  'Login',
  login_button_color:  '#1976d2',
  login_button_shape: 10,
  button_elevation:  2,
  button_opacity: 1,
  input_width:  150,
  input_height: 25,
  input_color:  '#f0f0f0',
  input_shape:  5,
  input_elevation:1,
  input_opacity:  1,
  previewTopImage: null,
  caditens: {
    nome: false,
    usuario: false,
    email: false,
    senha: false,
    cpf: false,
    telefone: false,
    nascimento: false,
    sexo: false,
  },
  types: {
    conta: false,  // Será ajustado com base nos elementos abaixo
    rede_social: false,
    login_click: false,
  },
  login_method:  [],
  login_password_method: [],
  selected_social_networks:  [],
  editableTextContent: 'Digite Aqui seu texto',
  elements: [],
  
});

const socialNetworks = ref([
  { name: 'Facebook', on: false, key: '', secret: '', url: '' },
  { name: 'Twitter', on: false, key: '', secret: '', url: '' },
  { name: 'Instagram', on: false, key: '', secret: '', url: '' },
  { name: 'Google', on: false, key: '', secret: '', url: '' },
]);

const showInsertItemsModal = ref(false);
const showSocialModal = ref(false);


// Função para carregar os dados da base de dados no form
const loadCustomizationData = (customizationData) => {
  form.value.id = customizationData.id;
  form.value.layout_name = customizationData.layout_name;
  form.value.top_type = customizationData.top_type;
  form.value.top_value = customizationData.top_value;
  form.value.background_type = customizationData.background_type;
  form.value.background_value = customizationData.background_value;
  form.value.login_button_text = customizationData.login_button_text;
  form.value.login_button_color = customizationData.login_button_color;
  form.value.backgroundImage = customizationData.background_image;
  form.value.regiao = customizationData.region;

  // Converter login_method de string JSON para array (se não for nulo)
  form.value.login_method = customizationData.login_method ? JSON.parse(customizationData.login_method) : [];

  // Converter login_password_method de string JSON para array (se não for nulo)
  //console.log('login_password_method:', customizationData.login_password_method);
  form.value.login_password_method = 
  typeof customizationData.login_password_method === 'string' 
    ? JSON.parse(customizationData.login_password_method) 
    : customizationData.login_password_method || [];

  // Converter caditens de string JSON para objeto (se não for nulo)
  if (customizationData.caditens) {
    const parsedCaditens = JSON.parse(customizationData.caditens);
    // Convertendo os valores de string para boolean
    form.value.caditens = Object.keys(parsedCaditens).reduce((acc, key) => {
      acc[key] = parsedCaditens[key] === "true";
      return acc;
    }, {});
  } else {
    form.value.caditens = {
      nome: false,
      usuario: false,
      email: false,
      senha: false,
      cpf: false,
      telefone: false,
      nascimento: false,
      sexo: false,
    };
  }

  // Converter social_networks de string JSON para array (se não for nulo)
  if (customizationData.social_networks) {
    const parsedSocialNetworks = JSON.parse(customizationData.social_networks);
    // Atualizar socialNetworks.value com os dados vindos do banco
    socialNetworks.value = parsedSocialNetworks.map(network => ({
      name: network.name,
      on: network.on === "true", // Convertendo para boolean
      key: network.key || '',
      secret: network.secret || '',
      url: network.url || '',
    }));
  }

  // Atualizar as variáveis globais de ajuste do form com base nos valores dos elementos
  if (customizationData.elements) {
    const parsedElements = JSON.parse(customizationData.elements);

    parsedElements.forEach(element => {
      if (element.type === 'input' || element.type === 'inputPassword') {
        form.value.input_width = element.width || form.value.input_width;
        form.value.input_height = element.height || form.value.input_height;
        form.value.input_color = element.backgroundColor || form.value.input_color;
        form.value.input_shape = element.shape || form.value.input_shape;
        form.value.input_elevation = element.elevation || form.value.input_elevation;
        form.value.input_opacity = element.opacity || form.value.input_opacity;
      }

      if (element.type === 'button' || element.type === 'buttonC' || element.type === 'buttonA') {
        form.value.login_button_shape = element.shape || form.value.login_button_shape;
        form.value.button_elevation = element.elevation || form.value.button_elevation;
        form.value.button_opacity = element.opacity || form.value.button_opacity;
        form.value.background = element.backgroundColor || form.value.background;
        form.value.textColorButon = element.color || form.value.textColorButon;
      }

      if (element.type === 'text') {
        form.value.textColor = element.color || form.value.textColor;
      }
    });

    // Popula o form.value.elements para manter a estrutura de elementos
    form.value.elements = parsedElements.map(element => {
      const baseElement = {
        id: element.id,
        type: element.type,
        top: element.top,
        left: element.left,
        width: element.width,
        height: element.height,
        opacity: element.opacity || 1,
        elevation: element.elevation || 0,
        backgroundColor: element.backgroundColor || form.value.input_color,
        previewTopImage: element.image || null
      };

      if (element.type === 'input' || element.type === 'inputPassword') {
        return {
          ...baseElement,
          text: element.text || null,
          color: element.color || form.value.input_color,
          shape: element.shape
        };
      }

      if (element.type === 'button' || element.type === 'buttonC' || element.type === 'buttonA') {
        return {
          ...baseElement,
          text: element.text || form.value.login_button_text,
          shape: element.shape,
          color: element.color || form.value.textColorButon
        };
      }

      if (element.type === 'topCard') {
        return {
          ...baseElement,
          image: element.image || null
        };
      }

      if (element.type === 'text') {
        return {
          ...baseElement,
          text: element.text || 'Texto padrão',
          color: element.color || form.value.textColor
        };
      }

      return baseElement;
    });
  }
};



const resolveImageSource = (element) => {
  // Verifica se existe uma imagem base64 (geralmente para pré-visualização)
  if (previewTopImage.value) {
    return previewTopImage.value;
  }
 
  // Se não houver base64, utiliza o caminho da imagem salva no banco de dados
  if (element) {
    return element.startsWith('data:image') ? element : `/storage/${element}`;
  }

  // Caso nenhum dos dois esteja disponível, retorna uma imagem padrão ou null
  return null;
};




// Carregar os elementos do customization para o preview
const loadElements = () => {
  if (customization && customization.elements) {
    loadCustomizationData(customization);  
    // Parsea o array de elementos armazenado como string
    previewElements.value = JSON.parse(customization.elements);
    // Atualiza os checkboxes com base nos elementos carregados
    form.value.types.conta = !!previewElements.value.find(el => el.type === 'input' || el.type === 'inputPassword');
    form.value.types.rede_social = !!previewElements.value.find(el => el.type === 'social');
    form.value.types.login_click = !!previewElements.value.find(el => el.type === 'button');
  }
};


const updateEditableText = (event, elementId) => {
  const element = previewElements.value.find(el => el.id === elementId);
  if (element) {
    element.text = event.target.innerText;
  }
};

const handleLoginMethodChange = () => {
  
  // Remover elementos de login que não estão mais selecionados
  previewElements.value = previewElements.value.filter(el => !(el.type === 'input' && !form.value.login_method.includes(el.label)));

  // Adicionar novos elementos de login
  form.value.login_method.forEach(method => {
    const existingElement = previewElements.value.find(el => el.label === method);
    if (!existingElement) {
      previewElements.value.push({
        id: previewElements.value.length + 1,
        type: 'input',
        label: method,
        top: 100 + previewElements.value.length * 50,
        left: 50,
        width: form.value.input_width,
        height: form.value.input_height,
        color: form.value.input_color,
        shape: form.value.input_shape,
        opacity: form.value.input_opacity,
      });
    }
  });
};



const handlePasswordMethodChange = () => {
 
  // Remover elementos de senha que não estão mais selecionados
  previewElements.value = previewElements.value.filter(el => !(el.type === 'input' && !form.value.login_password_method.includes(el.label)));

  // Adicionar novos elementos de senha
  form.value.login_password_method.forEach(method => {
    const existingElement = previewElements.value.find(el => el.label === method );
    if (!existingElement) {
      previewElements.value.push({
        id: previewElements.value.length + 1,
        type: 'input',
        label: method,
        top: previewElements.value.length * 50,
        left: 50,
        width: form.value.input_width,
        height: form.value.input_height,
        color: form.value.input_color,
        shape: form.value.input_shape,
        opacity: form.value.input_opacity,
      });
    }
  });
};


const handleTopImageUpload = (event) => {  
  const file = event.target.files[0];

  if (file) {
    // Pré-visualização da imagem usando base64
    const reader = new FileReader();
    reader.onload = (e) => {
      previewTopImage.value = e.target.result;  // Carrega a imagem como base64 para pré-visualização
      
      // Atualiza o elemento do topCard para armazenar a imagem
      const topCardElement = previewElements.value.find(el => el.type === 'topCard');
      if (topCardElement) {
        topCardElement.image = e.target.result;  // Armazena a imagem no elemento para visualização
      }
    };
    reader.readAsDataURL(file);

    // Cria uma instância de FormData e adiciona o arquivo de imagem
    const formData = new FormData();
    formData.append('imagem', file);  // 'imagem' é o nome do campo do arquivo a ser enviado

    // Armazena o formData para ser usado posteriormente ao enviar o formulário
    topImageFormData.value = formData;

      
  }
};



const handleBackgroundImageUpload = (event) => {
  const file = event.target.files[0];

  if (file) {
    // Pré-visualização da imagem usando base64
    const reader = new FileReader();
    reader.onload = (e) => {
      backgroundImage.value = e.target.result;  // Armazena a imagem como base64 para pré-visualização
    };
    reader.readAsDataURL(file);

    // Cria uma instância de FormData e adiciona o arquivo de imagem
    const formData = new FormData();
    formData.append('backgroundImage', file);  // 'backgroundImage' é o nome do campo que será enviado ao backend

    // Armazena o formData para ser usado no envio do formulário posteriormente
    backgroundImageFormData.value = formData;
  }
};


const handleToggleConta = () => {
  if (form.value.types.conta) {
    addContaInputs();
  } else {
    removePreviewElement('input', 'username');
    removePreviewElement('inputPassword', 'password');
    removePreviewElement('button');
    removePreviewElement('buttonC');
    removePreviewElement('buttonA');
    removePreviewElement('text');
  }
};

const handleToggleRedeSocial = () => {
  if (form.value.types.rede_social) {
    showSocialModal.value = true;
  } else {
    form.value.selected_social_networks = [];
  }
};

const saveInsertItems = () => {
  // Lógica para salvar os itens selecionados
  showInsertItemsModal.value = false;
};

const saveSocialConfig = () => {
  // Lógica para salvar a configuração das redes sociais
  showSocialModal.value = false;
};


const handleToggleLoginClick = () => {
  if (form.value.types.login_click) {
    previewElements.value.push({
      id: previewElements.value.length + 1,
      type: 'button',
      top: 250,
      left: 50,
      opacity: form.value.button_opacity,
    });
  } else {
    removePreviewElement('button');
  }
};




// Adicionar topCard ao previewElements
previewElements.value.push({
  id: 12, // ID único
  type: 'topCard', // Tipo 'topCard'
  top: topCardTop.value,
  left: topCardLeft.value,
  width: form.value.imageTamanho,
  height: form.value.imageTamanho,
  
});




const addContaInputs = () => {
  const baseTop = 100;
  const baseLeft = 50;
  const spacing = 60; // Espaçamento entre os elementos

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'input',
    label: 'username',
    top: baseTop,
    left: baseLeft,
    backgroundColor: form.value.input_color,
    width: form.value.input_width,
    height: form.value.input_height,
    color: form.value.input_color,
    shape: form.value.input_shape,
    opacity: form.value.input_opacity,
  });


  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'inputPassword',
    label: 'password',
    top: baseTop + spacing, // Distanciando do input anterior
    left: baseLeft,
    backgroundColor: form.value.input_color,
    width: form.value.input_width,
    height: form.value.input_height,
    color: form.value.input_color,
    shape: form.value.input_shape,
    opacity: form.value.input_opacity,
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'button',
    top: baseTop + spacing * 2, // Aumentando o espaçamento para o botão
    left: baseLeft,
    opacity: form.value.button_opacity,
    text: form.value.login_button_text,
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'buttonC',
    width: 200,
    top: baseTop + spacing * 3, // Outro espaçamento
    left: baseLeft + 40, // Ajustando a posição horizontal do botão
    color: form.value.textColorButon,
    opacity: form.value.button_opacity,
    
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'buttonA',
    width: 100,
    top: baseTop + spacing * 2.5, // Colocando entre os dois botões
    left: baseLeft - 30, // Ajustando a posição horizontal
    color: form.value.textColorButon,
    opacity: form.value.button_opacity,
  });

  previewElements.value.push({
    id: previewElements.value.length + 1,
    type: 'text',
    top: baseTop + spacing * 2, // Espaçamento maior para o texto
    left: baseLeft,
    width: 200,
    color: form.value.textColor,
    height: 100,
    text: 'Texto editável',
  });
};


const removePreviewElement = (type, label = null) => {
  previewElements.value = previewElements.value.filter(
    element => element.type !== type || (label && element.label !== label)
  );
};

const previewStyles = ref({
  width: '375px',
  height: '667px',
  border: '1px solid #ccc',
  position: 'relative',
  overflow: 'hidden',
  borderRadius: '12px',
});



const backgroundStyles = computed(() => {
  if (form.value.background_type === 'Imagem') {
    // Verifica se existe uma imagem em base64 para pré-visualização
    if (backgroundImage.value) {
      return {
        backgroundImage: `url(${backgroundImage.value})`, 
        backgroundSize: 'cover', 
        backgroundPosition: 'center'
      };
    }
   
    // Se não houver base64, verifica se existe uma imagem salva no banco de dados (URL)
    if (form.value.backgroundImage && !form.value.backgroundImage.startsWith('data:image')) {
      return {
        backgroundImage: `url(/storage/${form.value.backgroundImage})`, 
        backgroundSize: 'cover', 
        backgroundPosition: 'center'
      };
    }
  }
  
  // Caso o tipo seja "Cor" ou não haja imagem, retorna a cor de fundo
  return {
    backgroundColor: form.value.background_value || '#ffffff'  // Define um valor padrão se necessário
  };
});


onMounted(() => {
  if (isEditMode.value) {
    loadElements();
  }

  interact('.draggable').draggable({
    listeners: {
      move(event) {
        const target = event.target;
        const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
        const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

        target.style.transform = `translate(${x}px, ${y}px)`;

        target.setAttribute('data-x', x);
        target.setAttribute('data-y', y);
      },
      end(event) {
        const target = event.target;
        const elementId = parseInt(target.getAttribute('data-id'), 10);

        // Recupera as posições finais do elemento
        const deltaX = parseFloat(target.getAttribute('data-x')) || 0;
        const deltaY = parseFloat(target.getAttribute('data-y')) || 0;
        const initialLeft = parseFloat(target.style.left) || 0;
        const initialTop = parseFloat(target.style.top) || 0;

        const newLeft = initialLeft + deltaX;
        const newTop = initialTop + deltaY;

        // Pega as dimensões da área de pré-visualização
        const previewContainer = target.closest('.preview-background');
        const containerWidth = previewContainer.offsetWidth;
        const containerHeight = previewContainer.offsetHeight;

        // Verifica se o elemento está fora dos limites
        if (
          newLeft + target.offsetWidth < 0 ||   // Fora da esquerda
          newTop + target.offsetHeight < 0 ||   // Fora do topo
          newLeft > containerWidth ||           // Fora da direita
          newTop > containerHeight              // Fora da base
        ) {
          console.log('Elemento fora dos limites');
          // Remove o elemento se estiver fora dos limites
          previewElements.value = previewElements.value.filter(
            element => element.id !== elementId
          );
          return;
        }

        // Caso contrário, salva as novas posições
        const element = previewElements.value.find(el => el.id === elementId);
        if (element) {
          element.left = newLeft;
          element.top = newTop;
          target.style.transform = 'none';
          target.setAttribute('data-x', 0);
          target.setAttribute('data-y', 0);
          target.style.left = `${newLeft}px`;
          target.style.top = `${newTop}px`;
        }
      },
    },
  });
});



// Função para retornar a máscara baseada no método de login
const getMaskForMethod = computed(() => {
  const loginMethod = form.value.login_method[0]; // Seleciona o primeiro método de login
  if (loginMethod === 'CPF') {
    return '###.###.###-##'; // Máscara para CPF
  } else if (loginMethod === 'Telefone') {
    return '(##) ####-####'; // Máscara para Telefone
  } else {
    return ''; // Sem máscara para Email ou Nome
  }
});

// Função para retornar a máscara baseada no método de senha
const getMaskForPassword = computed(() => {
  const passwordMethod = form.value.login_password_method[0]; // Seleciona o primeiro método de senha
  if (passwordMethod === 'CPF') {
    return '###.###.###-##'; // Máscara para CPF
  } else {
    return ''; // Senha Livre ou Data de Nascimento
  }
});


// Função para capturar o estado da tela e salvar como JSON

const saveScreenAsJson = () => {
  const screenData = {
  layout_name: form.value.layout_name,
  top_type: form.value.top_type,
  top_value: form.value.top_value,
  background_type: form.value.background_type,
  background_value: form.value.background_value,
  login_button_text: form.value.login_button_text,
  login_button_color: form.value.login_button_color,
  login_method: form.value.login_method,
  login_password_method: form.value.login_password_method,
  input_color: form.value.input_color,
  region: form.value.regiao,
  caditens: { ...form.value.caditens }, // Incluindo caditens
  social_networks: socialNetworks.value.map(network => ({ // Incluindo redes sociais
    name: network.name,
    on: network.on,
    key: network.key,
    secret: network.secret,
    url: network.url
  })),
  elements: previewElements.value.map(element => ({
    id: element.id,
    type: element.type,
    top: element.top,
    left: element.left,
    backgroundColor: element.type === 'input' || element.type === 'inputPassword' 
      ? form.value.input_color || element.backgroundColor 
      : element.type === 'button' 
      ? form.value.login_button_color || element.backgroundColor 
      : element.backgroundColor,
    width: element.type === 'input' || element.type === 'inputPassword'
      ? form.value.input_width || element.width
      : element.type === 'topCard'
      ? form.value.imageTamanho || element.width
      : element.width || 100,
    height: element.type === 'input' || element.type === 'inputPassword'
      ? form.value.input_height || element.height
      : element.type === 'topCard'
      ? form.value.imageTamanho || element.height
      : element.height || 40,
    text: element.text || null,
    color: element.type === 'text' 
      ? form.value.textColor || element.color 
      : element.type === 'buttonA' || element.type === 'buttonC' 
      ? form.value.textColorButon || element.color 
      : element.color,
    shape: element.type === 'input' || element.type === 'inputPassword' 
      ? form.value.input_shape || element.shape 
      : element.type === 'button' 
      ? form.value.login_button_shape || element.shape 
      : element.shape,
    opacity: element.opacity || form.value.button_opacity,
    elevation: element.elevation || form.value.button_elevation,
    image: element.image || null,
  }))
};


  

  // Criar uma instância de FormData para combinar screenData e arquivos de imagem
  const formData = new FormData();

  // Adicionar cada campo do screenData ao FormData
  Object.keys(screenData).forEach(key => {
    if (key === 'elements' && Array.isArray(screenData[key])) {
      // Adiciona cada elemento individualmente, como array, no FormData
      screenData[key].forEach((element, index) => {
        formData.append(`elements[${index}][id]`, element.id);
        formData.append(`elements[${index}][type]`, element.type);
        formData.append(`elements[${index}][top]`, element.top);
        formData.append(`elements[${index}][left]`, element.left);
        formData.append(`elements[${index}][backgroundColor]`, element.backgroundColor);
        formData.append(`elements[${index}][width]`, element.width);
        formData.append(`elements[${index}][height]`, element.height);
        formData.append(`elements[${index}][text]`, element.text);
        formData.append(`elements[${index}][color]`, element.color);
        formData.append(`elements[${index}][shape]`, element.shape);
        formData.append(`elements[${index}][opacity]`, element.opacity);
        formData.append(`elements[${index}][elevation]`, element.elevation);
        formData.append(`elements[${index}][image]`, element.image);
      });
    } else if (key === 'login_method' || key === 'login_password_method') {
      // Tratar arrays separadamente para manter a estrutura de array no backend
      if (Array.isArray(screenData[key])) {
        screenData[key].forEach((item, index) => {
          formData.append(`${key}[${index}]`, item);
        });
      }
    } else if (key === 'social_networks' && Array.isArray(screenData[key])) {
      screenData[key].forEach((network, index) => {
        formData.append(`social_networks[${index}][name]`, network.name);
        formData.append(`social_networks[${index}][on]`, network.on);
        formData.append(`social_networks[${index}][key]`, network.key);
        formData.append(`social_networks[${index}][secret]`, network.secret);
        formData.append(`social_networks[${index}][url]`, network.url);
      });
    } else if (key === 'caditens') {
      console.log('caditens', screenData[key]);
      Object.keys(screenData[key]).forEach((item) => {
        formData.append(`caditens[${item}]`, screenData[key][item]);
      });
    } else {
      formData.append(key, screenData[key]);
    }
  });

  // Adicionar as imagens ao FormData
  if (topImageFormData.value && !isEditMode.value) {
    formData.append('imagem', topImageFormData.value.get('imagem'));  // Adiciona a imagem de topo
  } else if (topImageFormData.value) {
    formData.append('imagem', topImageFormData.value.get('imagem'));  // Adiciona a imagem de topo para update
  }

  if (backgroundImageFormData.value) {
    formData.append('backgroundImage', backgroundImageFormData.value.get('backgroundImage'));  // Adiciona a imagem de fundo
  }


  const url = isEditMode.value ? `/login_customizations/${form.value.id}` : '/login_customizations'; // Se tem ID, então é edição
  const method = isEditMode.value ? 'post' : 'post'; // 'put' para atualizar, 'post' para inserir
  if (isEditMode.value) {
    formData.append('_method', 'PUT');  // Adiciona o campo _method para emular PUT
  }

  axios({
    method: method,
    url: url,
    data: formData, // Enviar o FormData
    headers: {
      'Content-Type': 'multipart/form-data',
      
    }
  })
  .then(response => {
    console.log('Customização salva com sucesso!', response);
    window.location.href = '/login_customizations'; // Redirecionar após o sucesso
  })
  .catch(error => {
    console.error('Erro ao salvar customização:', error);
    errorMessage.value = 'Erro ao salvar customização: ' + (error.response?.data?.message || error.message || 'Erro desconhecido');
  });
};







// Função para baixar o arquivo JSON
const downloadJsonFile = (jsonString, fileName) => {
  const blob = new Blob([jsonString], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = fileName;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

</script>

<template>
  <AuthenticatedLayout>
    <v-container fluid>
      <v-row>
        <!-- Coluna de Customização -->
        <v-col cols="12" md="4">
          <v-card class="customization-card" exportparts="5" elevation="5">
             
              <v-card-title>Configure nome e Região</v-card-title>
              <v-card class="customization-card" elevation="5">
              <!-- Input para o nome do layout -->
              <v-text-field
                v-model="form.layout_name"
                label="Nome do Layout"
                :style="{ width: '300px', alignItems: 'center' }"
              ></v-text-field>
                   
                   <!-- Selecione uma Região -->
              <regioes-select
                
                v-model="form.regiao"
                label="Selecione uma região"
                :rules="[v => !!v || 'A seleção de uma região é obrigatória']"
               />
               <v-btn @click="showInsertItemsModal = true" color="secondary" class="mt-4">
              Inserir Itens
            </v-btn>
             <!-- Modal para seleção de campos -->
             <v-dialog v-model="showInsertItemsModal" max-width="500px">
              <v-card>
                <v-card-title>Selecione os Itens</v-card-title>
                <v-card-text>
                  <v-checkbox v-model="form.caditens.nome" label="Nome" />
                  <v-checkbox v-model="form.caditens.usuario" label="Nome de Usuário" />
                  <v-checkbox v-model="form.caditens.email" label="Email" />
                  <v-checkbox v-model="form.caditens.senha" label="Senha" />
                  <v-checkbox v-model="form.caditens.cpf" label="CPF" />
                  <v-checkbox v-model="form.caditens.telefone" label="Telefone" />
                  <v-checkbox v-model="form.caditens.nascimento" label="Data de Nascimento" />
                  <v-checkbox v-model="form.caditens.sexo" label="Sexo" />
                </v-card-text>
                <v-card-actions>
                  <v-btn text @click="showInsertItemsModal = false">Cancelar</v-btn>
                  <v-btn color="primary" @click="saveInsertItems">Salvar</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>

            <!-- Checkbox para Redes Sociais -->
            <v-checkbox
              v-model="form.types.rede_social"
              label="Rede Social"
              @change="handleToggleRedeSocial"
            ></v-checkbox>

            <!-- Modal para redes sociais -->
            <v-dialog v-model="showSocialModal" max-width="500px">
              <v-card>
                <v-card-title>Redes Sociais</v-card-title>
                <v-card-text>
                  <div v-for="(network, index) in socialNetworks" :key="index" class="my-2">
                    <v-checkbox v-model="network.on" :label="network.name" />
                    <template v-if="network.on">
                      <v-text-field v-model="network.key" label="Key" />
                      <v-text-field v-model="network.secret" label="Secret" />
                      <v-text-field v-model="network.url" label="URL" />
                    </template>
                  </div>
                </v-card-text>
                <v-card-actions>
                  <v-btn text @click="showSocialModal = false">Cancelar</v-btn>
                  <v-btn color="primary" @click="saveSocialConfig">Salvar</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-card>  

          
          <v-card-title>Ajuste fundo e logotipo</v-card-title>
          <v-card class="customization-card" elevation="10">
              <!-- Escolha de Topo (Cor ou Imagem) -->
              <v-radio-group v-model="form.top_type" label="Adcionar Logotipo" inline>
                <v-radio label="Add imagem" value="Imagem"></v-radio>
              </v-radio-group>

            
              <template v-if="form.top_type === 'Imagem'">
                <v-file-input
                  label="Upload Imagem de Topo"
                  accept="image/*"
                  prepend-icon="mdi-upload"              
                  @change="handleTopImageUpload"
                ></v-file-input>
              </template>

              <!-- Escolha de Fundo (Cor ou Imagem) -->
              <v-radio-group v-model="form.background_type" label="Escolher Fundo" inline>
                <v-radio label="Cor" value="Cor"></v-radio>
                <v-radio label="Imagem" value="Imagem"></v-radio>
              </v-radio-group>

              <template v-if="form.background_type === 'Cor'">
                Selecione a cor:
                <v-btn
                  class="my-2"
                  :style="{ backgroundColor: form.background_value, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
                  @click="showBackgroundColorPicker = true"
                ></v-btn>
                <v-dialog v-model="showBackgroundColorPicker" max-width="300px">
                  <v-card>
                    <v-color-picker
                      v-model="form.background_value"
                      hide-inputs
                      @change="closeBackgroundColorPicker"
                    ></v-color-picker>
                  </v-card>
                </v-dialog>
              </template>
              <template v-else>
                <v-file-input
                  label="Upload Imagem de Fundo"
                  accept="image/*"
                  prepend-icon="mdi-upload"
                  @change="handleBackgroundImageUpload"
                ></v-file-input>
              </template>

          </v-card>
          <v-card-title>Inserir entradas</v-card-title>
          <v-card  elevation="5">
                        <!-- Estilização do Botão de Login -->
                        <v-text-field
                label="Texto do Botão de Login"
                v-model="form.login_button_text"
                :style="{ width: '200px' }"
              ></v-text-field>

              <!-- Selecione a cor do botão de login -->
              Cor botao:
              <v-btn
                small
                :style="{ backgroundColor: form.login_button_color, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
                @click="showLoginButtonColorPicker = true"
              ></v-btn>

              <v-dialog v-model="showLoginButtonColorPicker" max-width="300px">
                <v-card>
                  <v-color-picker
                    v-model="form.login_button_color"
                    hide-inputs
                    @change="closeLoginButtonColorPicker"
                  ></v-color-picker>
                </v-card>
              </v-dialog>

              <!-- Tipos de Login com Checkboxes -->
              <v-checkbox
                v-model="form.types.conta"
                label="Conta"
                @change="handleToggleConta"
              ></v-checkbox>

              <template v-if="form.types.conta">
                <v-select
                  v-model="form.login_method"
                  :items="['Email', 'Nome', 'Telefone']"
                  label="Login Usando"
                  multiple
                  @change="handleLoginMethodChange"
                ></v-select>
                <v-select
                  v-model="form.login_password_method"
                  :items="['CPF', 'Data de Nascimento', 'Telefone', 'Livre']"
                  label="Senha Usando"
                  multiple
                  @change="handlePasswordMethodChange"
                ></v-select>
              </template>

              <v-checkbox
                v-model="form.types.rede_social"
                label="Rede Social"
                @change="handleToggleRedeSocial"
              ></v-checkbox>

              <template v-if="form.types.rede_social">
                <v-select
                  v-model="form.selected_social_networks"
                  :items="['Facebook', 'Google', 'Twitter']"
                  label="Selecione Redes Sociais"
                  multiple
                ></v-select>
              </template>

              <v-checkbox
                v-model="form.types.login_click"
                label="Login com um click"
                @change="handleToggleLoginClick"
              ></v-checkbox>
           </v-card>   


             
        </v-card>
        </v-col>

        <!-- Coluna de Preview Fixa -->
        <v-col cols="12" md="4" class="preview-sticky">
          <v-alert v-if="errorMessage" type="error" dismissible>
            {{ errorMessage }}
          </v-alert>
          <h2>Preview da Tela de Login</h2>
          <v-card :style="previewStyles">
            <div :style="backgroundStyles" class="preview-background">
              <div
                v-for="element in previewElements"
                :key="element.id"
                :data-id="element.id"
                :ref="element.type === 'topCard' ? 'topCard' : null" 
                :style="{
                  top: element.top + 'px',
                  left: element.left + 'px',
                  position: 'absolute',
                  width: '100%',
                  height: '100%',
                }"
                class="draggable"
              >
                <!-- Renderizar topCard -->
                <template v-if="element.type === 'topCard' && form.top_type === 'Imagem' && (previewTopImage || element.image)">
                  <v-img :src="resolveImageSource(element.image)" :max-height="form.imageTamanho || element.height + 'px'" :max-width="form.imageTamanho || element.width + 'px'"></v-img>
                </template>

                <!-- Renderizar outros tipos de elementos -->
                <v-btn v-if="element.type === 'button'" :style="{ background: form.login_button_color, borderRadius: form.login_button_shape + 'px', boxShadow: '0 ' + form.button_elevation + 'px ' + form.button_elevation + 'px rgba(0,0,0,0.2)', opacity: form.button_opacity, color: form.text_color }">
                  {{ form.login_button_text }}
                </v-btn>
                <a v-if="element.type === 'buttonC'" href="#" @click="handleCreateAccount" :style="{ color: form.textColorButon, fontSize: '14px', textDecoration: 'underline', cursor: 'pointer' }">Criar nova conta</a>
                <a v-if="element.type === 'buttonA'" href="#" @click="handleHelpAccount" :style="{ color: form.textColorButon, fontSize: '14px', textDecoration: 'underline', cursor: 'pointer' }">Ajuda</a>
                <div v-if="element.type === 'text'" contenteditable="true" ref="editableText" class="editable-text" :style="{ color: form.textColor, fontSize: fontSize + 'px', fontWeight: fontWeight, fontStyle: fontStyle }" @input="updateEditableText($event, element.id)">
                  {{ element.text }}
                </div>

                <!-- Renderizar inputs de login e senha -->
                <template v-if="element && form.types.conta" :style="{ backgroundColor: 'transparent' }">
                  <div>
                        <!-- Label do Login -->
                        <label
                          v-if="element.label === 'username'"
                          :for="element.label"
                          :style="{ backgroundColor: 'transparent' }"
                        >Login:</label>

                        <!-- Input do Login -->
                        <input
                          v-if="element.type === 'input' && element.type === 'input'"
                          id="username"
                          :placeholder="form.login_method.join(' ou ')" 
                          :style="{
                            width:  form.input_width + 'px',
                            height:  form.input_height + 'px',
                            backgroundColor:  form.input_color,
                            borderRadius:  form.input_shape + 'px',
                            boxShadow: '0 ' + (form.input_elevation) + 'px ' + ( form.input_elevation) + 'px rgba(0,0,0,0.2)',
                            color:  form.text_color,
                            opacity:  form.input_opacity,
                            border: '1px solid #ccc',
                            padding: '8px',
                            outline: 'none'
                          }"
                          v-model="form.input_value"
                         
                        />
                      </div>

                      <div>
                        <!-- Label da Senha -->
                        <label
                          v-if="element.label === 'password'"
                          :style="{ backgroundColor: 'transparent' }"
                          for="password"
                        >Senha:</label>

                        <!-- Input da Senha -->
                        <input
                          v-if="element.type === 'inputPassword' && (element.label || isEditMode)"
                          id="password"
                          :placeholder="form.login_password_method.join(' ou ')" 
                          :style="{
                            width:  form.input_width + 'px',
                            height: form.input_height + 'px',
                            backgroundColor:  form.input_color,
                            borderRadius: form.input_shape + 'px',
                            boxShadow: '0 ' + (form.input_elevation) + 'px ' + ( form.input_elevation) + 'px rgba(0,0,0,0.2)',
                            color:  form.text_color,
                            opacity:  form.input_opacity,
                            border: '1px solid #ccc',
                            padding: '8px',
                            outline: 'none'
                          }"
                          type="password"
                          v-model="form.password_value"
                          
                        />
                      </div>

                </template>
              </div>
            </div>
          </v-card>

          <v-btn @click="saveScreenAsJson" color="primary">Salvar como JSON</v-btn>
        </v-col>

        <!-- Coluna de Sliders à Direita -->
        <v-col cols="12" md="3" class="sliders-sticky" >
          <v-card class="sliders-card" elevation="5">
          <v-card-title class="sliders-title">Ajustes de tamanho</v-card-title>
          
          <v-card class="sliders-card" elevation="5">
            <!-- Sliders para ajustar o tamanho da Imagem -->         
          <v-slider v-model="form.imageTamanho" label="Tamanho da Imagem" min="50" max="1000" thumb-label></v-slider>
         </v-card>
          <v-card-title class="sliders-title">Controles de Botão</v-card-title>
          <v-card elevation="5">
          <!-- Sliders para controle do botão -->
          <v-slider label="Formato Botão(Arredondamento)" v-model="form.login_button_shape" min="0" max="50" thumb-label></v-slider>
          <v-slider label="Elevação do Botão" v-model="form.button_elevation" min="0" max="10" thumb-label></v-slider>
         </v-card>
         <v-card-title class="sliders-title">Controles de Input</v-card-title>
         <v-card elevation="5">
          <!-- Controles de Inputs -->
          <v-slider label="Largura do Input" v-model="form.input_width" min="100" max="400" thumb-label></v-slider>
          <v-slider label="Altura do Input" v-model="form.input_height" min="25" max="100" thumb-label></v-slider>
          <v-slider label="Formato Input (Arredondamento)" v-model="form.input_shape" min="0" max="50" thumb-label></v-slider>
          <v-slider label="Elevação do Input" v-model="form.input_elevation" min="0" max="10" thumb-label></v-slider>
                      <!-- Selecione a cor do botão do input -->
                      Cor input:
              <v-btn
                small
                :style="{ backgroundColor: form.input_color, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }"
                @click="showInputColorPicker = true"
              ></v-btn>

              <v-dialog v-model="showInputColorPicker" max-width="300px">
                <v-card>
                  <v-color-picker
                    v-model="form.input_color"
                    hide-inputs
                    @change="closeInputColorPicker"
                  ></v-color-picker>
                </v-card>
              </v-dialog>
         </v-card>
         <v-card-title class="sliders-title">Controles de texto mensagem</v-card-title>
         <v-card elevation="5">
          <!-- Controles para editar o texto -->
          <v-slider v-model="fontSize" label="Tamanho da Fonte" min="12" max="40" thumb-label></v-slider>

                    Cor do Texto
                  <v-btn small :style="{ backgroundColor: form.textColor, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }" @click="showTextColorPicker = true"></v-btn>

                  <v-dialog v-model="showTextColorPicker" max-width="300px">
                    <v-card>
                      <v-color-picker v-model="form.textColor" hide-inputs @change="closeTextColorPicker"></v-color-picker>
                    </v-card>
                  </v-dialog>
          
              
                  Cor Ajuda/cad 
                  <v-btn small :style="{ backgroundColor: form.textColorButon, width: '30px', height: '30px', borderRadius: '4px', border: '8px solid #ccc' }" @click="showTextColorButtonPicker = true"></v-btn>

                  <v-dialog v-model="showTextColorButtonPicker" max-width="300px">
                    <v-card>
                      <v-color-picker v-model="form.textColorButon" hide-inputs @change="closeTextColorPicker"></v-color-picker>
                    </v-card>
                  </v-dialog>

                  <!-- Controles para Negrito e Itálico -->
                  <v-checkbox v-model="isBold" label="Negrito"></v-checkbox>
                  <v-checkbox v-model="isItalic" label="Itálico"></v-checkbox>
          </v-card>  

          

        
          </v-card> 
        </v-col>
      </v-row>

    </v-container>
  </AuthenticatedLayout>
</template>



<style scoped>
.editable-text {
  padding: 8px;
  border: 'none';
  min-height: 50px;
  min-width: 150px;
  cursor: text;
}
.preview-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.preview-background {
  height: 100%;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.preview-top {
  border: none !important;  /* Remover borda */
  position: relative;
}
label {
  background-color: transparent !important;
  display: inline-block; /* Certifique-se de que o label não seja afetado por estilos de block */
  padding: 0;
  margin: 0;
}



.resizable {
  resize: both;
  overflow: auto;
}

.draggable {
  cursor: move;
}

.preview-sticky {
  position: sticky;
  top: 0;
}

.v-select {
  width: 200px;
}

.v-file-input {
  width: 300px;
}


</style>
