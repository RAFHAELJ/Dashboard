<template>
    
    <div class="user-form">
      <h2>Cadastro de Usuário</h2>
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="nome">Nome</label>
          <input
            type="text"
            id="nome"
            v-model="form.nome"
            class="form-control"
            :class="{ 'is-invalid': errors.nome }"
            placeholder="Digite seu nome"
          />
          <div v-if="errors.nome" class="invalid-feedback">
            {{ errors.nome }}
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
            placeholder="Digite seu e-mail"
          />
          <div v-if="errors.email" class="invalid-feedback">
            {{ errors.email }}
          </div>
        </div>
  
        <div class="form-group">
          <label for="senha">Senha</label>
          <input
            type="password"
            id="senha"
            v-model="form.senha"
            class="form-control"
            :class="{ 'is-invalid': errors.senha }"
            placeholder="Digite sua senha"
          />
          <div v-if="errors.senha" class="invalid-feedback">
            {{ errors.senha }}
          </div>
        </div>
  
        <div class="form-group">
          <label for="confirmSenha">Confirme a Senha</label>
          <input
            type="password"
            id="confirmSenha"
            v-model="form.confirmSenha"
            class="form-control"
            :class="{ 'is-invalid': errors.confirmSenha }"
            placeholder="Confirme sua senha"
          />
          <div v-if="errors.confirmSenha" class="invalid-feedback">
            {{ errors.confirmSenha }}
          </div>
        </div>
  
        <button type="submit" class="btn btn-primary">Cadastrar</button>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        form: {
          nome: '',
          email: '',
          senha: '',
          confirmSenha: ''
        },
        errors: {}
      };
    },
    methods: {
      validateForm() {
        this.errors = {};
  
        if (!this.form.nome) {
          this.errors.nome = 'O nome é obrigatório.';
        }
  
        if (!this.form.email) {
          this.errors.email = 'O e-mail é obrigatório.';
        } else if (!this.validEmail(this.form.email)) {
          this.errors.email = 'O e-mail não é válido.';
        }
  
        if (!this.form.senha) {
          this.errors.senha = 'A senha é obrigatória.';
        } else if (this.form.senha.length < 6) {
          this.errors.senha = 'A senha deve ter pelo menos 6 caracteres.';
        }
  
        if (!this.form.confirmSenha) {
          this.errors.confirmSenha = 'A confirmação de senha é obrigatória.';
        } else if (this.form.confirmSenha !== this.form.senha) {
          this.errors.confirmSenha = 'As senhas não coincidem.';
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
            const response = await axios.post('/users', this.form);
            alert('Usuário cadastrado com sucesso!');
            // Limpar o formulário após o cadastro
            this.form.nome = '';
            this.form.email = '';
            this.form.senha = '';
            this.form.confirmSenha = '';
          } catch (error) {
            if (error.response && error.response.data.errors) {
              this.errors = error.response.data.errors;
            } else {
              alert('Ocorreu um erro ao cadastrar o usuário.');
            }
          }
        }
      }
    }
  };
  </script>
  
  <style scoped>
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
  