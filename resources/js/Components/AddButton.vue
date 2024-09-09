<!-- AddButton.vue -->
<script setup>
const props = defineProps({
  label: {
    type: String,
    default: 'Adicionar',
  },
  onClick: {
    type: Function,
    required: true,
  },
});
</script>

<template>
  <v-btn
    @click="onClick"
    density="comfortable"
    color="primary"
    class="add-button"
  >
    <v-icon start>mdi-plus</v-icon>
    {{ label }}
  </v-btn>
</template>

<style scoped>
.add-button {
  border-radius: 50px;
  text-transform: uppercase;
  width: 100%;
}

@media (min-width: 769px) {
  .add-button {
    width: auto;
  }
}
</style>
