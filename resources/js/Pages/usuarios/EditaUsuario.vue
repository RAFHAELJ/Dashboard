<template>
    <div class="user-form">
      <h2>Editar Usuário</h2>
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="name">Nome</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            class="form-control"
            :class="{ 'is-invalid': errors.name }"
            placeholder="Digite o nome"
          />
          <div v-if="errors.name" class="invalid-feedback">
            {{ errors.name }}
          </div>
        </div>
  
        <div class="form-group">
          <label for="email">E-mail</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            class="form-control"
            :class="{ 'is-invalid': errors.email }"
            placeholder="Digite o e-mail"
          />
          <div v-if="errors.email" class="invalid-feedback">
            {{ errors.email }}
          </div>
        </div>
  
        <div class="form-group">
          <label for="password">Senha</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            class="form-control"
            :class="{ 'is-invalid': errors.password }"
            placeholder="Deixe em branco para manter a senha atual"
          />
          <div v-if="errors.password" class="invalid-feedback">
            {{ errors.password }}
          </div>
        </div>
  
        <div class="form-group">
          <label for="confirmPassword">Confirme a Senha</label>
          <input
            type="password"
            id="confirmPassword"
            v-model="form.confirmPassword"
            class="form-control"
            :class="{ 'is-invalid': errors.confirmPassword }"
            placeholder="Confirme a nova senha"
          />
          <div v-if="errors.confirmPassword" class="invalid-feedback">
            {{ errors.confirmPassword }}
          </div>
        </div>
  
        <button type="submit" class="btn btn-primary">Atualizar</button>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'EditarUsuario',
    data() {
      return {
        form: {
          name: '',
          email: '',
          password: '',
          confirmPassword: ''
        },
        errors: {}
      };
    },
    methods: {
      fetchUserData() {
        const userId = this.$route.params.id;
        axios
          .get(`/api/users/${userId}`)
          .then(response => {
            this.form.name = response.data.name;
            this.form.email = response.data.email;
          })
          .catch(error => {
            console.error('Erro ao buscar os dados do usuário:', error);
          });
      },
      validateForm() {
        this.errors = {};
  
        if (!this.form.name) {
          this.errors.name = 'O nome é obrigatório.';
        }
  
        if (!this.form.email) {
          this.errors.email = 'O e-mail é obrigatório.';
        } else if (!this.validEmail(this.form.email)) {
          this.errors.email = 'O e-mail não é válido.';
        }
  
        if (this.form.password && this.form.password.length < 6) {
          this.errors.password = 'A senha deve ter pelo menos 6 caracteres.';
        }
  
        if (this.form.password && this.form.confirmPassword !== this.form.password) {
          this.errors.confirmPassword = 'As senhas não coincidem.';
        }
  
        return Object.keys(this.errors).length === 0;
      },
      validEmail(email) {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
      },
      async submitForm() {
        if (this.validateForm()) {
          try {
            const userId = this.$route.params.id;
            await axios.put(`/api/users/${userId}`, this.form);
            alert('Usuário atualizado com sucesso!');
            this.$router.push('/usuarios');
          } catch (error) {
            if (error.response && error.response.data.errors) {
              this.errors = error.response.data.errors;
            } else {
              alert('Ocorreu um erro ao atualizar o usuário.');
            }
          }
        }
      }
    },
    mounted() {
      this.fetchUserData(); // Busca os dados do usuário ao montar o componente
    }
  };
  </script>
  
  <style scoped>
  /* Os mesmos estilos do formulário de cadastro */
  .user-form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 5px;
  }
  
  .form-control {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }
  
  .form-control.is-invalid {
    border-color: #e3342f;
  }
  
  .invalid-feedback {
    color: #e3342f;
    font-size: 14px;
  }
  
  .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    color: #fff;
  }
  
  .btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
  }
  </style>
  