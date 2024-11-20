<script setup>
import {Chart, registerables} from 'chart.js';
import {LineChart, useLineChart} from 'vue-chart-3';
import {computed, defineProps} from 'vue';

Chart.register(...registerables);

const props = defineProps({
  statistics: Object,
})


const chartData = computed(() => ({
  labels: Object.keys(props.statistics),
  datasets: [
    {
      label: 'Subscriptions',
      data: Object.values(props.statistics),
      borderColor: 'rgb(75, 192, 192)',
      fill: true,
    },
  ],
}));


const {lineChartProps, barChartRef} = useLineChart({
  chartData,
});

</script>

<template>
  <div id="chart" class="w-full ">
    <LineChart v-bind="lineChartProps"/>
  </div>
</template>

<style scoped>
#chart {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
