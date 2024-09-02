<template>
  <div class="chart-card">
    <canvas ref="canvasRef"></canvas>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import { Chart, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend } from 'chart.js';

Chart.register(BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend);

export default defineComponent({
  name: 'ChartCard',
  props: {
    data: {
      type: Object,
      required: true,
    },
    options: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const canvasRef = ref(null);

    onMounted(() => {
      const canvas = canvasRef.value;
      if (canvas) {
        const ctx = canvas.getContext('2d');
        if (ctx) {
          new Chart(ctx, {
            type: 'bar',
            data: props.data,
            options: props.options,
          });
        } else {
          console.error('Failed to get 2D context');
        }
      } else {
        console.error('Canvas not found');
      }
    });

    return {
      canvasRef,
    };
  },
});
</script>

<style scoped>
.chart-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s;
  padding: 20px;
  position: relative;
  height: 300px; /* Ajuste a altura conforme necessário */
}
.chart-card:hover {
  transform: scale(1.05);
}
</style>
