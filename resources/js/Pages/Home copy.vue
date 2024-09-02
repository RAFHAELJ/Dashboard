<template>
  <v-container>
    <div>
      <h1>Dashboard Home</h1>
      <div class="row">
        <div class="col-md-4">
          <chart-card :data="chartData1" :options="chartOptions"></chart-card>
        </div>
        <div class="col-md-4">
          <chart-card :data="chartData2" :options="chartOptions"></chart-card>
        </div>
        <div class="col-md-4">
          <chart-card :data="chartData3" :options="chartOptions"></chart-card>
        </div>
      </div>
    </div>
  </v-container>
</template>

<script>
import ChartCard from '../Components/ChartCard.vue';

export default {
  name: 'Home',
  components: {
    ChartCard
  },
  data() {
    return {
      chartData1: {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [
          {
            label: 'Data 1',
            backgroundColor: '#42A5F5',
            data: [40, 39, 10, 40, 39]
          }
        ]
      },
      chartData2: {
        labels: ['June', 'July', 'August', 'September', 'October'],
        datasets: [
          {
            label: 'Data 2',
            backgroundColor: '#66BB6A',
            data: [20, 39, 40, 35, 30]
          }
        ]
      },
      chartData3: {
        labels: ['November', 'December', 'January', 'February', 'March'],
        datasets: [
          {
            label: 'Data 3',
            backgroundColor: '#FFA726',
            data: [10, 29, 50, 20, 49]
          }
        ]
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false
      }
    };
  }
};
</script>

<style scoped>
.row {
  display: flex;
  justify-content: space-around;
  margin-top: 20px;
}
</style>
