<template>
  <v-app>
    <v-container class="no-padding no-margin" fluid >
      <v-row class="no-padding no-margin fill-height d-flex justify-center align-center">
        <v-col cols="12" class="preview-column no-padding no-margin">
          <v-card class="preview-card" :style="previewStyles">
            <div v-if="screenData" class="preview-background" :style="backgroundStyles">
              <form>
                <!-- Elementos da tela renderizados dinamicamente -->
                <div v-for="(element, index) in screenData?.elements" :key="element.id" :style="elementStyle(element, index)" class="draggable">
                  <!-- Topo da página -->
                  <template v-if="element.type === 'topCard' && element.image">
                    <v-img :src="element.image" :max-height="element.height + 'px'" :max-width="element.width + 'px'"></v-img>
                  </template>

                  <!-- Botão -->
                  <v-btn v-if="element.type === 'button'" :style="buttonStyle(element)">
                    {{ element.text || screenData?.login_button_text || 'Login' }}
                  </v-btn>

                  <!-- Links de texto -->
                  <a v-if="element.type === 'buttonC'" href="#" @click="handleCreateAccount" :style="linkStyle(element)">
                    Criar nova conta
                  </a>
                  <a v-if="element.type === 'buttonA'" href="#" @click="handleHelpAccount" :style="linkStyle(element)">
                    Ajuda
                  </a>

                  <!-- Texto editável -->
                  <div v-if="element.type === 'text'" contenteditable="true" class="editable-text" :style="textStyle(element)">
                    {{ element.text || 'Editável! Adicione seu texto aqui...' }}
                  </div>

                  <!-- Campo de Login -->
                  <div v-if="element.type === 'input'" class="input-field" :style="inputStyle(element)">
                    <input v-model="element.value" :placeholder="screenData?.login_method?.join(' ou ') || 'Login'" :style="inputInternalStyle(element)" />
                  </div>

                  <!-- Campo de Senha -->
                  <div v-if="element.type === 'inputPassword'" class="input-field" :style="inputStyle(element)">
                    <input v-model="element.value" type="password" :placeholder="screenData?.login_password_method?.join(' ou ') || 'Senha'" :style="inputInternalStyle(element)" />
                  </div>
                </div>
              </form>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>



<script>
export default {
data() {
  return {
    screenData: null,  // Inicializa como nulo até que os dados do JSON sejam carregados
    previewStyles: {
      width: '375px',
      height: '667px',
      border: '1px solid #ccc',
      position: 'relative', // Garanta que o container principal seja relative
      overflow: 'hidden',
      borderRadius: '12px'
    },
    topCardHeight: 150,
    topCardWidth: 375,
  };
},
computed: {
  topCardStyle() {
    return {
      height: this.topCardHeight + 'px',
      width: this.topCardWidth + 'px',
      backgroundColor: this.screenData?.top_type === 'Cor' ? this.screenData?.top_value : 'transparent'
    };
  },
  backgroundStyles() {
      return {
          backgroundImage: this.screenData?.background_type === 'Imagem' && this.screenData?.background_value
          ? `url(${this.screenData.backgroundImage})`
          : 'none',
          backgroundColor: this.screenData?.background_type === 'Cor' && !this.screenData?.background_value
          ? this.screenData?.background_value
          : 'transparent',
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          backgroundRepeat: 'no-repeat',  // Evita repetição de imagem
          width: '100%',  // Garante que a imagem de fundo preencha o container
          height: '100%',
      };
      },
},
methods: {
  elementStyle(element, index) {
    return {
      top: element.top + 'px',
      left: element.left + 'px',
      position: 'absolute', // Garanta que a posição seja 'absolute'
      width: element.width + 'px',
      height: element.height + 'px',
      zIndex: index + 1 // Garantindo que cada elemento tenha uma camada própria
    };
  },
  buttonStyle(element) {
    return {
      background: element.color || this.screenData?.login_button_color,
      borderRadius: element.shape + 'px',
      boxShadow: '0 ' + element.elevation + 'px ' + element.elevation + 'px rgba(0,0,0,0.2)',
      opacity: element.opacity,
      color: this.screenData?.text_color
    };
  },
  linkStyle(element) {
    return {
      color: this.screenData?.text_color,
      fontSize: '14px',
      textDecoration: 'underline',
      cursor: 'pointer',
      position: 'transform',
      top: element.top + 'px',
      left: element.left + 'px'
    };
  },
  textStyle(element) {
    return {
      color: element.color || this.screenData?.text_color,
      fontSize: '16px',
      fontWeight: 'bold',
      fontStyle: 'italic',
      position: 'trasform',
      top: element.top + 'px',
      left: element.left + 'px'
    };
  },
  inputStyle(element) {
      console.log(element);
    return {
      position: 'transform',
      top: element.top + 'px',
      left: element.left + 'px',
      width: element.width + 'px',
      height: element.height + 'px',
      backgroundColor: element.backgroundColor, // Adiciona uma cor de fundo visível
      border: '1px solid #ccc', // Adiciona uma borda visível
      borderRadius: element.shape + 'px'
    };
  },
  inputInternalStyle(element) {
    return {
      width: '100%',
      height: '100%',
      backgroundColor: element.backgroundColor,
      borderRadius: element.shape + 'px',
      opacity: element.opacity,
      boxShadow: '0 ' + element.elevation + 'px ' + element.elevation + 'px rgba(0,0,0,0.2)',
      padding: '8px',
      outline: 'none'
    };
  },


  handleCreateAccount() {
    console.log('Criar nova conta');
  },
  handleHelpAccount() {
    console.log('Ajuda');
  },
  fetchData() {
    fetch('/login_customization.json')
      .then(response => response.json())
      .then(data => {
        if (!data.login_method) {
          data.login_method = ['Email']; 
        }
        if (!data.login_password_method) {
          data.login_password_method = ['Senha']; 
        }
        if (!data.login_button_text) {
          data.login_button_text = 'Login'; 
        }
        this.screenData = data;
      })
      .catch(error => {
        console.error('Erro ao carregar o JSON:', error);
      });
  }
},
mounted() {
  this.fetchData();
}
};
</script>

<style scoped>
/* Remover padding e margem globalmente */
.no-padding {
padding: 0 !important;
}
.no-margin {
margin: 0 !important;
}

.fill-height {
height: 100vh; /* Ocupa toda a altura da viewport */
}

.preview-column {
display: flex;
justify-content: center;
align-items: center;
padding: 0;
margin: 0;
}

.preview-card {
width: 100%;
max-width: 400px;
min-height: 667px;
background-color: #fff;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
overflow: hidden;
}

.preview-background {
width: 100%;
height: 100%;
display: flex;
justify-content: center;
align-items: center;
background-color: #fff;
border-radius: 12px;
}

.editable-text {
font-size: 16px;
color: #000;
padding: 8px;
border: none;
background-color: transparent;
min-height: 50px;
min-width: 150px;
}

.input-field {
width: 100%;
margin-bottom: 16px;
}

input {
width: 100%;
padding: 8px;
border: 1px solid #ccc;
border-radius: 8px;
outline: none;
font-size: 14px;
}

/* Remover margem lateral no mobile e preencher toda a largura */
@media (max-width: 600px) {
.preview-card {
  width: 100vw;
  height: 100vh;
  max-width: none;
  max-height: none;
  border-radius: 0;
  box-shadow: none;
}

.preview-background {
  border-radius: 0;
}
}

/* Para telas maiores, o preview é centralizado */
@media (min-width: 768px) {
.preview-container {
  justify-content: center;
}

.preview-card {
  margin: auto;
  width: 375px;
  height: 667px;
}
}

</style>
