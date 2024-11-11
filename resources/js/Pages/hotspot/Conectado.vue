<template>
  <v-container fluid fill-height>
    <v-row class="fill-height d-flex justify-center align-center">
      <v-col cols="12" class="text-center">
        <h3>Cliente reconectado, redirecionando...</h3>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default {
  setup() {
    const { props } = usePage();
    const redirectUrl = props.redirectUrl;
console.log('URL de redirecionamento:', redirectUrl);
    onMounted(() => {
      if (redirectUrl) {
        window.location.href = redirectUrl; // Redireciona para a URL especificada
      }
    });

    return {
      redirectUrl,
    };
  },
};
</script>

<style scoped>
.fill-height {
  height: 100vh;
}
.text-center {
  text-align: center;
  font-size: 1.5em;
}
</style>
