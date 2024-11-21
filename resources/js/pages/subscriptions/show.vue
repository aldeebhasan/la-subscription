<script setup>
import {onMounted, ref, reactive, getCurrentInstance} from "vue";
import {globals} from "@/app.js";
import {useRoute, useRouter} from "vue-router";
import Breadcrumb from "@/components/Breadcrumb.vue";
import Loader from "@/components/Loader.vue";
import VTable from "@/components/VTable.vue";

const route = useRoute();
const id = route.params.id;

const loading = ref(true);
const item = ref({});

onMounted(() => {
  loadItem()
});

function loadItem() {
  loading.value = true;
  globals.$http.get(Global.basePath + "/api/subscriptions/" + id)
    .then(response => {
      item.value = response.data.data;
      console.log(item);
      loading.value = false;
    });
}
</script>

<template>
  <breadcrumb title="Subscription Detail"/>

  <div class="max-w-full bg-white rounded-md shadow overflow-hidden relative min-h-96">
    <div class="absolute w-full h-full flex items-center justify-center z-10 bg-opacity-65 bg-gray-100" v-if="loading">
      <loader/>
    </div>

    <div class="flex flex-col p-8" v-if="!loading">
      <div class="flex flex-wrap w-full">
        <div class="attr-item">
          <label class="font-bold">Plan</label>
          <p>{{ item?.plan }}</p>
        </div>

        <div class="attr-item">
          <label class="font-bold">Subscriber</label>
          <p>{{ item?.subscriber }}</p>
        </div>

        <div class="attr-item">
          <label class="font-bold">Status</label>
          <p>{{ item?.status }}</p>
        </div>

        <div class="attr-item">
          <label class="font-bold">Start At</label>
          <p>{{ item?.start_at }}</p>
        </div>

        <div class="attr-item">
          <label class="font-bold">End At</label>
          <p>{{ item?.end_at }}</p>
        </div>

        <div class="attr-item">
          <label class="font-bold">Canceled At</label>
          <p>{{ item?.canceled_at }}</p>
        </div>

        <div class="attr-item">
          <label class="font-bold">Suppressed At</label>
          <p>{{ item?.suppressed_at }}</p>
        </div>
      </div>

      <hr/>
      <h1 class="my-2 font-bold">Contracts</h1>
      <hr/>
      <v-table :count="item.contracts?.length">
        <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Item</th>
          <th scope="col">Start At</th>
          <th scope="col">End At</th>
          <th scope="col">Auto Renew</th>
        </tr>
        </thead>
        <template v-for="contract in item.contracts">
          <tr>
            <td>C-{{ contract.number }}</td>
            <td>{{ contract.name }}</td>
            <td>{{ contract.start_at }}</td>
            <td>{{ contract.end_at }}</td>
            <td>{{ contract.auto_renew }}</td>
          </tr>
          <tr >
            <td class="bg-neutral-200"></td>
            <td colspan="4"  class="bg-neutral-200">
              <table class="min-w-full">
                <tr>
                  <th>Type</th>
                  <th>Start At</th>
                  <th>End At</th>
                  <th>Causative</th>
                </tr>
                <tr v-for="transaction in contract.transactions">
                  <td>{{ transaction.type }}</td>
                  <td>{{ transaction.start_at }}</td>
                  <td>{{ transaction.end_at }}</td>
                  <td>{{ transaction.causative }}</td>
                </tr>
              </table>
            </td>
          </tr>
        </template>
      </v-table>


    </div>

  </div>
</template>

<style scoped>
.attr-item {
  @apply basis-1/4 mb-5
}
</style>
