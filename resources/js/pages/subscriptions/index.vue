<script setup>
import {onMounted, ref, defineModel} from "vue";
import breadcrumb from "@/components/Breadcrumb.vue";
import {globals} from "@/app.js";
import VTable from "@/components/VTable.vue";
import Pagination from "@/components/Pagination.vue";
import Icon from "@/components/Icon.vue";

const data = ref({});
const loading = ref(true);
const keyword = defineModel("");

onMounted(() => load())

function load(link = "") {
  loading.value = true;
  globals.$http.get(link ? link : Global.basePath + "/api/subscriptions", {params: {filters: {q: keyword.value}}})
    .then(response => {
      response = response.data;
      data.value = response.data;
      loading.value = false;
    });
}
</script>

<template>

  <breadcrumb title="Plugins"/>

  <div class="flex p-2 mt-2">
    <div class="relative flex-grow ">
      <input v-model="keyword" @keyup.enter.native="load()" type="text" class="form-input border-0" placeholder="Search Here ..." autofocus/>
      <icon class="size-8 absolute  top-1 right-1 cursor-pointer hover:fill-gray-400 px-2" @click="load()" name="search"/>
    </div>
  </div>

  <v-table :loading="loading" :count="data.items?.length">
    <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Subscriber</th>
      <th scope="col">Plan</th>
      <th scope="col">Start At</th>
      <th scope="col">End At</th>
      <th scope="col">Status</th>
      <th scope="col">Actions</th>
    </tr>
    </thead>
    <tr v-for="item in data.items">
      <td>{{ item.id }}</td>
      <td>{{ item.subscriber }}</td>
      <td>{{ item.plan }}</td>
      <td>{{ item.start_at }}</td>
      <td>{{ item.end_at }}</td>
      <td>{{ item.status }}</td>
      <td><a class="action" :href="'subscriptions/'+item.id"> View</a></td>
    </tr>
  </v-table>
  <pagination :links="data.meta?.links ?? []" @navigate="load"></pagination>

</template>

<style scoped>


</style>
