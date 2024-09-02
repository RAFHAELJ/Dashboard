<template>
    <form @submit.prevent="enviar">
      <input type="text" v-model="nome" :placeholder="nomePlaceholder"/>
      <input type="email" v-model="email" :placeholder="emailPlaceholder"/>
      <button type="submit">{{ botaoEnviar }}</button>
    </form>
  </template>
  
  <script>
  export default{
    props: {
    nomePlaceholder: {
    type: String,
    default: 'Nome',
    },
    emailPlaceholder: {
    type: String,
    default: 'Email',
    },
    botaoEnviar: {
    type: String,
    default: 'Enviar',
    },
    },
    data() {
      return {
        nome: '',
        email: '',
      };
    },
    methods: {
      enviar() {
      console.log(this.nome, this.email);
      },
     },
    };
  </script>