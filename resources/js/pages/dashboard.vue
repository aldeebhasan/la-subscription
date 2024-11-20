<script setup>
import {onMounted, ref} from 'vue';
import {globals} from "@/app.js";
import SubscriptionChart from "@/components/SubscriptionChart.vue";
import SelectInput from "@/components/SelectInput.vue";

const loading = ref(true)
const statistics = ref({})

onMounted(() => load())

function load(year = null) {
  loading.value = true;
  globals.$http.get(Global.basePath + "/api/subscriptions/statistics", {params: {year: year}})
    .then(response => {
      statistics.value = response.data.data;
      loading.value = false;
    })
}

</script>

<template>

  <div class="container flex flex-wrap p-2">
    <div class="w-full text-center p-10">
      <h1 class="text-4xl font-extrabold  bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-300">
        La- Subscription
      </h1>
    </div>
    <div class="basis-1/2  p-1">
      <div class="dashboard-item">
        <div class="text-lg">Plans</div>
        <div>
          <span class="bg-gray-700 text-white p-1 px-4 rounded-sm"> {{ statistics.plans ?? "-" }}</span>
        </div>
      </div>
    </div>
    <div class="basis-1/2 p-1">
      <div class="dashboard-item">
        <div class="text-lg">Plugins</div>
        <div>
          <span class="bg-gray-700 text-white p-1 px-4 rounded-sm"> {{ statistics.plugins ?? "-" }}</span>
        </div>
      </div>
    </div>

    <div class="basis-1/2  p-1">
      <div class="dashboard-item">
        <div class="text-lg">Features</div>
        <div>
          <span class="bg-gray-700 text-white p-1 px-4 rounded-sm"> {{ statistics.features ?? "-" }}</span>
        </div>
      </div>
    </div>

    <div class="basis-1/2  p-1">
      <div class="dashboard-item">
        <div class="text-lg">Subscriptions</div>
        <div>
          <span class="bg-gray-700 text-white p-1 px-4 rounded-sm"> {{ statistics.subscriptions ?? "-" }}</span>
        </div>
      </div>
    </div>

    <div class="w-full  p-1 mt-10 flex flex-col items-end">
      <select @change="load($event.target.value)">
        <option :selected="i===0" :value=" (new Date().getFullYear()) + i" v-for="i in Array(3).keys()">
          {{ (new Date().getFullYear()) + i }}
        </option>
      </select>
      <subscription-chart :statistics="statistics.chartData || {}"/>
    </div>
  </div>

</template>

<style scoped>

.dashboard-item {
  @apply bg-gray-200 border-2 p-2 flex justify-between items-center;
}
</style>
