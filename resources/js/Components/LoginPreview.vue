<template>
    <v-container class="no-padding no-margin" fluid>
      <v-row class="no-padding no-margin fill-height d-flex justify-center align-center">
        <v-col cols="12" class="preview-column no-padding no-margin">
          <v-card class="preview-card" :style="previewStyles">
            <div v-if="screenData" class="preview-background" :style="backgroundStyles">
              <form>
                <!-- Elementos da tela renderizados dinamicamente -->
                <div
                  v-for="(element, index) in screenData?.elements"
                  :key="element.id"
                  :style="elementStyle(element, index)"
                  class="draggable"
                >
                  <!-- Topo da página -->
                  <template v-if="element.type === 'topCard' && element.image">
                    <v-img :src="resolveImageSource(element.image)" :max-height="element.height + 'px'" :max-width="element.width + 'px'"></v-img>
                  </template>
  
                  <!-- Botão -->
                  <v-btn v-if="element.type === 'button'" :style="buttonStyle(element)">
                    {{ screenData?.login_button_text || 'Login' }}
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
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    props: {
      // Propriedade opcional para passar customizações de fora
      customScreenData: {
        type: Object,
        default: null,
      },
    },
    
    data() {
      return {
        screenData: null,
        previewStyles: {
          width: "375px",
          height: "667px",
          border: "1px solid #ccc",
          position: "relative",
          overflow: "hidden",
          borderRadius: "12px",
        },
      };
    },
    computed: {
      backgroundStyles() {
        return {
          backgroundImage:
            this.screenData?.background_type === "Imagem" && this.screenData?.background_value
              ? `url(/storage/${this.screenData.background_image})`
              : "none",
          backgroundColor:
            this.screenData?.background_type === "Cor" && this.screenData?.background_value
              ? this.screenData?.background_value
              : "transparent",
          backgroundSize: "cover",
          backgroundPosition: "center",
          backgroundRepeat: "no-repeat",
          width: "100%",
          height: "100%",
        };
      },
    },
    methods: {
      handleCreateAccount() {
        console.log("Criar nova conta");
      },
      handleHelpAccount() {
        console.log("Ajuda");
      },
      fetchData() {
        axios
          .get("/login_customizations")
          .then((response) => {
            let customization = response.data.customizations[0]; // Pegue a primeira customização, ajuste se necessário  
            // Convertendo campos de string JSON para arrays, se necessário
            if (typeof customization.login_method === "string") {
              customization.login_method = JSON.parse(customization.login_method);
            }
            if (typeof customization.elements === "string") {
              customization.elements = JSON.parse(customization.elements);
            }
            if (typeof customization.login_password_method === "string") {
              customization.login_password_method = JSON.parse(customization.login_password_method);
            }
  
            this.screenData = customization;
          })
          .catch((error) => {
            console.error("Erro ao carregar os dados da customização:", error);
          });
      },
      elementStyle(element, index) {
        return {
          top: element.top + "px",
          left: element.left + "px",
          position: "absolute",
          width: element.width + "px",
          height: element.height + "px",
          zIndex: index + 1,
        };
      },
      buttonStyle(element) {
        return {
          background: element.backgroundColor,
          borderRadius: element.shape + "px",
          boxShadow: "0 " + element.elevation + "px " + element.elevation + "px rgba(0,0,0,0.2)",
          opacity: element.opacity,
          color: this.screenData?.text_color,
        };
      },
      linkStyle(element) {
        return {
          color: element.color,
          fontSize: "14px",
          textDecoration: "underline",
          cursor: "pointer",
          position: "transform",
          top: element.top + "px",
          left: element.left + "px",
        };
      },
      textStyle(element) {
        return {
          color: element.color || this.screenData?.text_color,
          fontSize: "16px",
          fontWeight: "bold",
          fontStyle: "italic",
          position: "transform",
          top: element.top + "px",
          left: element.left + "px",
        };
      },
      inputStyle(element) {
        return {
          position: "transform",
          top: element.top + "px",
          left: element.left + "px",
          width: element.width + "px",
          height: element.height + "px",
          backgroundColor: element.backgroundColor,
          border: "1px solid #ccc",
          borderRadius: element.shape + "px",
        };
      },
      
 resolveImageSource(element) {
 
    return element.startsWith('data:image') ? element : `/storage/${element}`;

},
      inputInternalStyle(element) {
        return {
          width: "100%",
          height: "100%",
          backgroundColor: element.backgroundColor,
          borderRadius: element.shape + "px",
          opacity: element.opacity,
          boxShadow: "0 " + element.elevation + "px " + element.elevation + "px rgba(0,0,0,0.2)",
          padding: "8px",
          outline: "none",
        };
      },
    },
    mounted() {
      if (!this.customScreenData) {
        this.fetchData(); // Carregar os dados do servidor se não tiver sido passado externamente
      } else {
        this.screenData = this.customScreenData; // Se passado como propriedade, usa-se esses dados
        
       
      }
    },
  };
  </script>
  
  <style scoped>
  .no-padding {
    padding: 0 !important;
  }
  .no-margin {
    margin: 0 !important;
  }
  
  .fill-height {
    height: 100vh;
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
  

  </style>
  